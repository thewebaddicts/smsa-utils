<?php

namespace twa\smsautils\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\DB;


class SmsaLabelController extends Controller
{

    public function generatePdf($awb)
    {
        $awbModel = \twa\smsautils\Models\Awb::with(['sender', 'receiver', 'pickupRoute.hub', 'deliveryRoute.hub'])->where('awb', $awb)->firstOrFail();

        $logoPath = public_path('assets/images/smsa.png');

        // 2. Read the image file content.
        $logoData = File::get($logoPath);

        // 3. Encode the image data into Base64.
        $logoBase64 = base64_encode($logoData);

        // 4. Create the full data URI for the image source.
        $logoSrc = 'data:image/png;base64,' . $logoBase64;


        $awbNumber = $awbModel->awb;
        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $barcodeHorizontal = '<div style="width: 225px;height: 40px; text-align: center;">'
            . $dns1d->getBarcodeHTML($awbNumber, 'C128', 2, 42) .
            '<div style="font-size: 12px;margin-top: -3px;padding-left: 10px">' . $awbNumber . '</div>
            </div>';

        $barcodeVertical = '<div style="width: 50px;height: 100%; transform: rotate(270deg);">
            <div style="width: 344px; height: 100px; text-align: center;">

            ' . $dns1d->getBarcodeHTML($awbNumber, 'C128', 1.5, 35) . '
            <div style="width:fit-content; font-size: 12px;text-align: left;margin-left: 50px">' . $awbNumber . '</div>

            </div>
        </div>';

        $qrcode = '<div style="width: 45px; height: 45px; overflow: hidden;">' .
            $dns2d->getBarcodeHTML($awbNumber, 'QRCODE', 2, 2) .
            '</div>';

   

        $originReference = $awbModel->pickupRoute->hub->reference ?? '';
        $destinationReference = $awbModel->deliveryRoute->hub->reference ?? '';

    
        $data = [
            'logo' => $logoSrc,
            'page_count' => ($awbModel->awb_sequence ?? 1) . '/' . ($awbModel->nb_packages ?? 1),
            'date' => optional($awbModel->created_at)->format('M d, Y'),
            'order_reference' => $awbModel->shipment->reference1 ?? '',
            'tracking_number' => $awbModel->master_awb,
            'service_dom' => $awbModel->shipment->product_group ?? '',
            'service_cda' => $awbModel->shipment->product_code ?? '',
            'service_p' => $awbModel->shipment->payment_type ?? '',
            'weight' => $awbModel->declared_weight_g
                ? number_format($awbModel->declared_weight_g / 1000, 2) . ' KG'
                : '',
            'commodity' => $awbModel->commodity_type ?? '',
            'description' => $awbModel->description ?? '',
            'insurance' => $awbModel->shipment->insurance,
            // 'customs_sar' => $customsSar,
            // 'insurance_sar' => ($awbModel->insurance_amount ? $awbModel->insurance_amount : '') . ($awbModel->insurance_currency ? ' ' . $awbModel->insurance_currency : ''),
          
          'cod_sar' => ($awbModel->cod_amount !== null 
                ? number_format((float) $awbModel->cod_amount, 0, '.', '') 
                : ''
             ) . ($awbModel->cod_currency ? ' ' . $awbModel->cod_currency : ''), 'freight_payment_type'=> $awbModel->shipment->freight_payment_type ?? '',
            'freight_payment_method'=> $awbModel->shipment->freight_payment_method ?? '',
            'customs_payment_type'=> $awbModel->shipment->customs_payment_type ?? '',
            'customs_payment_method'=> $awbModel->shipment->customs_payment_method ?? '',

            
            
            'origin_city_code' => $originReference,
            'shipper_id' => $awbModel->sender->id ?? '',
            'shipper_name' => $awbModel->sender->company ?? '',
            'shipper_address_1' => $awbModel->sender->address1 ?? '',
            'shipper_address_2' => $awbModel->sender->address2 ?? '',
            'shipper_phone' => $awbModel->sender->phone ?? '',
            'destination_city_code' => $destinationReference,
            'recipient_name' => $awbModel->receiver->company ?? '',
            'recipient_address_1' => $awbModel->receiver->address1 ?? '',
            'recipient_address_2' => $awbModel->receiver->address2 ?? '',
            'recipient_district' => $awbModel->receiver->district ?? '',
            'recipient_phone' => $awbModel->receiver->phone ?? '',

            'barcode_horizontal' => $barcodeHorizontal,
            'barcode_vertical' => $barcodeVertical,
            'qrcode' => $qrcode,
            'master_awb' => $awbModel->master_awb ?? '',
        ];

        //        return view('pages.smsa_test', $data);
        $pdf = Pdf::loadView('pages.smsa_test', $data);
        $pdf->setPaper('a6', 'portrait');
        return $pdf->stream('smsa-awb-label.pdf');
    }

