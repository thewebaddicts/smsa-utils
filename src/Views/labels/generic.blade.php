<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title ?? 'Label' }}</title>
    <style>
    
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            color: #1f2937;
            line-height: 1.3;
        }

        .page {
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 12px;
        }

        .logo-section img {
            max-width: 72%;
            max-height: 42px;
            margin-bottom: 15px;
        }

        .title {
            text-align: center;
            font-size: 44px;
            font-weight: 800;
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .barcode-section {
            text-align: center;
            margin-bottom: 8px;
        }

        .barcode-section img,
        .barcode-section svg {
            display: block !important;
            margin: 0 auto !important;
            width: 92% !important;
            max-width: 92% !important;
            height: 54px !important;
        }

        .barcode-value {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 3px;
            margin: 4px 0 14px;
        }

        .route-info {
            text-align: center;
            padding: 10px 8px;
            margin: 12px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            background: #f8f9fa;
            text-transform: uppercase;
        }

        .route-main {
            font-size: 28px;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .route-sub {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .route-description {
            text-align: center;
            font-size: 10px;
            letter-spacing: 0.5px;
            color: #4b5563;
        }

        .footer-code {
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 10px;
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
            @if(!empty($barcodeData) && str_starts_with(trim($barcodeData), '<'))
                {!! $barcodeData !!}
            @elseif(!empty($barcodeData))
                <img src="{{ $barcodeData }}" alt="Barcode">
            @endif
            <div class="barcode-value">{{ $code ?? '-' }}</div>
        </div>

     

        <div class="footer-code">
            {{ $footer_code_line ?? (($label_type ?? 'LABEL') . ': ' . ($code ?? '-')) }}
        </div>
    </div>
</body>
</html>

