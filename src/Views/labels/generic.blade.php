<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title ?? 'Label' }}</title>
    <style>
        @page {
            size: A6 portrait;
            margin: 8mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            color: #333;
            line-height: 1.4;
            -webkit-font-smoothing: antialiased;
        }

        .awb-number {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            border-radius: 6px;
            padding: 8px 0;
        }

        .page {
            padding: 8mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-section {
            text-align: center;
            padding: 8px 0 12px;
            margin-bottom: 18px;
            position: relative;
        }

        .logo-section img {
            max-width: 75%;
            max-height: 45px;
            filter: contrast(1.1);
        }

        .title {
            text-align: center;
            font-size: 56px;
            font-weight: 800;
            letter-spacing: 4px;
            line-height: 1;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .barcode-section {
            text-align: center;
            padding: 10px 5px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .barcode-container {
            width: 100%;
        }

        .barcode-container table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .barcode-row {
            text-align: center;
        }

        .barcode-row svg,
        .barcode-row img {
            display: block !important;
            margin: 0 auto !important;
            width: 90% !important;
            max-width: 90% !important;
            height: 60px !important;
        }

        .barcode-value {
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            margin: 4px 0 14px;
        }

        .route-info {
            text-align: center;
            font-size: 20px;
            padding: 18px 12px;
            margin: 20px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            background: #f8f9fa;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
        }

        .branch-info {
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .code-info {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .reference-info {
            text-align: center;
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="page">
        @if(!empty($logo))
            <div class="logo-section">
                <img src="{{ $logo }}" alt="Logo" />
            </div>
        @else
            <div class="title">{{ $title ?? 'LABEL' }}</div>
        @endif

        <div class="barcode-section">
            <div class="barcode-container">
                <table>
                    <tr class="barcode-row">
                        <td style="text-align: center;">
                            @if(!empty($barcodeData) && str_starts_with(trim($barcodeData), '<'))
                                {!! $barcodeData !!}
                            @elseif(!empty($barcodeData))
                                <img src="{{ $barcodeData }}" alt="Barcode">
                            @endif
                            <div class="barcode-value">{{ $code ?? '-' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

       

     

        <div class="awb-number">
            {{ $footer_code_line  }}
        </div>
    </div>
</body>
</html>