    public function viewLabel($awb)
    {
        $awbModel = \twa\smsautils\Models\Awb::with(['shipment.sender', 'shipment.receiver', 'pickupRoute.hub', 'deliveryRoute.hub'])->where('awb', $awb)->firstOrFail();

        // dd($awbModel);
        $logoPath = public_path('assets/images/smsa.png');
        $logoData = File::get($logoPath);
        $logoBase64 = base64_encode($logoData);
        $logoSrc = 'data:image/png;base64,' . $logoBase64;

        $awbNumber = $awbModel->awb;
        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $barcodeHorizontal = '<div style="width: 225px;height: 40px; text-align: center;">'
            . $dns1d->getBarcodeHTML($awbNumber, 'C128', 2.7, 42) .
            '<div style="font-size: 12px;margin-top: -3px;padding-left: 10px">' . $awbNumber . '</div>
            </div>';

        $barcodeVertical = '<div style="width: 50px;height: 100%; transform: rotate(270deg);">
            <div style="max-width:fit-content;width: 204px; height: 100px; text-align: center;">'
            . $dns1d->getBarcodeHTML($awbNumber, 'C128', 1, 30) .
            '<div style="display: flex
            ;
                justify-content: center;font-size: 12px;text-align: left;">' . $awbNumber . '</div>
            </div>
        </div>';

        $qrcode = '<div style="width: 45px; height: 45px; overflow: hidden;">' .
            $dns2d->getBarcodeHTML($awbNumber, 'QRCODE', 2, 2) .
            '</div>';

        // Get city codes for origin and destination
        $originCityCode = DB::table('cities')
            ->where('name', $awbModel->shipment->sender->city)
            ->value('code') ?? $awbModel->shipment->sender->city ?? '';

        $destinationCityCode = DB::table('cities')
            ->where('name', $awbModel->shipment->receiver->city)
            ->value('code') ?? $awbModel->shipment->receiver->city ?? '';

        // Calculate total customs duties from all items
        // dd($awbModel->items);
        $totalCustomsDuties = $awbModel->items->sum('custom_duties_value');
        $customsCurrency = $awbModel->items->first()?->custom_duties_unit ?? 'SAR';
        $customsSar = $totalCustomsDuties ? number_format($totalCustomsDuties, 2) . ' ' . $customsCurrency : '';

        // dd( $customsSar);
        $data = [
            'logo' => $logoSrc,
            'page_count' => ($awbModel->awb_sequence ?? 1) . '/' . ($awbModel->nb_packages ?? 1),
            'date' => optional($awbModel->created_at)->format('M d, Y'),
            'order_reference' => $awbModel->shipment->reference1 ?? '',
            'tracking_number' => $awbModel->awb,
            'service_dom' => $awbModel->shipment->product_group ?? '',
            'service_cda' => $awbModel->shipment->product_code ?? '',
            'service_p' => $awbModel->shipment->payment_type ?? '',
            'weight' => $awbModel->declared_weight_g
                ? number_format($awbModel->declared_weight_g / 1000, 2) . ' KG'
                : '',
            'commodity' => $awbModel->commodity_type ?? '',
            'description' => $awbModel->description ?? '',
            'customs_sar' => $customsSar,
          'insurance' => $awbModel->shipment->insurance,

          'cod_sar' => ($awbModel->cod_amount !== null 
                ? number_format((float) $awbModel->cod_amount, 0, '.', '') 
                : ''
             ) . ($awbModel->cod_currency ? ' ' . $awbModel->cod_currency : ''),
'origin_city_code' => $originCityCode,
            'shipper_id' => $awbModel->shipment->sender->id ?? '',
            'shipper_name' => $awbModel->shipment->sender->company ?? '',
            'shipper_address_1' => $awbModel->shipment->sender->address1 ?? '',
            'shipper_address_2' => $awbModel->shipment->sender->address2 ?? '',
            'shipper_phone' => $awbModel->shipment->sender->phone ?? '',
            'destination_city_code' => $destinationCityCode,
            'recipient_name' => $awbModel->shipment->receiver->company ?? '',
            'recipient_address_1' => $awbModel->shipment->receiver->address1 ?? '',
            'recipient_address_2' => $awbModel->shipment->receiver->address2 ?? '',
            'recipient_district' => $awbModel->shipment->receiver->district ?? '',
            'recipient_phone' => $awbModel->shipment->receiver->phone ?? '',
            'barcode_horizontal' => $barcodeHorizontal,
            'barcode_vertical' => $barcodeVertical,
            'qrcode' => $qrcode,
            'master_awb' => $awbModel->master_awb ?? '',
        ];

        return view('pages.smsa_test', $data);
    }
}