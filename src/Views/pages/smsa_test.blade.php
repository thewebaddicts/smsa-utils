
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMSA AWB Label</title>
    <style>
        @page {
            size: A6 portrait;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .content-box {
            padding: 10px 5px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 5px;
            background: white;
            color: #150640;
        }

        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
        }

        .container {
            border: 2px solid #000;
            padding-bottom: calc(174800% / 1240);
            position: relative;
            box-sizing: initial;
            overflow: hidden;
        }

        .container-inner {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
        }

        .container-content {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* Row 1 - Single cell */
        .row-1 {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: calc(260 / 1748 * 75%);
            /* 14.88% */
            /*height: calc(260 / 1748 * 100%); !* 14.88% *!*/
            border-bottom: 2px solid #000;
        }

        /* Row 2 - 4 boxes */
        .row-2-box-1 {
            position: absolute;
            top: calc(260 / 1748 * 100%);
            /* 14.88% */
            left: 0;
            width: calc(276 / 1240 * 100%);
            /* 22.26% */
            /*height: calc(202 / 1748 * 100%); !* 11.55% *!*/
            height: calc(202 / 1748 * 70%);
            /* 11.55% */
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .row-2-box-2 {
            position: absolute;
            top: calc(260 / 1748 * 100%);
            /* 14.88% */
            left: calc(276 / 1240 * 100%);
            /* 22.26% */
            width: calc(276 / 1240 * 100%);
            /* 22.26% */
            height: calc(202 / 1748 * 70%);
            /* 11.55% */
            /*height: calc(202 / 1748 * 100%); !* 11.55% *!*/
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .row-2-box-3 {
            position: absolute;
            top: calc(260 / 1748 * 100%);
            /* 14.88% */
            left: calc((276 / 1240 * 100%) * 2);
            /* 44.52% */
            width: calc(276 / 1240 * 100%);
            /* 22.26% */
            /*height: calc(202 / 1748 * 100%); !* 11.55% *!*/
            height: calc(202 / 1748 * 70%);
            /* 11.55% */
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .row-2-box-4 {
            position: absolute;
            top: calc(260 / 1748 * 100%);
            /* 14.88% */
            left: calc((276 / 1240 * 100%) * 3);
            /* 66.78% */
            width: calc(100% - ((276 / 1240 * 100%) * 3));
            /* 33.22% */
            /*height: calc(202 / 1748 * 100%); !* 11.55% *!*/
            height: calc(202 / 1748 * 70%);
            /* 11.55% */
            border-bottom: 2px solid #000;
        }

        /* Row 3 - 2 boxes */
        .row-3-box-1 {
            position: absolute;
            top: calc((260 + 202) / 1748 * 95%);
            /* 26.43% */
            left: 0;
            width: calc(828 / 1240 * 100%);
            /* 66.77% */
            height: calc(194 / 1748 * 95%);
            /* 11.10% */
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .row-3-box-2 {
            position: absolute;
            top: calc((260 + 202) / 1748 *93%);
            /* 26.43% */
            left: calc(828 / 1240 * 100%);
            /* 66.77% */
            width: calc(100% - (828 / 1240 * 100%));
            /* 33.23% */
            height: calc(194 / 1748 * 100%);
            /* 11.10% */
            border-bottom: 2px solid #000;
        }


        /* Row 4 - Complex nested structure */
        .row-4-box-1 {
            position: absolute;
            top: calc((260 + 202 + 194) / 1748 * 105%);
            /* 37.53% */
            left: 0;
            width: calc(986 / 1240 * 100%);
            /* 79.52% */
            height: calc(723 / 1748 * 100%);
            /* 41.36% */
            border-right: 2px solid #000;
            /*border-bottom: 2px solid #000;*/
        }

        .row-4-box-2 {
            position: absolute;
            top: calc((260 + 202 + 194) / 1748 * 100%);
            /* 37.53% */
            left: calc(986 / 1240 * 100%);
            /* 79.52% */
            width: calc(100% - (986 / 1240 * 100%));
            /* 20.48% */
            height: calc(723 / 1748 * 100%);
            /* 41.36% */
            /*border-bottom: 2px solid #000;*/
        }

        /* Row 4 Inner boxes - Upper row */
        .row-4-box-1-inner-1-box-1 {
            position: absolute;
            /*margin-top: 10px;*/
            top: calc((260 + 202 + 194) / 1748 * 105%);
            /* 37.53% */
            left: 0;
            width: calc((986 / 1240) * (224 / 984) * 100%);
            /* 18.11% */
            height: calc((723 / 1748) * (374 / 721) * 100%);
            /* 21.46% */
            border-right: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .row-4-box-1-inner-1-box-2 {
            position: absolute;
            top: calc((260 + 202 + 194) / 1748 * 105%);
            /* 37.53% */
            left: calc((986 / 1240) * (224 / 984) * 100%);
            /* 18.11% */
            width: calc((986 / 1240) * (760 / 984) * 100%);
            /* 61.41% */
            height: calc((723 / 1748) * (374 / 721) * 100%);
            /* 21.46% */
            border-bottom: 2px solid #000;

        }

        /* Row 4 Inner boxes - Lower row */
        .row-4-box-1-inner-2-box-1 {
            position: absolute;
            top: calc(((260 + 202 + 194) / 1748 * 100%) + ((723 / 1748) * (374 / 721) * 100%));
            /* 58.99% */
            left: 0;
            width: calc((986 / 1240) * (224 / 984) * 100%);
            /* 18.11% */
            height: calc((723 / 1748) * (347 / 721) * 100%);
            /* 19.90% */
            /*border-right: 2px solid #000;*/
            /*border-bottom: 2px solid #000;*/
        }

        .row-4-box-1-inner-2-box-2 {
            position: absolute;
            top: calc(((260 + 202 + 194) / 1748 * 100%) + ((723 / 1748) * (374 / 721) * 100%));
            /* 58.99% */
            left: calc((986 / 1240) * (224 / 984) * 115%);
            /* 18.11% */
            width: calc((986 / 1240) * (760 / 984) * 100%);
            /* 61.41% */
            height: calc((723 / 1748) * (347 / 721) * 110%);
            /* 19.90% */
            border-left: 2px solid #000;
        }

        /* Row 5 - Footer */
        .row-5 {
            position: absolute;
            top: calc((260 + 202 + 194 + 723) / 1748 * 107%);
            left: 0;
            width: 100%;
            height: calc(100% - ((260 + 202 + 194 + 723) / 1748 * 100%));
            border-top: 2px solid #000;
        }

        /*Hussein styles*/

        .big-text {
            font-size: 19px;
            font-weight: bold;
        }

        .label {
            font-size: 9px;
            font-weight: bold;
            font-family: 'almarai', 'DejaVu Sans', sans-serif;
        }

        .value {
            font-size: 9px;
            font-weight: 700;
            font-family: 'almarai', 'DejaVu Sans', sans-serif;
        }

        /* Text rendering for all content types (Arabic, English, Mixed) */
        .mixed-text {
            /* Let the ar-php library handle text direction via preprocessing */
            unicode-bidi: normal;
        }

        table {
            width: 100%;
        }

        td,
        th {
            padding: 0;
            text-align: left;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    {{-- used for awb label --}}
    <div class="container">
        <div class="container-inner">
            <div class="container-content">
                <div class="row-1 content-box">
                    <table>
                        <tr>
                            <td>
                                <div style="width: 100px; height: 20px;">
                                    <img width="100" height="20" style="object-fit: contain;"
                                        src="{{ $logo ?? '' }}" alt="" />
                                </div>
                            </td>
                            <td style="padding-right: 15px;">
                                <div style="font-size: 17px; font-weight: bold;text-align: right">
                                    @if (!empty($is_reexport))
                                        <span class="label" style="color: #b00020; font-size: 11px; margin-right: 6px;">RE-EXPORT</span>
                                    @endif
                                    {{ $page_count ?? '' }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="">
                                <div>
                                    <div class="label">Date:
                                        <span class="value">{{ $date ?? '' }}</span>
                                    </div>
                                </div>
                                <div style="width: 160px;margin-right: 10px;padding-bottom: -2px">
                                    <div class="label">Order Reference:
                                        <span class="value">{{ $order_reference ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding-right: 15px;overflow: hidden;padding-top: 5px;">
                                <div class="big-text" style="text-align: right">{{ $tracking_number ?? '' }}</div>
                            </td>
                        </tr>
                    </table>

                </div>

                <div class="row-2-box-1 content-box">
                    <div class="big-text" style="padding:3px 0px;">{{ $service_dom ?? '' }}</div>
                </div>

                <div class="row-2-box-2 content-box">
                    <div class="big-text" style="padding:3px 15px;">{{ $service_cda ?? '' }}</div>
                </div>

                <div class="row-2-box-3 content-box">
                    <div class="big-text" style="padding:3px 15px;">{{ $freight_payment_type ?? '' }}</div>
                </div>

                <div class="row-2-box-4 content-box">
                    <div style="width: 45px; height: 45px;padding:0 15px 0;">
                        <div class="qrcode-square" style="width: 45px; height: 45px;">
                            {!! $qrcode !!}
                        </div>
                        {{--                        <img width="45" height="45" src="/assets/images/smsa.svg" alt=""/> --}}
                    </div>
                </div>
                <div class="row-3-box-1 content-box">
                    <table>
                        <tr>
                            <!-- First column -->
                            <td style="padding:3px 0 1px; vertical-align: top;">
                                <div class="label">
                                    Weight: <span class="value">{{ $weight ?? '' }}</span>
                                </div>

                                <div class="label" style="margin-top: 2px;">
                                    Commodity Type: <span class="value mixed-text">{{ $commodity ?? '' }}</span>
                                </div>
                                <div class="label" style="margin-top: 2px;">
                                    Description: <span class="value mixed-text">{{ $description ?? '' }}</span>
                                </div>

                                @if (!empty($declared_amount))
                                    <div class="label" style="margin-top: 2px;">
                                        Declared Value: <span class="value">{{ $declared_amount }}</span>
                                    </div>
                                @endif
                            </td>


                            @if (empty($customs_payment_type))
                                <td style="padding:3px 0 1px; vertical-align: top;">
                                    <div class="label">
                                        Insurance: <span class="value">{{ $insurance ? 'YES' : 'NO' }}</span>
                                    </div>
                                </td>
                            @endif
                            <!-- Second column (only if Customs exists) -->
                            @if (!empty($customs_payment_type))
                                <td style="padding:3px 0 1px; vertical-align: top;">
                                    <div class="label">
                                        Customs: <span class="value">{{ $customs_payment_type }}</span>
                                    </div>
                                    <div class="label">
                                        Insurance: <span class="value">{{ $insurance ? 'YES' : 'NO' }}</span>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </table>
                </div>

                <div class="row-3-box-2 content-box" style="text-align: left;">
                    <div style="padding: 3px;">
                        <span class="label" style="font-size: 12px;padding-left: 15px;">COD</span><br>
                        <span class="value" style="font-size: 14px;padding-left: 15px;">{{ $cod_sar ?? '' }}</span>
                    </div>
                </div>

                <div class="row-4-box-1 content-box">
                </div>

                <div class="row-4-box-2 content-box">
                    <div style="position:absolute; left:35px;top: 70%;">
                        <div class="barcode-vertical" style="width: 30px;height: 100%;">
                            {!! $barcode_vertical !!}
                        </div>

                    </div>
                </div>

                <div style="overflow: hidden" class="row-4-box-1-inner-1-box-1 content-box">
                    <div style="width: fit-content;height: fit-content;position:absolute;top: 9%;left: 5px;bottom: 0;">
                        <div class="label">Origin</div>
                        <div style="font-size: 23px; font-weight: bold;line-height: 1">{{ $origin_city_code ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="row-4-box-1-inner-1-box-2 content-box">
                    <div class="" style="position: absolute;left: 25px;top: 9%">
                        <div class="details">
                            <div>
                                <div class="label">Shipper:
                                    <span class="value">{{ $shipper_number ?? '' }}</span>
                                </div>
                            </div>
                            <div class="mixed-text" style="font-size: 12px;font-weight: bold;">
                                {{ $shipper_name ?? '-' }}
                            </div>
                            <div style="height: 5px"></div>
                            <div class="label">
                                <div class="mixed-text">{{ $shipper_attention ?? '' }}</div>

                                <div class="mixed-text">{{ $shipper_address_1 ?? '' }}</div>
                                <div class="mixed-text">{{ $shipper_address_2 ?? '' }}</div>
                                <div class="mixed-text">{{ $shipper_district ?? '' }}</div>
                                <div class="mixed-text">{{ $shipper_area_code ?? '' }}</div>
                                <div>{{ $shipper_phone ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="overflow: hidden" class="row-4-box-1-inner-2-box-1 content-box">
                    <div style="width: fit-content;height: fit-content;position:absolute;top: 33%;left: 5px;bottom: 0;">
                        <div class="label">Destination</div>
                        <h2 style="font-size: 23px; font-weight: bold;line-height: 1">
                            {{ $destination_city_code ?? '' }}
                        </h2>
                    </div>

                    {{-- @dd($destination_route); --}}
                    <!-- New Route section -->
                    <div style="position:absolute; left: 5px; bottom: 5px;">
                        <div class="label">{{ $destination_route ?? '-' }}</div>
                        {{-- <div class="value" style="font-size: 10px;">
            {{ $destination_route ?? '-' }}
        </div> --}}
                    </div>
                </div>

                <div class="row-4-box-1-inner-2-box-2 content-box">
                    <div class="" style="position: absolute;left: 15px;top: 29%;padding-right: 20px">
                        <div class="details">
                            <div class="label mixed-text"
                                 style="font-size: 12px;font-weight: bold;overflow: hidden;line-height: 1">
                                {{ in_array(($product ?? $service_cda ?? ''), ['RTS', 'CIR'], true) ? ($recipient_name ?? '') : ($sender_name ?? '') }}
                            </div>

                            <div style="height: 5px"></div>
                            <div class="label">
                                <div class="label">
                                    <div class="mixed-text">{{ $recipient_attention ?? '' }}</div>
                                    {{-- <div class="mixed-text">{{ $recipient_company ?? '' }}</div> --}}

                                    <div class="mixed-text">{{ $recipient_address_1 ?? '' }}</div>
                                    <div class="mixed-text">{{ $recipient_address_2 ?? '' }}</div>
                                    <div class="mixed-text">{{ $recipient_district ?? '' }}</div>
                                    <div class="mixed-text">{{ $recipient_area_code ?? '' }}</div>
                                    <div>{{ $recipient_phone ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-5 content-box">
                    <div style="position:absolute; left: 18%;top:2%">
                        <div class="barcode-horizontal">
                            <div class="value"
                                style="font-size: 10px; width: 100%;text-align: center;margin-bottom: 3px;padding-right:110px">
                                MASTER#:
                                <span class="label" style="font-size: 10px">{{ $master_awb ?? '' }}</span>

                            </div>
                            {!! $barcode_horizontal !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
