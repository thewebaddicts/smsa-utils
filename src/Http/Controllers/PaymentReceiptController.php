<?php

namespace twa\smsautils\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

/**
 * Builds the simplified Arabic tax invoice PDF for a POS transaction.
 *
 * Data flow:
 *   transaction_inventories  -> id  (route param)
 *     ├─ pos_receipts (via pos_receipt_id)            => invoice_number
 *     ├─ hubs        (via hub_id)                     => branch label/reference + supplier address
 *     │    └─ addresses (via hubs.address_id)         => supplier street line
 *     └─ transactions (via transaction_id)
 *          └─ awbs   (via transactions.awb)           => nb_packages
 *               └─ shipments (via awbs.shipment_id)   => customer name + service_type_reference
 *
 * Anything tagged "HARDCODED" below is a placeholder until business rules are wired up.
 */
class PaymentReceiptController extends Controller
{
    private const SUPPLIER_NAME_AR = 'شركة سمسا للنقل السريع المحدودة';
    private const LOGO_RELATIVE_PATH = '/../../Resources/images/logo.png';

    public function show(Request $request, $transaction_inventory_id)
    {
        $payload = $this->buildInvoicePayload($transaction_inventory_id);

        if ($payload === null) {
            abort(404, 'Transaction inventory not found');
        }

        return view('smsautils::receipt.payment-receipt', $payload);
    }

    private function buildInvoicePayload($transactionInventoryId): ?array
    {
        $ti = DB::table('transaction_inventories')
            ->whereNull('deleted_at')
            ->where('id', $transactionInventoryId)
            ->first();

        if (!$ti) {
            return null;
        }

        // ------------ Supplier block (top: المورد) ------------
        $hub = null;
        $hubAddress = null;
        if (!empty($ti->hub_id)) {
            $hub = DB::table('hubs')->whereNull('deleted_at')->where('id', $ti->hub_id)->first();

            $timezone = trim($hub->timezone ?? 'UTC');
            if ($hub && !empty($hub->address_id)) {
                $hubAddress = DB::table('addresses')->whereNull('deleted_at')->where('id', $hub->address_id)->first();
            }
        }

        $supplier = $this->buildSupplierBlock($hubAddress);

        // ------------ Invoice number (رقم الفاتورة) ------------
        $invoiceNumber = '';
        if (!empty($ti->pos_receipt_id)) {
            $invoiceNumber = (string) DB::table('pos_receipts')
                ->whereNull('deleted_at')
                ->where('id', $ti->pos_receipt_id)
                ->value('invoice_number');
        }

        // ------------ Issue date (تاريخ الإصدار) ------------
        $issueDate = $ti->created_at
            ? format_date_time_with_timezone($ti->created_at, $timezone)
            : '';

        // ------------ Seller branch (فرع البائع) ------------
        $sellerBranch = '';
        if ($hub) {
            $sellerBranch = trim(
                trim((string) ($hub->identifier ?? ''))
                . (!empty($hub->identifier) && !empty($hub->label) ? ' - ' : '')
                . trim((string) ($hub->label ?? ''))
            );
        }

        // ------------ Number of packages (إجمالي عدد القطع) ------------
        $nbPackages = 0;
        $shipment = null;
        if (!empty($ti->transaction_id)) {
            $transaction = DB::table('transactions')
                ->whereNull('deleted_at')
                ->where('id', $ti->transaction_id)
                ->first();

            if ($transaction && !empty($transaction->awb)) {
                $awb = DB::table('awbs')
                    ->whereNull('deleted_at')
                    ->where('awb', $transaction->awb)
                    ->first();

                if (!$awb) {
                    $awb = DB::table('awbs')
                        ->whereNull('deleted_at')
                        ->where('master_awb', $transaction->awb)
                        ->first();
                }

                if ($awb) {
                    $nbPackages = (int) ($awb->nb_packages ?? 0);

                    if (!empty($awb->shipment_id)) {
                        $shipment = DB::table('shipments')
                            ->whereNull('deleted_at')
                            ->where('id', $awb->shipment_id)
                            ->first();
                    }
                }
            }
        }

        // ------------ Customer name (اسم العميل) + Service type (نوع الخدمة) ------------
        $customerName = '';
        $serviceType  = '';
        if ($shipment) {
            $customerName = $this->extractCustomerName($shipment);
            $serviceType  = (string) ($shipment->service_type_reference ?? '');
        }

        // ------------ Amounts ------------
        $operationAmount = $this->money($ti->amount ?? 0);
        $discountAmount  = $this->money($ti->discount_amount ?? 0);
        $paymentMethod   = (string) ($ti->transaction_type ?? '');

        // ------------ Barcode + QR ------------
        $codes = $this->buildCodes($invoiceNumber, $issueDate);

        return [
            'logo_src'       => $this->buildLogoSrc(),
            'supplier' => $supplier,
            'invoice_number' => $invoiceNumber,
            'issue_date'     => $issueDate,
            'seller_branch'  => $sellerBranch,
            'nb_packages'    => $nbPackages,
            'customer_name'  => $customerName,
            'service_type'   => $serviceType,
            'barcode_src'    => $codes['barcode'],
            'qr_src'         => $codes['qr'],
            'amounts' => [
                'operation_amount'       => $operationAmount,
                'discount_total'         => $discountAmount,
                // HARDCODED placeholders until calculation rules are confirmed:
                'fuel_surcharge_percent' => '%25',
                'fuel_surcharge'         => '14.04',
                'taxable_amount'         => '41.04',
                'vat_percent'            => '10%',
                'vat_amount'             => '4.10',
                'invoice_total'          => '45.14',
                'amount_paid'            => '50',
                'remaining_total'        => '4.86',
            ],
            'payment_method' => $paymentMethod,
        ];
    }

