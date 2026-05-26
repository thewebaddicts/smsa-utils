<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة ضريبية مبسطة</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 6mm 5mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #e9ecef;
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            direction: rtl;
            line-height: 1.55;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .receipt {
            background: #fff;
            width: 80mm;
            min-height: 100vh;
            margin: 16px auto;
            padding: 10mm 5mm;
            box-sizing: border-box;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
        }

        .print-bar {
            max-width: 80mm;
            margin: 16px auto 0;
            text-align: left;
        }

        .print-bar button {
            background: #111;
            color: #fff;
            border: 0;
            border-radius: 4px;
            padding: 8px 14px;
            font-size: 12px;
            cursor: pointer;
        }

        .print-bar button:hover {
            background: #333;
        }

        .title {
            text-align: center;
            font-weight: 700;
            font-size: 13px;
            margin: 0 0 10px;
        }

        .block {
            margin: 0 0 8px;
        }

        .row {
            margin: 1px 0;
        }

        .label {
            font-weight: 700;
        }

        .value-ltr {
            direction: ltr;
            unicode-bidi: embed;
        }

        .hr {
            border-top: 1px dashed #555;
            margin: 8px 0;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 9.5px;
        }

        .note {
            font-size: 9.5px;
            color: #333;
        }

        .totals .row {
            margin: 2px 0;
        }

        .barcode-wrap {
            margin: 8px 0 4px;
            text-align: center;
        }

        .barcode-wrap img {
            display: block;
            width: 100%;
            max-width: 100%;
            height: 50px;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
        }

        .qr-wrap {
            margin: 10px 0 4px;
            text-align: center;
        }

        .qr-wrap img {
            display: inline-block;
            width: 96px;
            height: 96px;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
        }

        @media print {
            html,
            body {
                background: #fff;
            }

            .print-bar {
                display: none;
            }

            .receipt {
                margin: 0 auto;
                box-shadow: none;
                padding: 0 5mm;
            }
        }
    </style>
</head>

<body>

    <div class="print-bar">
        <button type="button" onclick="window.print()">طباعة / Print</button>
    </div>

    <div class="receipt">

    <div class="title">فاتورة ضريبية مبسطة</div>

    <div class="block">
        <div class="row"><span class="label">المورد :</span></div>
        <div class="row">{{ $supplier['line1'] }}</div>
        @if (!empty($supplier['line2']))
            <div class="row">{{ $supplier['line2'] }}</div>
        @endif
        <div class="row">{{ $supplier['line3'] }}</div>
        <div class="row ">{{ $supplier['postal_code'] }}</div>
    </div>

    <div class="block">
        <div class="row">
            <span class="label">رقم الفاتورة :</span>
            <span class="value-ltr">{{ $invoice_number }}</span>
        </div>
        <div class="row">
            <span class="label">تاريخ الإصدار :</span>
            <span class="value-ltr">{{ $issue_date }}</span>

        </div>
        {{-- <div class="row value-ltr">{{ $issue_date }}</div> --}}
        <div class="row">
            <span class="label">فرع البائع :</span>
            <span class="value-ltr">{{ $seller_branch }}</span>
        </div>
        <div class="row">
            <span class="label">إجمالي عدد القطع :</span>
            <span class="value-ltr">{{ $nb_packages }}</span>
        </div>
    </div>

    <div class="hr"></div>

    <div class="block">
        <div class="row">
            <span class="label">اسم العميل :</span>
            <span class="value-ltr">{{ $customer_name }}</span>
        </div>
        <div class="row">
            <span class="label">نوع الخدمة :</span>
            <span class="value-ltr">{{ $service_type }}</span>
        </div>
    </div>

    <div class="block totals">
        <div class="row">
            <span class="label">مبلغ العملية :</span>
            <span class="value-ltr">{{ $amounts['operation_amount'] }}</span>
        </div>
        <div class="row">
            <span class="label">إجمالي الخصم :</span>
            <span class="value-ltr">{{ $amounts['discount_total'] }}</span>
        </div>
        <div class="row">
            <span class="label">رسوم إضافية الوقود ({{ $amounts['fuel_surcharge_percent'] }}) :</span>
            <span class="value-ltr">{{ $amounts['fuel_surcharge'] }}</span>
        </div>
        <div class="row">
            <span class="label">المبلغ الخاضع للضريبة :</span>
            <span class="value-ltr">{{ $amounts['taxable_amount'] }}</span>
        </div>
        <div class="row note">(غير شامل ضريبة القيمة المضافة)</div>
        <div class="row">
            <span class="label">ضريبة القيمة المضافة ({{ $amounts['vat_percent'] }}) :</span>
            <span class="value-ltr">{{ $amounts['vat_amount'] }}</span>
        </div>
        <div class="row">
            <span class="label">إجمالي الفاتورة :</span>
            <span class="value-ltr">{{ $amounts['invoice_total'] }}</span>
        </div>
        <div class="row note">(شاملاً ضريبة القيمة المضافة)</div>
        <div class="row">
            <span class="label">المبلغ المدفوع :</span>
            <span class="value-ltr">{{ $amounts['amount_paid'] }}</span>
        </div>
        <div class="row">
            <span class="label">إجمالي المتبقى :</span>
            <span class="value-ltr">{{ $amounts['remaining_total'] }}</span>
        </div>
        <div class="row">
            <span class="label">طريقة الدفع :</span>
            <span class="value-ltr">{{ $payment_method }}</span>
        </div>
    </div>

    <div class="hr"></div>

    <div class="center value-ltr">{{ $invoice_number }}</div>
    @if (!empty($barcode_src))
        <div class="center barcode-wrap">
            <img src="{{ $barcode_src }}" alt="{{ $invoice_number }}">
        </div>
    @endif

    <div class="hr"></div>

    <div class="center value-ltr">{{ $issue_date }}</div>
    @if (!empty($qr_src))
        <div class="center qr-wrap">
            <img src="{{ $qr_src }}" alt="QR">
        </div>
    @endif

    </div>

</body>

</html>
