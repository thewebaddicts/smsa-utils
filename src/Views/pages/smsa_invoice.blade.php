<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SMSA Tax Invoice</title>

    <style>
        @font-face {
            font-family: 'InvoiceArabic';
            src: url("file://{{ storage_path('fonts/NotoNaskhArabic-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'InvoiceArabic';
            src: url("file://{{ storage_path('fonts/NotoNaskhArabic-Bold.ttf') }}") format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111;
            margin: 12px;
        }

        .top {
            width: 100%;
            padding-bottom: 4px;
        }

        .left {
            width: 32%;
            float: left;
            text-align: left;
        }

        .right {
            float: right;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .logo {
            width: 90px;
            height: 18px;
            object-fit: contain;
            display: block;
            margin-left: 0;
            margin-right: auto;
        }

        .top-head {
            position: relative;
            height: 12px;
            margin-bottom: 22px;
        }

        .top-meta {
            font-size: 8px;
            position: absolute;
            left: 0;
            top: 0;
            color: #444;
        }

        .top-title {
            font-size: 8px;
            text-align: center;
            color: #333;
        }

        .title {
            font-size: 20px;
            font-weight: 700;
            margin-top: 8px;
        }

        .ar {
            font-size: 13px;
            direction: ltr;
            text-align: right;
            font-family: 'InvoiceArabic', DejaVu Sans, sans-serif;
        }

        .note {
            font-size: 10px;
            margin: 3px 0;
            line-height: 1.3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 4px;
            vertical-align: middle;
        }

        th {
            background: #f5f5f5;
            font-size: 9px;
        }

        .amount {
            text-align: center;
            width: 22%;
        }

        .section-title {
            font-weight: 700;
            margin-top: 10px;
        }

        .total-row td {
            font-weight: 700;
            background: #f3f3f3;
        }

        .mini {
            /* padding-top: 10px; */
            font-size: 13px;
            color: #333;
        }

        .ar-title {
            font-family: 'InvoiceArabic', DejaVu Sans, sans-serif !important;
            font-weight: 700 !important;
            padding-top: 10px;
        }

        .en-title {
            font-weight: 700;
        }

        .details-wrap {
            width: 100%;
            margin: 8px 0 5px;
        }

        .details-left,
        .details-right {
            width: 48.5%;
            display: inline-block;
            vertical-align: top;
        }

        .details-right {
            margin-left: 2%;
            text-align: right;
        }

        .d-row {
            margin-bottom: 5px;
            border: 1px solid #666;
            border-radius: 2px;
            background: #fafafa;
            padding: 4px 6px;
            display: table;
            width: 100%;
        }

        .d-value {
            font-size: 11px;
            font-weight: 700;
            display: table-cell;
            width: 44%;
            vertical-align: middle;
            word-break: break-word;
        }

        .d-labels {
            display: table-cell;
            width: 56%;
            vertical-align: middle;
        }

        .d-ar {
            font-family: 'InvoiceArabic', DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.15;
        }

        .d-en {
            font-size: 9px;
            color: #444;
            line-height: 1.1;
            margin-top: 1px;
        }

        .info-grid {
            width: 100%;
            margin: 8px 0 6px;
            border-collapse: collapse;
        }

        .info-grid td {
            border: none;
            padding: 7px 4px;
            vertical-align: top;
            width: 50%;
        }

        .info-row {
            margin: 0 0 6px;
            display: table;
            width: auto;
        }

        .info-value {
            display: table-cell;
            width: auto;
            font-size: 9px;
            font-weight: 700;
            line-height: 1.2;
            vertical-align: middle;
            white-space: nowrap;
            padding-right: 4px;
            padding-left: 4px;
        }

        .info-labels {
            display: table-cell;
            width: auto;
            vertical-align: middle;
            text-align: right;
        }

        .info-ar {
            display: block;
            font-family: 'InvoiceArabic', DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.1;
            white-space: nowrap;
        }

        .info-en {
            display: block;
            font-size: 8px;
            color: #555;
            line-height: 1.1;
            white-space: nowrap;
        }

        .info-right {
            text-align: right;
        }

        .info-left .info-row {
            margin-left: 0;
        }

        .info-left .info-value {
            text-align: right;
        }

        .info-left .info-labels {
            min-width: 80px;
            max-width: 80px;
        }

        .info-left .info-ar,
        .info-left .info-en {
            text-align: left;
        }

        .info-right .info-row {
            margin-left: auto;
            margin-right: 0;
        }

        /* .info-right .info-value { width: 130px; text-align: left; } */
        .info-left-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 2px;
            table-layout: fixed;
        }

        .info-left-table td {
            border: none;
            padding: 0;
        }

        .info-left-table .vcol {
            width: 38%;
            text-align: right;
            font-weight: 700;
            font-size: 10px;
            line-height: 1.2;
            white-space: nowrap;
            padding-right: 2px;
        }

        .info-left-table .lcol {
            text-align: right;
        }

        .info-left-table .lcol .info-ar {
            font-size: 14px;
            line-height: 1.1;
            white-space: nowrap;
            text-align: right;
            width: 90px;
        }

        .info-left-table .lcol .info-en {
            font-size: 8px;
            line-height: 1.1;
            color: #555;
            white-space: nowrap;
            text-align: right;
            margin-top: 1px;
            width: 90px;
        }

        .info-left-table tr.lrow td {
            padding-bottom: 1px;
            border-bottom: none;
        }

        .info-left-table tr.lrow:last-child td {
            padding-bottom: 0;
        }

        .charges-table {
            margin-top: 8px;
        }

        .charges-table th {
            font-size: 9px;
            font-weight: 700;
            text-align: center;
            line-height: 1.2;
        }

        .charges-table .desc-col {
            width: 78%;
        }

        .charges-table .amount-col {
            width: 22%;
        }

        .charges-table td {
            padding: 3px 6px;
        }

        .charges-table .desc {
            text-align: right;
            line-height: 1.15;
        }

        .charges-table .desc .ar-line {
            display: block;
            font-family: 'InvoiceArabic', DejaVu Sans, sans-serif;
            font-size: 9px;
            white-space: nowrap;
        }

        .charges-table .desc .en-line {
            display: block;
            font-size: 8px;
            white-space: nowrap;
        }

        .charges-table .amount {
            font-size: 10px;
            font-weight: 700;
            text-align: center;
        }

        .charges-table .total-row td {
            background: #f0f0f0;
            font-weight: 700;
        }

        .invoice-footer {
            margin-top: 10px;
            border-top: 1px solid #666;
            padding-top: 6px;
        }

        .invoice-footer .footer-en {
            font-size: 8px;
            color: #333;
            line-height: 1.35;
            text-align: left;
            margin-bottom: 4px;
        }

        .invoice-footer .footer-ar {
            font-family: 'InvoiceArabic', DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.35;
            text-align: right;
            direction: rtl;
            /* unicode-bidi: plaintext; */
            margin-bottom: 3px;
            direction: ltr
        }
    </style>
</head>

<body>
    @php
        $isNonZero = static fn($value) => (float) preg_replace('/[^0-9.\-]/', '', (string) $value) != 0.0;
    @endphp
    <div class="top">

        <div class="left">
            @if (!empty($logo))
                <img class="logo" style="margin-bottom: 10px" src="{{ $logo }}" alt="SMSA logo">
            @endif

            <div>
             <barcode code="https://example.com/invoice/123" type="QR" size="1" error="M" />

            </div>

        </div>
        <div class="right" >
            <div class="ar ar-title">{{ $ar_company_line ?? '' }}</div>
            <div class="mini ar-title">{{ $ar_tax_reg_line ?? '' }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <div style="text-align:center; margin: 5px 0 2px; font-weight:700; direction:rtl ">
        <div style="display:inline-block;font-size: 18px">TAX INVOICE - {{ $ar_title_line ?? 'S' }}</div>
        {{-- <div style="display:inline-block;font-size: 18px">TAX INVOICE</div> --}}
    </div>

    <table class="info-grid" style="margin: 30px 0;">
        <tr>
            <td>
                <table>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px;">{{ $invoice_date }}</td>
                        <td class="lcol" style="width:180px;text-align: left">
                            <div style="font-size: 14px">: {{ $ar_labels['invoice_date'] ?? '' }}</div>
                            <div>(Invoice Date)</div>
                        </td>
                    </tr>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px;">{{ $origin }}</td>
                        <td class="lcol" style="width:180px;text-align: left">
                            <div style="font-size: 14px">: {{ $ar_labels['origin'] ?? '' }}</div>
                            <div>(Origin)</div>
                        </td>
                    </tr>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px;">{{ $destination }}</td>
                        <td class="lcol" style="width:180px;text-align: left">
                            <div style="font-size: 14px">: {{ $ar_labels['destination'] ?? '' }}</div>
                            <div>(Destination)</div>
                        </td>
                    </tr>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px;">{{ $weight }}</td>
                        <td class="lcol" style="width:180px;text-align: left">
                            <div style="font-size: 14px">: {{ $ar_labels['weight'] ?? '' }}</div>
                            <div>(Weight)</div>
                        </td>
                    </tr>
                </table>
            </td>

            <td>
                <table>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px">{{ $invoice_number }}</td>
                        <td class="lcol" style="width:180px; text-align: right">
                            <div style="font-size: 14px">: {{ $ar_labels['tax_invoice_number'] ?? '' }}</div>
                            <div>(Tax Invoice Number)</div>
                        </td>
                    </tr>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px">{{ $name }}</td>
                        <td class="lcol" style="width:180px;text-align: right">
                            <div style="font-size: 14px">: {{ $ar_labels['name'] ?? '' }}</div>
                            <div>(Name)</div>
                        </td>
                    </tr>
                    <tr class="lrow">
                        <td class="vcol" style="padding-top: 5px">{{ $contact_number }}</td>
                        <td class="lcol" style="width:180px;text-align: right">
                            <div style="font-size: 14px">: {{ $ar_labels['contact_number'] ?? '' }}</div>
                            <div>(Contact Number)</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <th class="amount">
                <div>Shipment Value in ({{ $currency }})</div>
                <div class="ar">{{ $ar_table_labels['shipment_value_local_currency'] ?? '' }}</div>
            </th>
            <th>Customs Declaration (BAYAN) No</th>
            <th>Shipment Number</th>
        </tr>
        <tr>
            <td class="amount">{{ $shipment_value }}</td>
            <td>{{ $bayan_number }}</td>
            <td>{{ $shipment_number }}</td>
        </tr>
    </table>

    <table width="100%" class="charges-table">
        <tr>
            <th class="amount-col">
                <span class="ar">{{ $ar_table_labels['amount'] ?? '' }} ({{ $currency }})</span>
                <span>Amount ({{ $currency }})</span>
            </th>
            <th class="desc-col">
                <span class="ar">{{ $ar_table_labels['description'] ?? '' }}</span>
                <span>Description</span>
            </th>
        </tr>
        @if($isNonZero($customs_duties))
        <tr>
            <td class="amount">{{ $customs_duties }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['customs_duties'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">Customs Duties ({{ $currency }})</span>
            </td>
        </tr>
        @endif
        @if($isNonZero($vat_on_bayan))
        <tr>
            <td class="amount">{{ $vat_on_bayan }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['vat_on_bayan'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">VAT On Bayan ({{ $currency }}) 5.00% of Shipment Value</span>
            </td>
        </tr>
        @endif
        @if($isNonZero($custom_service_fee))
        <tr>
            <td class="amount">{{ $custom_service_fee }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['custom_service_fee'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">Custom Services Fee ({{ $currency }})</span>
            </td>
        </tr>
        @endif
        @if($isNonZero($vat_on_custom_service_fee))
        <tr>
            <td class="amount">{{ $vat_on_custom_service_fee }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['vat_on_custom_service_fee'] ?? '' }}
                    ({{ $currency }})</span>
                <span class="en-line">5.00% VAT on Custom Services Fee ({{ $currency }})</span>
            </td>
        </tr>
        @endif
        @if($isNonZero($admin_fee))
        <tr>
            <td class="amount">{{ $admin_fee }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['admin_fee'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">Admin fee ({{ $currency }})</span>
            </td>
        </tr>
        @endif
        @if($isNonZero($vat_on_admin_fee))
        <tr>
            <td class="amount">{{ $vat_on_admin_fee }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['vat_on_admin_fee'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">5.00% VAT on Admin Fee ({{ $currency }})</span>
            </td>
        </tr>
        @endif
        @if($isNonZero($other_charges))
        <tr>
            <td class="amount">{{ $other_charges }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['other_charges'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">Other Charges ({{ $currency }})</span>
            </td>
        </tr>
        @endif

        <tr class="total-row">
            <td class="amount">{{ $total_customs_amount }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['total_customs_amount'] ?? '' }}
                    ({{ $currency }})</span>
                <span class="en-line">Total Customs Amount ({{ $currency }})</span>
            </td>
        </tr>
        <tr class="total-row">
            <td class="amount">{{ $total_vat_amount }}</td>
            <td class="desc">
                <span class="ar-line">{{ $ar_charge_labels['total_vat_amount'] ?? '' }} ({{ $currency }})</span>
                <span class="en-line">Total Vat Amount ({{ $currency }})</span>
            </td>
        </tr>
    </table>
    <div style="margin-top:30px">
        <table class="info-grid">
            <tr>
                <td colspan="2" style="padding: 10px 0; line-height:1.5">
                    If you have any questions regarding this invoice, please contact us by email within 3 days of this
                    invoice date else it will considered correct. (info@smsaexpress.com)
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;padding: 20px 0; line-height:1.5">
                    {{ $ar_footer['contact_notice'] ?? '' }} (info@smsaexpress.com)
                </td>
            </tr>
            <tr>
                <td style="text-align: left;padding: 20px 0; line-height:1.5">
                    This is a computer generated TAX invoice and no signature required.
                </td>
                <td style="text-align: right;padding: 20px 0; ; line-height:1.5">
                    {{ $ar_footer['computer_generated'] ?? '' }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