    /**
     * Builds the Code128 barcode and QR code data-URIs shown at the bottom of the receipt.
     *
     * Returns ['barcode' => data-uri|'', 'qr' => data-uri|''].
     *
     * We emit base64 PNGs (via getBarcodePNG) instead of getBarcodeHTML on purpose: the HTML
     * variant outputs a row of <div> bars with inline pixel widths that can't be scaled with CSS,
     * which causes the barcode to overflow the 80mm receipt for longer invoice numbers.
     * A real <img> can be constrained with `max-width: 100%`.
     */
    private function buildCodes(string $invoiceNumber, string $issueDate): array
    {
        if ($invoiceNumber === '') {
            return ['barcode' => '', 'qr' => ''];
        }

        $dns1d = new DNS1D();
        $dns2d = new DNS2D();

        $barcodePng = $dns1d->getBarcodePNG($invoiceNumber, 'C128', 2, 55);

        $qrPayload = implode('|', array_filter([
            'INV:' . $invoiceNumber,
            $issueDate !== '' ? 'DATE:' . $issueDate : null,
        ]));

        $qrPng = $dns2d->getBarcodePNG($qrPayload, 'QRCODE', 6, 6);

        return [
            'barcode' => $barcodePng ? 'data:image/png;base64,' . $barcodePng : '',
            'qr'      => $qrPng ? 'data:image/png;base64,' . $qrPng : '',
        ];
    }

    /**
     * Loads the SMSA logo shipped with the package and returns it as a base64 data-URI.
     *
     * Inlining the PNG keeps the receipt self-contained: it survives print preview, dompdf,
     * and any host app that doesn't serve a public logo asset.
     */
    private function buildLogoSrc(): string
    {
        $path = __DIR__ . self::LOGO_RELATIVE_PATH;

        if (!is_file($path) || !is_readable($path)) {
            return '';
        }

        $binary = @file_get_contents($path);
        if ($binary === false || $binary === '') {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($binary);
    }

    /**
     * Builds the four-line supplier block (المورد) from the hub's linked address.
     *
     * Line 1 is the legal name (hardcoded).
     * Line 2 is the street (addresses.address1, fallback to label).
     * Line 3 is "<city>, <country>" resolved via countries.name / cities.name.
     * Line 4 is the postal/area code from addresses.area_code.
     */
    private function buildSupplierBlock($address): array
    {
        $line2 = '';
        $cityName = '';
        $countryName = '';
        $postal = '';

        if ($address) {
            $line2 = trim((string) ($address->address1 ?? '')) ?: trim((string) ($address->label ?? ''));
            $postal = trim((string) ($address->area_code ?? ''));

            if (!empty($address->country)) {
                $countryName = (string) DB::table('countries')
                    ->whereNull('deleted_at')
                    ->where('code', $address->country)
                    ->value('name');
            }

            if (!empty($address->city)) {
                $cityQuery = DB::table('cities')->whereNull('deleted_at')->where('code', $address->city);
                if (!empty($address->country)) {
                    $cityQuery->where('country', $address->country);
                }
                $cityName = (string) $cityQuery->value('name');
            }
        }

        $line3 = trim(implode(', ', array_filter([$cityName, $countryName])));

        return [
            'line1'       => self::SUPPLIER_NAME_AR,
            'line2'       => $line2,
            'line3'       => $line3,
            'postal_code' => $postal,
        ];
    }

    /**
     * Tries to pull a human-readable customer name from the available shipment snapshots.
     * Falls back across consignee_snapshot, receiver_address_snapshot, shipper_snapshot,
     * and sender_address_snapshot since the source field wasn't pinned down.
     */
    private function extractCustomerName($shipment): string
    {
        $snapshots = [
            'consignee_snapshot',
            'receiver_address_snapshot',
            'shipper_snapshot',
            'sender_address_snapshot',
        ];

        foreach ($snapshots as $field) {
            $raw = $shipment->$field ?? null;
            if (empty($raw)) {
                continue;
            }

            $decoded = is_array($raw) ? $raw : json_decode($raw, true);
            if (!is_array($decoded)) {
                continue;
            }

            foreach (['name', 'full_name', 'label', 'attention', 'company'] as $key) {
                if (!empty($decoded[$key]) && is_string($decoded[$key])) {
                    return $decoded[$key];
                }
            }
        }

        return '';
    }

    private function money($value): string
    {
        $float = (float) ($value ?? 0);
        return rtrim(rtrim(number_format($float, 2, '.', ''), '0'), '.');
    }
}
