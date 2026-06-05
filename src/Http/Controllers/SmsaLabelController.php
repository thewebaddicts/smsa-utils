<?php

namespace twa\smsautils\Http\Controllers;

use Illuminate\Http\Request;
use twa\smsautils\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\DB;
use Arphp\Glyphs;
use twa\smsautils\Models\Awb as AwbModel;
use Spatie\Browsershot\Browsershot;

class SmsaLabelController extends Controller
{


    private function money($value): string
    {
        return number_format((float) ($value ?? 0), 2);
    }

    /**
     * Check if a string contains Arabic characters
     */
    private function hasArabic($string)
    {
        if (empty($string)) {
            return false;
        }
        return preg_match('/[\x{0600}-\x{06FF}]/u', $string);
    }

    /**
     * Format text for proper display in PDF (handles Arabic, English, and mixed content)
     */
    private function formatText($text)
    {
        if (empty($text)) {
            return $text;
        }

        // Always apply Arabic processing if Arabic characters are present
        // This handles pure Arabic, mixed Arabic/English, and pure English correctly
        if ($this->hasArabic($text)) {
            $arabic = new Glyphs();

            // Use Glyphs to properly shape Arabic text
            // This will handle mixed content correctly by only processing Arabic parts
            $text = $arabic->utf8Glyphs($text);
        }

        return $text;
    }

    /**
     * Truncate text with proper handling for Arabic, English, and mixed content
     */
    private function truncateText($text, $maxWords = 7)
    {
        if (empty($text)) {
            return $text;
        }

        // Use character limit for all types of text for consistency
        $maxChars = $maxWords * 7; // Approximate 7 characters per word

        if (mb_strlen($text) > $maxChars) {
            // Truncate at character limit
            $truncated = mb_substr($text, 0, $maxChars);

            // Try to break at word boundary (works for both Arabic and English)
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > ($maxChars * 0.7)) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }

            return $truncated . '...';
        }

        return $text;
    }

    /**
     * Process text for display (handles Arabic formatting and truncation)
     * Works for: Pure Arabic, Pure English, Mixed Arabic/English
     */
    private function processTextForDisplay($text, $maxWords = 4)
    {
        if (empty($text)) {
            return $text;
        }

        // First truncate if needed (before Arabic processing to get correct length)
        $truncatedText = $this->truncateText($text, $maxWords);

        // Then apply Arabic formatting if text contains Arabic
        // This handles mixed content properly
        return $this->formatText($truncatedText);
    }

    private function resolveAddressFor(array $snapshot, $address, ?int $addressId): ?string
    {
        if (!empty($snapshot['address_for'])) {
            return (string) $snapshot['address_for'];
        }

        if (!empty($address?->address_for)) {
            return (string) $address->address_for;
        }

        $id = $addressId ?? ($snapshot['id'] ?? null);
        if ($id) {
            $fromDb = DB::table('addresses')->where('id', $id)->value('address_for');
            if ($fromDb) {
                return (string) $fromDb;
            }
        }

        return null;
    }

    private function isHubAddress(?string $addressFor): bool
    {
        return strtoupper(trim((string) $addressFor)) === 'HUB';
    }

    public function buildLabelDataFromModel(AwbModel $awbModel): array
    {
        $logoPath = public_path('assets/images/logo.png');

        // 2. Read the image file content.
        $logoData = File::get($logoPath);

        // 3. Encode the image data into Base64.
        $logoBase64 = base64_encode($logoData);

        // 4. Create the full data URI for the image source.
        $logoSrc = 'data:image/png;base64,' . $logoBase64;


        $awbNumber = $awbModel->awb;
        $formattedAwbNumber = trim(chunk_split($awbNumber, 4, ' '));

        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $barcodeHorizontal = '<div style="width: 225px;height: 40px; text-align: center;">'
            . $dns1d->getBarcodeHTML($awbNumber, 'C128', 2.5, 42) .
            '<div style="font-size: 12px;margin-top: -3px;padding-top:4px;padding-left: 10px">' . $formattedAwbNumber . '</div>
                    </div>';


        $barcodeVertical = '<div style="width: 50px;height: 100%; transform: rotate(270deg);">
                    <div style="width: 344px; height: 100px; text-align: center;">
                        ' . $dns1d->getBarcodeHTML($awbNumber, 'C128', 2, 35) . '
                        <div style="width:fit-content; font-size: 14px;text-align: left;margin-left: 60px">' . $formattedAwbNumber . '</div>
                    </div>
                </div>';




        $qrcode = '<div style="width: 45px; height: 45px; overflow: hidden;">' .
            $dns2d->getBarcodeHTML($awbNumber, 'QRCODE', 2, 2) .
            '</div>';



        $originReference = $awbModel->pickupRoute->hub->reference ?? '';
        $destinationReference = $awbModel->deliveryRoute->hub->reference ?? '';
        $destinationRoute = $awbModel->deliveryRoute->label ?? '-';

        // Get declared amount and currency
        $declaredAmount = ($awbModel->declared_amount !== null && $awbModel->declared_amount > 0)
            ? number_format($awbModel->declared_amount, 2) . ($awbModel->declared_amount_currency ? ' ' . $awbModel->declared_amount_currency : '')
            : '';

           
        $shipment = $awbModel->shipment;
       
        $client = $shipment->client;
        $customer = $shipment->customer;
        $shipperSnapshot = (array) ($shipment->shipper_snapshot ?? []);
        $consigneeSnapshot = (array) ($shipment->consignee_snapshot ?? []);
        $senderSnapshot = (array) ($shipment->sender_address_snapshot ?? []);
        $receiverSnapshot = (array) ($shipment->receiver_address_snapshot ?? []);
        $weight = $awbModel->actual_weight_g ?: $awbModel->declared_weight_g;

        $senderAddressFor = $this->resolveAddressFor(
            $senderSnapshot,
            $awbModel->sender,
            $awbModel->sender_address_id ?? $shipment->sender_address_id ?? null
        );
        $isSenderHubAddress = $this->isHubAddress($senderAddressFor);
        $shipperPhone = $isSenderHubAddress
            ? ($shipperSnapshot['phone'] ?? $client?->phone ?? '')
            : ($senderSnapshot['phone'] ?? $awbModel->sender?->phone ?? '');

        $receiverAddressFor = $this->resolveAddressFor(
            $receiverSnapshot,
            $awbModel->receiver,
            $awbModel->receiver_address_id ?? $shipment->receiver_address_id ?? null
        );
        $isReceiverHubAddress = $this->isHubAddress($receiverAddressFor);
        $recipientPhone = $isReceiverHubAddress
            ? ($consigneeSnapshot['phone'] ?? $customer?->phone ?? '')
            : ($receiverSnapshot['phone'] ?? $awbModel->receiver?->phone ?? '');

        $consigneeName = $consigneeSnapshot['name'] ?? $customer?->name ?? '';
        $recipientAttention = $isReceiverHubAddress
            ? $consigneeName
            : ($receiverSnapshot['attention'] ?? $awbModel->receiver?->attention ?? '');

        return [
            'logo' => $logoSrc,
            'page_count' => ($awbModel->awb_sequence ?? 1) . '/' . ($awbModel->nb_packages ?? 1),
            'date' => optional($awbModel->created_at)->format('M d, Y'),
            'order_reference' => $this->processTextForDisplay($awbModel->package_reference ?? ''),
            'tracking_number' => $formattedAwbNumber,
            'service_dom' => $awbModel->shipment->product_group ?? '',
            'service_cda' => $awbModel->shipment->product_code ?? '',
            'service_p' => $awbModel->shipment->payment_type ?? '',
            'is_reexport' => !is_null($awbModel->reexport_at),
            'weight' => $weight ? ($weight / 1000) . ' KG' : '',


            'commodity' => $this->processTextForDisplay($awbModel->commodity_type ?? '', 4),
            'description' => $this->processTextForDisplay($awbModel->package_description ?? '', 3),
            'insurance' => $awbModel->shipment->insurance,
            // 'customs_sar' => $customsSar,
            // 'insurance_sar' => ($awbModel->insurance_amount ? $awbModel->insurance_amount : '') . ($awbModel->insurance_currency ? ' ' . $awbModel->insurance_currency : ''),
            // 'insurance_sar' => ($awbModel->insurance_amount ? $awbModel->insurance_amount : '') . ($awbModel->insurance_currency ? ' ' . $awbModel->insurance_currency : ''),
            'cod_sar' => ($awbModel->cod_amount ?? 0) . ($awbModel->cod_currency ? ' ' . $awbModel->cod_currency : ''),
            'declared_amount' => $declaredAmount,

            'freight_payment_type' => $awbModel->shipment->freight_payment_type ?? '',
            'freight_payment_method' => $awbModel->shipment->freight_payment_method ?? '',
            'customs_payment_type' => $awbModel->shipment->customs_payment_type ?? '',
            'customs_payment_method' => $awbModel->shipment->customs_payment_method ?? '',



            //            'origin_city_code' => $originReference,
            'origin_city_code' => strtoupper($awbModel->origin_code) ?? '',
            'shipper_id' => $shipperSnapshot['id'] ?? $client->id ?? '',
            'shipper_number' => $shipperSnapshot['account_number'] ?? $client->account_number ?? '',

            'shipper_name' => $this->processTextForDisplay($shipperSnapshot['name'] ?? '', 6),

            'shipper_address_1' => $this->processTextForDisplay($senderSnapshot['address1'] ?? $awbModel->sender?->address1 ?? '', 8),
            'shipper_address_2' => $this->processTextForDisplay($senderSnapshot['address2'] ?? $awbModel->sender?->address2 ?? '', 8),
            'shipper_attention' => $this->processTextForDisplay($senderSnapshot['attention'] ?? $awbModel->sender?->attention ?? '', 8),
            'shipper_area_code' => $senderSnapshot['area_code'] ?? $awbModel->sender?->area_code ?? null,
            'shipper_district' => $this->processTextForDisplay(
                implode(', ', array_filter([
                    $senderSnapshot['country'] ?? $awbModel->sender?->country ?? null,
                    $senderSnapshot['province'] ?? $awbModel->sender?->province ?? null,
                    $senderSnapshot['city'] ?? $awbModel->sender?->city ?? null,
                ])),
                10 // max words
            ),

            'shipper_phone' => $shipperPhone,
            'destination_city_code' => strtoupper($awbModel->destination_code) ?? '',
            //            'destination_city_code' => $destinationReference,
            'destination_route' => $destinationRoute,

            'recipient_name' => $this->processTextForDisplay($consigneeSnapshot['name'] ?? '', 6),

            'recipient_address_1' => $this->processTextForDisplay($receiverSnapshot['address1'] ?? $awbModel->receiver?->address1 ?? '', 7),
            'recipient_address_2' => $this->processTextForDisplay($receiverSnapshot['address2'] ?? $awbModel->receiver?->address2 ?? '', 7),
            'recipient_attention' => $this->processTextForDisplay($recipientAttention, 7),
            'recipient_company' => $this->processTextForDisplay($receiverSnapshot['company'] ?? $awbModel->receiver?->company ?? '', 7),
            'recipient_area_code' => $receiverSnapshot['area_code'] ?? $awbModel->receiver?->area_code ?? null,
            'recipient_district' => $this->processTextForDisplay(
                implode(', ', array_filter([
                    $receiverSnapshot['country'] ?? $awbModel->receiver?->country ?? null,
                    $receiverSnapshot['province'] ?? $awbModel->receiver?->province ?? null,
                    $receiverSnapshot['city'] ?? $awbModel->receiver?->city ?? null,
                ])),
                10
            ),

            'recipient_phone' => $recipientPhone,

            'barcode_horizontal' => $barcodeHorizontal,
            'barcode_vertical' => $barcodeVertical,
            'qrcode' => $qrcode,
            'master_awb' => $awbModel->master_awb ? trim(chunk_split($awbModel->master_awb, 4, ' ')) : '',

        ];
    }

    public function generatePdf($awb, $token)
    {
        if (!$awb || !$token) {
            return view('smsautils::awb.not_found');
        }

        $check = generate_awb_token($awb);

        if ($check != $token) {
            return view('smsautils::awb.not_found');
        }

        $awbModel = AwbModel::with(['sender', 'receiver', 'pickupRoute.hub', 'deliveryRoute.hub', 'shipment.client', 'shipment.customer'])
            ->where('awb', $awb)
            ->firstOrFail();

        $data = $this->buildLabelDataFromModel($awbModel);

        //        return view('pages.smsa_test', $data);
        $pdf = Pdf::loadView('smsautils::pages.smsa_test', $data);
        $pdf->setPaper('a6', 'portrait');
        return $pdf->stream($awbModel->awb . '.pdf');
    }

    public function viewLabel($awb)
    {

        // if (!$awb || !$token) {
        //     return view('awb.not_found');
        // }

        // $check = generate_awb_token($awb);

        // if ($check != $token) {
        //     return view('awb.not_found');
        // }

        $awbModel = \twa\smsautils\Models\Awb::with(['sender', 'receiver', 'pickupRoute.hub', 'deliveryRoute.hub'])->where('awb', $awb)->firstOrFail();

        $logoPath = public_path('assets/images/logo.png');

        // 2. Read the image file content.
        $logoData = File::get($logoPath);

        // 3. Encode the image data into Base64.
        $logoBase64 = base64_encode($logoData);

        // 4. Create the full data URI for the image source.
        $logoSrc = 'data:image/png;base64,' . $logoBase64;


        $awbNumber = $awbModel->awb;
        $formattedAwbNumber = trim(chunk_split($awbNumber, 4, ' '));

        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $barcodeHorizontal = '<div style="width: 225px;height: 40px; text-align: center;">'
            . $dns1d->getBarcodeHTML($awbNumber, 'C128', 2, 42) .
            '<div style="font-size: 12px;margin-top: -3px;padding-left: 10px">' . $formattedAwbNumber . '</div>
                </div>';


        $barcodeVertical = '<div style="width: 50px;height: 100%; transform: rotate(270deg);">
                <div style="width: 344px; height: 100px; text-align: center;">
                    ' . $dns1d->getBarcodeHTML($awbNumber, 'C128', 1.5, 35) . '
                    <div style="width:fit-content; font-size: 12px;text-align: left;margin-left: 35px">' . $formattedAwbNumber . '</div>
                </div>
            </div>';


        $qrcode = '<div style="width: 45px; height: 45px; overflow: hidden;">' .
            $dns2d->getBarcodeHTML($awbNumber, 'QRCODE', 2, 2) .
            '</div>';

        //        dd($barcodeVertical);

        // Get city codes for origin and destination
        // $originCityCode = DB::table('cities')
        //     ->where('name', $awbModel->shipment->sender->city)
        //     ->value('code') ?? $awbModel->shipment->sender->city ?? '';

        // $destinationCityCode = DB::table('cities')
        //     ->where('name', $awbModel->shipment->receiver->city)
        //     ->value('code') ?? $awbModel->shipment->receiver->city ?? '';



        $originReference = $awbModel->pickupRoute->hub->reference ?? '';
        $destinationReference = $awbModel->deliveryRoute->hub->reference ?? '';
        $destinationRoute = $awbModel->deliveryRoute->label ?? '-';

        // Get declared amount and currency
        $declaredAmount = ($awbModel->declared_amount !== null && $awbModel->declared_amount > 0)
            ? number_format($awbModel->declared_amount, 2) . ($awbModel->declared_amount_currency ? ' ' . $awbModel->declared_amount_currency : '')
            : '';

        $data = [
            'logo' => $logoSrc,
            'page_count' => ($awbModel->awb_sequence ?? 1) . '/' . ($awbModel->nb_packages ?? 1),
            'date' => optional($awbModel->created_at)->format('M d, Y'),
            'order_reference' => $awbModel->package_reference ?? '',
            'tracking_number' => $awbModel->master_awb,
            'service_dom' => $awbModel->shipment->product_group ?? '',
            'service_cda' => $awbModel->shipment->product_code ?? '',
            'service_p' => $awbModel->shipment->payment_type ?? '',
            'weight' => $awbModel->declared_weight_g
                ? number_format($awbModel->declared_weight_g / 1000, 2) . ' KG'
                : '',
            'commodity' => $this->processTextForDisplay($awbModel->commodity_type ?? '', 4),
            'description' => $this->processTextForDisplay($awbModel->package_description ?? '', 4),
            'insurance' => $awbModel->shipment->insurance,
            // 'customs_sar' => $customsSar,
            // 'insurance_sar' => ($awbModel->insurance_amount ? $awbModel->insurance_amount : '') . ($awbModel->insurance_currency ? ' ' . $awbModel->insurance_currency : ''),

            // 'insurance_sar' => ($awbModel->insurance_amount ? $awbModel->insurance_amount : '') . ($awbModel->insurance_currency ? ' ' . $awbModel->insurance_currency : ''),
            'cod_sar' => ($awbModel->cod_amount ?? 0) . ($awbModel->cod_currency ? ' ' . $awbModel->cod_currency : ''),
            'declared_amount' => $declaredAmount,

            'freight_payment_type' => $awbModel->shipment->freight_payment_type ?? '',
            'freight_payment_method' => $awbModel->shipment->freight_payment_method ?? '',
            'customs_payment_type' => $awbModel->shipment->customs_payment_type ?? '',
            'customs_payment_method' => $awbModel->shipment->customs_payment_method ?? '',



            //            'origin_city_code' => $originReference,
            'origin_city_code' => strtoupper($awbModel->origin_code) ?? '',
            'shipper_id' => $awbModel->sender->id ?? '',
            'shipper_number' => $awbModel->sender->account_number ?? '',
            'shipper_name' => $this->formatText($awbModel->sender->name ?? '----'),

            'shipper_address_1' => $this->formatText($awbModel->sender->address1 ?? ''),
            'shipper_address_2' => $this->formatText($awbModel->sender->address2 ?? ''),
            'shipper_district' => $this->formatText($awbModel->sender->district ?? ''),
            'shipper_phone' => $awbModel->sender->phone ?? '',
            'destination_city_code' => strtoupper($awbModel->destination_code) ?? '',
            //            'destination_city_code' => $destinationReference,
            'destination_route' => $destinationRoute,
            'recipient_name' => $this->formatText($awbModel->receiver->company ?? ''),
            'recipient_address_1' => $this->formatText($awbModel->receiver->address1 ?? ''),
            'recipient_address_2' => $this->formatText($awbModel->receiver->address2 ?? ''),
            'recipient_district' => $this->formatText($awbModel->receiver->district ?? ''),
            'recipient_phone' => $awbModel->receiver->phone ?? '',

            'barcode_horizontal' => $barcodeHorizontal,
            'barcode_vertical' => $barcodeVertical,
            'qrcode' => $qrcode,
            'master_awb' => $awbModel->master_awb ? trim(chunk_split($awbModel->master_awb, 4, ' ')) : '',

        ];



        return view('smsautils::pages.smsa_test', $data);
    }

    public function generateInvoice($awb, $token)
    {

        if (!$awb || !$token) {
            return view('awb.not_found');
        }

        $check = generate_awb_token($awb);

        if ($check != $token) {
            return view('awb.not_found');
        }



        $awbModel = AwbModel::with(['sender', 'receiver', 'shipment.client', 'shipment.customer'])
            ->where('master_awb', $awb)
            ->whereNull('deleted_at')
            ->firstOrFail();


        $hasCustomsLog = DB::table('customs_import_logs')
            ->whereNull('deleted_at')
            ->where('sawb', $awbModel->awb)
            ->exists();




        if (!$hasCustomsLog) {
            return view('awb.not_found');
        }

        $invoiceData = $this->buildInvoiceDataFromModel($awbModel, false);

        $mpdf = new \Mpdf\Mpdf();

        $html = view('smsautils::pages.smsa_invoice', $this->buildInvoiceDataFromModel($awbModel, false));

        $mpdf->WriteHTML($html);
        $mpdf->Output();




        // return $pdf->stream('invoice-' . $awbModel->awb . '.pdf');
    }

    public function viewInvoice(Request $request)
    {



        $awb = $request->query('awb');

        $awbModel = AwbModel::with(['sender', 'receiver', 'shipment.client', 'shipment.customer'])
            ->whereNull('deleted_at')
            ->when($awb, fn($q) => $q->where('awb', $awb))
            ->first();

        if (!$awbModel) {
            return view('smsautils::awb.not_found');
        }

        return view('smsautils::pages.smsa_invoice', $this->buildInvoiceDataFromModel($awbModel, false));
    }

    private function buildInvoiceDataFromModel(AwbModel $awbModel, bool $useArabicGlyphs = true): array
    {

        $logoPath = public_path('assets/images/logo.png');
        $logoData = File::get($logoPath);
        $logoBase64 = base64_encode($logoData);
        $logoSrc = 'data:image/png;base64,' . $logoBase64;

        $awbNumber = $awbModel->awb;
        $latestLog = null;

        $latestLog = DB::table('customs_import_logs')
            ->whereNull('deleted_at')
            ->where('sawb', $awbNumber)
            ->orderBy('id', 'desc')
            ->first();

        // $customsDuties = (float) (($latestLog->custom_duty ?? 0) + ($latestLog->fuel_surcharge ?? 0) + ($latestLog->airline_surcharge ?? 0) + ($latestLog->customs_surcharge ?? 0) + ($latestLog->special_documents_surcharge ?? 0) + ($latestLog->seal_charge ?? 0) + ($latestLog->other_charges ?? 0));

        $customsDuties = (float) ($latestLog->custom_duty ?? 0); //customs
        $vatOnBayan = (float) ($latestLog->vat_amount ?? 0);
        $customServiceFee = (float) ($latestLog->custom_service_fees ?? 0); //customs
        $vatOnCustomServiceFee = (float) ($latestLog->vat_custom_service_fees ?? 0);
        $adminFee = (float) ($latestLog->duty_amin_fees ?? 0); //customs
        $vatOnAdminFee = (float) ($latestLog->vat_duty_amin_fees ?? 0);



        $fuelSurcharge = (float) ($latestLog->fuel_surcharge ?? 0); // customs
        $vatOnFuelSurcharge = (float) ($latestLog->vat_fuel_surcharge ?? 0);
        $airlineSurcharge = (float) ($latestLog->airline_surcharge ?? 0); // customs
        $vatOnAirlineSurcharge = (float) ($latestLog->vat_airline_surcharge ?? 0);
        $customsSurcharge = (float) ($latestLog->customs_surcharge ?? 0); // customs
        $vatOnCustomsSurcharge = (float) ($latestLog->vat_customs_surcharge ?? 0);
        $specialDocumentsSurcharge = (float) ($latestLog->special_documents_surcharge ?? 0); // customs
        $vatOnSpecialDocumentsSurcharge = (float) ($latestLog->vat_special_documents_surcharge ?? 0);
        $sealCharge = (float) ($latestLog->seal_charge ?? 0); // customs
        $vatOnSealCharge = (float) ($latestLog->vat_seal_charge ?? 0);
        $otherCharges = (float) ($latestLog->other_charges ?? 0); // customs
        $vatOnOtherCharges = (float) ($latestLog->vat_other_charges ?? 0);



        $sum_otherCharges = (float) ($fuelSurcharge ?? 0) + (float) ($vatOnFuelSurcharge ?? 0) + (float) ($airlineSurcharge ?? 0) + (float) ($vatOnAirlineSurcharge ?? 0) + (float) ($customsSurcharge ?? 0) + (float) ($vatOnCustomsSurcharge ?? 0) + (float) ($specialDocumentsSurcharge ?? 0) + (float) ($vatOnSpecialDocumentsSurcharge ?? 0) + (float) ($sealCharge ?? 0) + (float) ($vatOnSealCharge ?? 0) + (float) ($otherCharges ?? 0) + (float) ($vatOnOtherCharges ?? 0);



        $sum_total_custom_amount =  $customsDuties + $customServiceFee + $adminFee + $fuelSurcharge + $airlineSurcharge + $customsSurcharge + $specialDocumentsSurcharge + $sealCharge + $otherCharges;
        $sum_total_vat_amount = $vatOnBayan + $vatOnAdminFee + $vatOnCustomServiceFee  + $vatOnFuelSurcharge + $vatOnAirlineSurcharge + $vatOnCustomsSurcharge + $vatOnSpecialDocumentsSurcharge + $vatOnSealCharge + $vatOnOtherCharges;

        $totalCustomsAmount = (float) ($sum_total_custom_amount);
        $totalVatAmount = (float) ($sum_total_vat_amount);




        $dns2d = new DNS2D();
        $qrPayload = implode('|', [
            'INV:' . ('VAT' . ($latestLog->sawb ?? $awbModel->awb)),
            'AWB:' . $awbModel->awb,
            'DATE:' . optional($latestLog?->sent_at ?? $awbModel->updated_at)->format('Y-m-d'),
        ]);
        $qrcode = '<div style="width:72px;height:72px;overflow:hidden;">' . $dns2d->getBarcodeHTML($qrPayload, 'QRCODE', 2, 2) . '</div>';

        $weight = $awbModel->actual_weight_g ?: $awbModel->declared_weight_g;

        $shape = function (?string $text) use ($useArabicGlyphs): string {
            $text = (string) ($text ?? '');
            return $useArabicGlyphs ? $this->formatText($text) : $text;
        };


        $destinationCountry = DB::table('countries')->where('code', $awbModel->destination_country)->whereNull('deleted_at')->first();




        if (!$destinationCountry) {
            $destinationCountry = DB::table('country_airports')
                ->join('countries', 'country_airports.country', '=', 'countries.code')
                ->where('country_airports.aiata_code', $awbModel->destination_code)
                ->whereNull('country_airports.deleted_at')
                ->first();
        }
        // dd($destinationCountry);
        // $country = $destinationCountry->code;
        $tax_percentage = ($destinationCountry->tax_percentage . '%');
        $tax_registration_number = $destinationCountry->tax_registration_number;
        $currency = $destinationCountry->currency_code;

        $originCountry = DB::table('countries')->where('code', $awbModel->origin_country)->whereNull('deleted_at')->first();
        if (!$originCountry) {
            $originCountry = DB::table('country_airports')
                ->join('countries', 'country_airports.country', '=', 'countries.code')
                ->where('country_airports.aiata_code', $awbModel->origin_code)
                ->whereNull('country_airports.deleted_at')
                ->first();
        }

        return [
            'logo' => $logoSrc,
            'qrcode' => $qrcode,
            'ar_company_line' => $shape((string) config('invoice.ar_company_line', '')),
            'ar_tax_reg_line' => $shape(trim(
             (string) $tax_registration_number.  ' ' . (string) config('invoice.ar_tax_reg_prefix', '') 
            )),
            'ar_title_line' => $shape((string) config('invoice.ar_title_line', '')),
            'ar_labels' => [
                'invoice_date' => $shape((string) config('invoice.labels.invoice_date', '')),
                'origin' => $shape((string) config('invoice.labels.origin', '')),
                'destination' => $shape((string) config('invoice.labels.destination', '')),
                'weight' => $shape((string) config('invoice.labels.weight', '')),
                'tax_invoice_number' => $shape((string) config('invoice.labels.tax_invoice_number', '')),
                'name' => $shape((string) config('invoice.labels.name', '')),
                'contact_number' => $shape((string) config('invoice.labels.contact_number', '')),
            ],
            'ar_table_labels' => [
                'shipment_value_local_currency' => $shape((string) config('invoice.table_labels.shipment_value_local_currency', '')),
                'amount' => $shape((string) config('invoice.table_labels.amount', '')),
                'description' => $shape((string) config('invoice.table_labels.description', '')),
            ],
            'ar_charge_labels' => [
                'customs_duties' => $shape((string) config('invoice.charge_labels.customs_duties')),
                'vat_on_bayan' => $shape((string) str_replace('x%', $tax_percentage, config('invoice.charge_labels.vat_on_bayan', ''))),
                'custom_service_fee' => $shape((string) config('invoice.charge_labels.custom_service_fee', '')),
                'vat_on_custom_service_fee' => $shape((string) str_replace('x%', $tax_percentage, config('invoice.charge_labels.vat_on_custom_service_fee', ''))),
                'admin_fee' => $shape((string) config('invoice.charge_labels.admin_fee', '')),
                'vat_on_admin_fee' => $shape((string) str_replace('x%', $tax_percentage, config('invoice.charge_labels.vat_on_admin_fee', ''))),
                'other_charges' => $shape((string) config('invoice.charge_labels.other_charges', '')),
                'total_vat_amount' => $shape((string) config('invoice.charge_labels.total_vat_amount', '')),
                'total_customs_amount' => $shape((string) config('invoice.charge_labels.total_customs_amount', '')),
            ],
            'ar_footer' => [
                'contact_notice' => $shape((string) config('invoice.footer.ar_contact_notice', '')),
                'computer_generated' => $shape((string) config('invoice.footer.ar_computer_generated', '')),
            ],





            'invoice_number' => 'VAT' . ($latestLog->sawb ?? $awbModel->awb),
            'invoice_date' => !empty($latestLog?->created_at)
                ? now()->parse($latestLog->created_at)->format('Y/m/d')
                : '-',
            'name' => $shape($awbModel->shipment?->customer?->name ?? $awbModel->receiver?->company ?? $awbModel->receiver?->attention ?? '-'),
            'origin' => $originCountry->code ?? '-',
            'contact_number' => $awbModel->receiver?->phone ?? '-',
            'destination' => $destinationCountry->code ?? '-',
            'weight' => $weight ? number_format($weight / 1000, 2) . ' KG' : '-',
            'shipment_number' => $awbModel->awb,
            'bayan_number' => 'VAT' . $awbModel->awb,
            'sadad_number' => $latestLog->cd_status ?? '-',
            'shipment_value' => $this->money($awbModel->declared_amount),
            'currency' => $currency,



            'customs_duties' => $this->money($customsDuties),
            'vat_on_bayan' => $this->money($vatOnBayan),
            'custom_service_fee' => $this->money($customServiceFee),
            'vat_on_custom_service_fee' => $this->money($vatOnCustomServiceFee),
            'admin_fee' => $this->money($adminFee),
            'vat_on_admin_fee' => $this->money($vatOnAdminFee),
            'other_charges' => $this->money($sum_otherCharges),
            'total_customs_amount' => $this->money($totalCustomsAmount),
            'total_vat_amount' => $this->money($totalVatAmount),
        ];
    }
}
