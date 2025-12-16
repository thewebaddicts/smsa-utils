<?php

namespace twa\smsautils\Enums;

use UnhandledMatchError;

enum AwbStatusEnum: string
{
    case CREATED = 'SHCR';
    case PICKED_UP = 'SHPU';
    case RECEIVED_OPERATION = 'SHOR';
    case EXPECTED_RECEIVE = 'SHER';
    case CRN_IN = 'SHCI';
    case CRN_OUT = 'SHCO';
    case SHELF_IN = 'SHSI';
    case SHELF_OUT = 'SHSO';
    case HUB_IN = 'SHHI';
    case HUB_OUT = 'SHHO';
    case OPERATION_INBOUND = 'SHOI';
    case OPERATION_OUTSTANDING = 'SHOT';
    case OPERATION_OUTBOUND = 'SHOO';
    case DELIVERED = 'SHDL';
    case REVERSE_DELIEVERY = 'SHRD';
    case DEBRIEFED_OPERATION = 'SHDO';
    case DEBRIEFED_FINANCE = 'SHDF';
    case CLOSED = 'SHCL';
    case ADDRESS_CHANGED = 'SHAU';
    case ADDRESS_VALIDATED = 'SHAV';
    case UPDATED_DIMENSIONS = 'SHDU';
    case UPDATED_WEIGHT = 'SHWU';
    case CANCELLED = 'SHCN';
    case CHANGE_ROUTE = 'SHRC';
    case OUT_FOR_DELIVERY = 'SHOD';
    case SMS_SENT = 'SHSM';
    case SHIPPER_CANCELED = 'SHSC';
    case IN_TRANSIT = 'SHIT';
    case GATEWAY_INBOUND = 'SHGI';
    case GATEWAY_OUTBOUND = 'SHGO';
    case HOLD_FOR_PICKUP = 'SHHP';
    case HOLD = 'SHOH'; //was DEX
    case RELEASE_HOLD = 'SHRL';
    case HOLD_CUSTOMS = 'SHCH'; //was DEX
    case RELEASE_CUSTOMS = 'SHCU';
    case RETURN_IN = 'SHRI';
    case RETURN_OUT = 'SHRO';
    case RETURN_TO_SHIPPER = 'SHRS';
    case SCAN_RUNSHEET = 'SHRA';
    case SCAN_RUNSHEET_VERIFIED = 'SHRV';
    case  SCAN_RUNSHEET_UNASSIGNED = 'SHRU';
    case  DOCUMENT_UPLOAD = 'SHUD';



    case NOT_AVAILABLE_MOBILE_CLOSED  = 'ATMC';
    case NOT_AVAILABLE_NO_ANSWER  = 'ATNA';
    case NOT_AVAILABLE_RESCHEDULE  = 'ATRS';
    case NOT_AVAILABLE_TRAVELING  = 'ATTR';

    case NOT_AVAILABLE_WRONG_PHONE = 'ATWN';
    case NOT_AVAILABLE_WRONG_CUSTOMER = 'ATWC';
    case NOT_AVAILABLE_WRONG_CITY = 'ATLC';
    case NOT_AVAILABLE_ROUTE = 'ATLR'; //to be checked by hovig
    case NOT_AVAILABLE_OUT_OF_AREA = 'ATOA';

    case HOLD_PICKUP = 'SHHP';

    case NOT_PICKED_UP_RESCHEDULE = 'NPRS';
    case NOT_PICKED_UP_NO_ANSWER = 'NPNA';
    case NOT_PICKED_UP_ADDRESS_CHANGED = 'NPLR';


    case REFUSED_OPEN_SHIPMENT  = 'RFOS';
    case REFUSED_MONEY  = 'RFMO';
    case REFUSED_ALREADY_RECEIVED  = 'RFAR';
    case REFUSED_NO_LONGER_NEEDED = 'RFNN';
    case REFUSED_DELAYED = 'RFDL'; // was exp


    case OVERAGE = 'SHOV'; // was exp
    case ATTEMPTED = 'SHAT';
    case REFUSED = 'SHRF';


    case DAMAGED = 'EXDM'; // was exp
    case LOST = 'EXLO';


        //old statuses:

    case AF = 'AF';
    case CC = 'CC';
    case CR = 'CR';
    case DE = 'DE';
    case DEX03 = 'DEX03';
    case DEX08 = 'DEX08';
    case DEX14 = 'DEX14';
    case DEX29 = 'DEX29';
    case DEX41 = 'DEX41';
    case DEX42 = 'DEX42';
    case DEX93 = 'DEX93';
    case DF = 'DF';
    case DL = 'DL';
    case HIP = 'HIP';
    case HOP = 'HOP';
    case IC = 'IC';
    case OD = 'OD';
    case PU = 'PU';
    case RTIN = 'RTIN';
    case RTOPS = 'RTOPS';
    case SOP = 'SOP';
    case ST44 = 'ST44';
    case ST68 = 'ST68';
    case ST77 = 'ST77';
    case UTI = 'UTI';
    case UTLX = 'UTLX';
    case CD = 'CD';
    case ST41 = 'ST41';
    case ST64 = 'ST64';
    case DEX8X = 'DEX8X';
    case DEX09 = 'DEX09';
    case BA = 'BA';
    case CA = 'CA';
    case FD = 'FD';
    case NH = 'NH';
    case MS = 'MS';
    case OH = 'OH';
    case RD = 'RD';
    case WC = 'WC';
    case PRC = 'PRC';
    case TN = 'TN';
    case POD = 'POD';
    case RPMX = 'RPMX';
    case DEX03_1 = 'DEX03-1';
    case DEX03_2 = 'DEX03-2';
    case DEX03_3 = 'DEX03-3';
    case DEX03_4 = 'DEX03-4';
    case DEX03_7 = 'DEX03-7';
    case DEX03_8 = 'DEX03-8';
    case DEX03_9 = 'DEX03-9';
    case DEX03_10 = 'DEX03-10';
    case DEX03_12 = 'DEX03-12';
    case DEX03_13 = 'DEX03-13';
    case DEX03_14 = 'DEX03-14';
    case DEX03_15 = 'DEX03-15';
    case DEX03_16 = 'DEX03-16';
    case DEX07 = 'DEX07';
    case DEX07_3 = 'DEX07-3';
    case DEX07_4 = 'DEX07-4';
    case DEX07_5 = 'DEX07-5';
    case DEX07_6 = 'DEX07-6';
    case DEX07_7 = 'DEX07-7';
    case DEX07_8 = 'DEX07-8';
    case DEX93_1 = 'DEX93-1';
    case DEX93_2 = 'DEX93-2';
    case DEX93_3 = 'DEX93-3';
    case DEX93_4 = 'DEX93-4';
    case DEX93_5 = 'DEX93-5';
    case RTS = 'RTS';
    case RTI = 'RTI';
    case ST50 = 'ST50';
    case Data = 'Data';
    case RTO = 'RTO';
    case SMS = 'SMS';
    case DEX17 = 'DEX17';
    case PUX43 = 'PUX43';
    case ADV = 'ADV';
    case PUX_17 = 'PUX-17';
    case PUX03_1 = 'PUX03-1';
    case PUX3 = 'PUX3';
    case ST60_10 = 'ST60-10';
    case ST60_5 = 'ST60-5';
    case ST60_6 = 'ST60-6';
    case BTRO = 'BTRO';
    case BTRI = 'BTRI';

        // Additional status codes from scans.json (not duplicates)
    case HAL = 'HAL';
    case DEX34 = 'DEX34';
    case DEX84 = 'DEX84';
    case CODRTS = 'CODRTS';
    case RTSIN = 'RTSIN';
    case HSSC = 'HSSC';




    public function info()
    {
        $lang = app()->getLocale();


        try {
            return match ($this) {
                self::CREATED => [
                    'label' => $lang === 'ar' ? 'تم الإنشاء' : 'Created',

                  
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#1565c0',
                    'description' => 'Shipment has been created in the system',
                ],
                self::EXPECTED_RECEIVE => [
                    'label' => $lang === 'ar' ? 'متوقع الاستلام' : 'Expected Receive',

                   
                    'icon' => 'fa-inbox',
                    'color_bg' => '#fff8e1',
                    'color_text' => '#f57f17',
                    'description' => 'AWB is expected to be received soon',
                ],
                self::DOCUMENT_UPLOAD => [
                    'label' => $lang === 'ar' ? 'رفع المستندات' : 'Document Upload',
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#99b7dcff',
                    'description' => 'Required shipment documents have been uploaded',
                ],


                self::PICKED_UP => [
                    'label' => $lang === 'ar' ? 'تم الاستلام' : 'Picked Up',
                  
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'Shipment has been picked up from sender',
                ],
                self::RECEIVED_OPERATION => [
                    'label' => $lang === 'ar' ? 'مستلم' : 'Received',
                   
                    'icon' => 'inbox',
                    'color_bg' => '#f0f9eb',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment received in operations',
                ],
                self::SHELF_IN => [
                    'label' => $lang === 'ar' ? 'دخول الرف' : 'Shelf In',
                   
                    'icon' => 'archive',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Shipment placed in storage shelf',
                ],
                self::SHELF_OUT => [
                    'label' => $lang === 'ar' ? 'خروج الرف' : 'Shelf Out',
                   
                    'icon' => 'archive',
                    'color_bg' => '#e1f5fe',
                    'color_text' => '#0288d1',
                    'description' => 'Shipment removed from storage shelf',
                ],
                self::HUB_IN => [
                    'label' => $lang === 'ar' ? 'استلام المحور' : 'Received Hub',
                  
                    'icon' => 'home',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#1565c0',
                    'description' => 'Shipment arrived at hub facility',
                ],
                self::HUB_OUT => [
                    'label' => $lang === 'ar' ? 'مغادرة المحور' : 'Hub Departure',
                 
                    'icon' => 'log-out',
                    'color_bg' => '#ffe0b2',
                    'color_text' => '#ef6c00',
                    'description' => 'Shipment departed from hub facility',
                ],
                self::OPERATION_INBOUND => [
                        'label' => $lang === 'ar' ? 'استلام العمليات' : 'Received Operations',
                       
                    'icon' => 'download',
                    'color_bg' => '#dcedc8',
                    'color_text' => '#558b2f',
                    'description' => 'Shipment received by operations team',
                ],

                self::OPERATION_OUTBOUND => [
                    'label' => $lang === 'ar' ? 'خروج العمليات' : 'Operations Out',
                    'icon' => 'upload',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Shipment dispatched by operations team',
                ],
                self::DELIVERED => [
                    'label' => $lang === 'ar' ? 'تم التسليم' : 'Delivered',
                    'icon' => 'check-circle',
                    'color_bg' => '#e8f5e9',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment successfully delivered',
                ],
                self::REVERSE_DELIEVERY => [
                    'label' => $lang === 'ar' ? 'تسليم عكسي' : 'Reverse Delivery',
                    'icon' => 'check-circle',
                    'color_bg' => '#e8f5e9',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment successfully reversed',
                ],
                self::DEBRIEFED_OPERATION => [
                    'label' => $lang === 'ar' ? 'تقرير العمليات' : 'Debrief Operation',
                    'icon' => 'check-circle',
                    'color_bg' => '#e8f5e9',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment successfully reversed',
                ],
                self::OPERATION_OUTSTANDING => [
                    'label' => $lang === 'ar' ? 'عمليات معلقة' : 'Operation Outstanding',
                    'icon' => 'check-circle',
                    'color_bg' => '#e8f5e9',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment successfully reversed',
                ],
                self::DEBRIEFED_FINANCE => [
                    'label' => $lang === 'ar' ? 'تقرير المالية' : 'Debrief Finance',
                    'icon' => 'check-circle',
                    'color_bg' => '#e8f5e9',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment successfully reversed',
                ],
                self::ADDRESS_CHANGED => [
                    'label' => $lang === 'ar' ? 'تم تغيير العنوان' : 'Address changed',
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#15c0a4ff',
                    'description' => 'Shipment created in system',
                ],
                self::ADDRESS_VALIDATED => [
                    'label' => $lang === 'ar' ? 'تم التحقق من العنوان' : 'Address validated',
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#d482d8ff',
                    'description' => 'Shipment created in system',
                ],
                self::UPDATED_DIMENSIONS => [
                    'label' => $lang === 'ar' ? 'تم تحديث الأبعاد' : 'Updated Dimensions',
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#82add8ff',
                    'description' => 'Shipment created in system',
                ],
                self::UPDATED_WEIGHT => [
                    'label' => $lang === 'ar' ? 'تم تحديث الوزن' : 'Updated Weight',
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#82add8ff',
                    'description' => 'Shipment created in system',
                ],
                self::CANCELLED => [
                    'label' => $lang === 'ar' ? 'ملغي' : 'Cancelled',
                    'icon' => 'x-circle',
                    'color_bg' => '#ffebee',
                    'color_text' => '#b71c1c',
                    'description' => 'Shipment cancelled',
                ],
                self::CHANGE_ROUTE => [
                    'label' => $lang === 'ar' ? 'تغيير المسار' : 'Change Route',
                    'icon' => 'file-plus',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#82add8ff',
                    'description' => 'Shipment changed route',
                ],
                self::OUT_FOR_DELIVERY => [
                    'label' => $lang === 'ar' ? 'خارج للتسليم' : 'Out for Delivery',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'Shipment is out with courier',
                ],
                self::SMS_SENT => [
                    'label' => $lang === 'ar' ? 'تم إرسال الرسالة' : 'SMS Sent',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'Shipment is out with courier',
                ],
                self::SHIPPER_CANCELED => [
                    'label' => $lang === 'ar' ? 'ملغي من المرسل' : 'Cancelled by shipper',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'shipment cancelled by shipper',
                ],
                self::IN_TRANSIT => [
                    'label' => $lang === 'ar' ? 'قيد النقل' : 'In Transit',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'shipment in transit',
                ],
                self::GATEWAY_INBOUND => [
                    'label' => $lang === 'ar' ? 'دخول البوابة' : 'Gateway inbound',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'shipment in gateway',
                ],
                self::GATEWAY_OUTBOUND => [
                    'label' => $lang === 'ar' ? 'خروج البوابة' : 'Gateway Outbound',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'shipment in gateway',
                ],
                self::HOLD_FOR_PICKUP => [
                    'label' => $lang === 'ar' ? 'معلق للاستلام' : 'Hold For Pickup',
                    'icon' => 'truck',
                    'color_bg' => '#e6f7ff',
                    'color_text' => '#007bff',
                    'description' => 'shipment in gateway',
                ],
                self::HOLD => [
                    'label' => $lang === 'ar' ? 'معلق' : 'On Hold',
                    'icon' => 'pause-circle',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#ef6c00',
                    'description' => 'Shipment placed on hold',
                ],
                self::RELEASE_HOLD => [
                    'label' => $lang === 'ar' ? 'إلغاء التعليق' : 'Release Hold',
                    'icon' => 'pause-circle',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#ef6c00',
                    'description' => 'Shipment placed on hold',
                ],
                self::HOLD_CUSTOMS => [
                    'label' => $lang === 'ar' ? 'معلق - الجمارك' : 'On Hold - Customs',
                    'icon' => 'shield',
                    'color_bg' => '#ede7f6',
                    'color_text' => '#4527a0',
                    'description' => 'Shipment on hold for customs clearance',
                ],
                self::RELEASE_CUSTOMS => [
                    'label' => $lang === 'ar' ? 'إطلاق الجمارك' : 'Release Customs',
                    'icon' => 'shield',
                    'color_bg' => '#ede7f6',
                    'color_text' => '#4527a0',
                    'description' => 'Shipment on hold for customs clearance',
                ],
                self::RETURN_IN => [
                    'label' => $lang === 'ar' ? 'إرجاع داخلي' : 'Return In',
                    'icon' => 'shield',
                    'color_bg' => '#ede7f6',
                    'color_text' => '#4527a0',
                    'description' => 'Shipment Returned in',
                ],
                self::RETURN_OUT => [
                    'label' => $lang === 'ar' ? 'إرجاع خارجي' : 'Return Out',
                    'icon' => 'shield',
                    'color_bg' => '#ede7f6',
                    'color_text' => '#4527a0',
                    'description' => 'Shipment Returned out',
                ],
                self::RETURN_TO_SHIPPER => [
                    'label' => $lang === 'ar' ? 'إرجاع للمرسل' : 'Return to shipper',
                    'icon' => 'shield',
                    'color_bg' => '#ede7f6',
                    'color_text' => '#4527a0',
                    'description' => 'Shipment Returned out',
                ],
                self::SCAN_RUNSHEET => [
                    'label' => $lang === 'ar' ? 'مسح الجدول' : 'Scan Runsheet',
                    'icon' => 'clipboard-list',
                    'color_bg' => '#f3e5f5',
                    'color_text' => '#6a1b9a',
                    'description' => 'Shipment scanned and assigned to runsheet',
                ],

                self::SCAN_RUNSHEET_UNASSIGNED => [
                    'label' => $lang === 'ar' ? 'جدول غير مخصص' : 'Unassigned Runsheet',
                    'icon' => 'clipboard-list',
                    'color_bg' => '#f3e5f5',
                    'color_text' => '#6a1b9a',
                    'description' => 'Shipment unassigned from the runsheet',
                ],
                self::SCAN_RUNSHEET_VERIFIED => [
                    'label' => $lang === 'ar' ? 'تم التحقق من الجدول' : 'Runsheet Verified',
                    'icon' => 'clipboard-check',
                    'color_bg' => '#e8f5e9',
                    'color_text' => '#2e7d32',
                    'description' => 'Shipment verification completed on runsheet',
                ],
                self::NOT_AVAILABLE_MOBILE_CLOSED => [
                    'label' => $lang === 'ar' ? 'الجوال مغلق' : 'Mobile Closed',
                    'icon' => 'phone-off',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Recipient mobile phone is closed, could not contact',
                ],
                self::NOT_AVAILABLE_NO_ANSWER => [
                    'label' => $lang === 'ar' ? 'لا يوجد رد' : 'No Answer',
                    'icon' => 'phone-missed',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Recipient did not answer the call',
                ],
                self::NOT_AVAILABLE_RESCHEDULE => [
                    'label' => $lang === 'ar' ? 'إعادة الجدولة' : 'Reschedule',
                    'icon' => 'calendar',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Delivery attempt needs to be rescheduled',
                ],
                self::NOT_AVAILABLE_TRAVELING => [
                    'label' => $lang === 'ar' ? 'مسافر' : 'Traveling',
                    'icon' => 'map-pin',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Recipient is traveling, cannot receive shipment',
                ],
                self::NOT_AVAILABLE_WRONG_PHONE => [
                    'label' => $lang === 'ar' ? 'رقم هاتف خاطئ' : 'Wrong Phone Number',
                    'icon' => 'phone-slash',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Delivery could not be completed due to wrong phone number',
                ],
                self::NOT_AVAILABLE_WRONG_CUSTOMER => [
                    'label' => $lang === 'ar' ? 'عميل خاطئ' : 'Wrong Customer',
                    'icon' => 'user-x',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Delivery could not be completed due to wrong recipient',
                ],
                self::NOT_AVAILABLE_WRONG_CITY => [
                    'label' => $lang === 'ar' ? 'مدينة خاطئة' : 'Wrong City',
                    'icon' => 'map',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Shipment address does not match city',
                ],
                self::NOT_AVAILABLE_ROUTE => [
                    'label' => $lang === 'ar' ? 'مشكلة في المسار' : 'Route Issue',
                    'icon' => 'map-signs',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Delivery route needs verification',
                ],
                self::NOT_AVAILABLE_OUT_OF_AREA => [
                    'label' => $lang === 'ar' ? 'خارج منطقة التسليم' : 'Out of Delivery Area',
                    'icon' => 'map-pin-off',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Delivery address is outside the service area',
                ],
                self::HOLD_PICKUP => [
                    'label' => $lang === 'ar' ? 'معلق للاستلام' : 'Hold for Pickup',
                    'icon' => 'archive',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Shipment is held for pickup by recipient',
                ],
                self::ATTEMPTED => [
                    'label' => $lang === 'ar' ? 'محاولة' : 'Attempted',
                    'icon' => 'alert-circle',
                    'color_bg' => '#fff4e5',
                    'color_text' => '#b36b00',
                    'description' => 'Delivery attempt made',
                ],
                self::REFUSED => [
                    'label' => $lang === 'ar' ? 'مرفوض' : 'Refused',
                    'icon' => 'x-circle',
                    'color_bg' => '#ffebee',
                    'color_text' => '#b71c1c',
                    'description' => 'Recipient refused the shipment',
                ],
                self::NOT_PICKED_UP_RESCHEDULE => [
                    'label' => $lang === 'ar' ? 'إعادة جدولة الاستلام' : 'Reschedule Pickup',
                    'icon' => 'calendar',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Pickup attempt needs to be rescheduled',
                ],
                self::NOT_PICKED_UP_NO_ANSWER => [
                    'label' => $lang === 'ar' ? 'لا يوجد رد' : 'No Answer',
                    'icon' => 'phone-missed',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Recipient did not answer during pickup attempt',
                ],
                self::NOT_PICKED_UP_ADDRESS_CHANGED => [
                    'label' => $lang === 'ar' ? 'تم تغيير العنوان' : 'Address Changed',
                    'icon' => 'map-pin',
                    'color_bg' => '#fff3e0',
                    'color_text' => '#fb8c00',
                    'description' => 'Pickup could not be completed due to address change',
                ],
                self::REFUSED_OPEN_SHIPMENT => [
                    'label' => $lang === 'ar' ? 'رفض فتح الشحنة' : 'Refused to Open Shipment',
                    'icon' => 'x-circle',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Recipient refused to open the shipment before delivery',
                ],
                self::REFUSED_NO_LONGER_NEEDED => [
                    'label' => $lang === 'ar' ? 'مرفوض - لم يعد مطلوباً' : 'Refused – No Longer Needed',
                    'icon' => 'x-circle',
                    'color_bg' => '#ffcdd2',
                    'color_text' => '#c62828',
                    'description' => 'Recipient refused shipment, no longer needed',
                ],
                self::OVERAGE => [
                    'label' => $lang === 'ar' ? 'فائض' : 'Overage',
                    'icon' => 'plus-circle',
                    'color_bg' => '#fbe9e7',
                    'color_text' => '#bf360c',
                    'description' => 'Extra shipment found',
                ],
                self::DAMAGED => [
                    'label' => $lang === 'ar' ? 'تالف' : 'Damaged',
                    'icon' => 'alert-octagon',
                    'color_bg' => '#ffebee',
                    'color_text' => '#c62828',
                    'description' => 'Shipment damaged',
                ],
                self::REFUSED_DELAYED => [
                    'label' => $lang === 'ar' ? 'مرفوض - متأخر' : 'Refused - Delayed',
                    'icon' => 'clock',
                    'color_bg' => '#e3f2fd',
                    'color_text' => '#1565c0',
                    'description' => 'Refused delivery due to delay',
                ],
                self::REFUSED_MONEY => [
                    'label' => $lang === 'ar' ? 'مرفوض - مشكلة مالية' : 'Refused - Money Issue',
                    'icon' => 'fa fa-money-bill-wave',
                    'color' => 'text-red-600',
                    'bg' => 'bg-red-100',
                    'description' => 'Shipment was refused due to payment or money-related issues.',
                ],

                self::REFUSED_ALREADY_RECEIVED => [
                    'label' => $lang === 'ar' ? 'مرفوض - تم الاستلام مسبقاً' : 'Refused - Already Received',
                    'icon' => 'fa fa-box-open',
                    'color' => 'text-red-600',
                    'bg' => 'bg-red-100',
                    'description' => 'Recipient refused the shipment, claiming they have already received it.',
                ],
                self::CANCELLED => [
                    'label' => $lang === 'ar' ? 'ملغي' : 'Cancelled',
                    'icon' => 'x-circle',
                    'color_bg' => '#ffebee',
                    'color_text' => '#b71c1c',
                    'description' => 'Shipment cancelled',
                ],
                self::LOST => [
                    'label' => $lang === 'ar' ? 'مفقود' : 'Lost',
                    'icon' => 'help-circle',
                    'color_bg' => '#f3e5f5',
                    'color_text' => '#6a1b9a',
                    'description' => 'Shipment lost in transit',
                ],
                self::CLOSED => [
                    'label' => $lang === 'ar' ? 'مغلق' : 'Closed',
                    'icon' => 'lock',
                    'color_bg' => '#eeeeee',
                    'color_text' => '#424242',
                    'description' => 'Shipment closed',
                ],
                self::CRN_IN => [
                    'label' => $lang === 'ar' ? 'دخول CRN' : 'CRN In',
                    'icon' => 'check-circle',
                    'color_bg' => '#c8e6c9',
                    'color_text' => '#000000',
                    'description' => 'Shipment successfully scanned in CRN'
                ],
                self::CRN_OUT => [
                    'label' => $lang === 'ar' ? 'خروج CRN' : 'CRN Out',
                    'icon' => 'check-circle',
                    'color_bg' => '#c8e6c9',
                    'color_text' => '#000000',
                    'description' => 'Shipment successfully scanned out of CRN'
                ],


                //old statuses:
                self::AF => ['label' => 'AF', 'icon' => 'file-plus', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Arrived Delivery Facility'],
                self::CC => ['label' => 'CC', 'icon' => 'file-plus', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Processing for Consignee Collection'],
                self::CR => ['label' => 'CR', 'icon' => 'file-check', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Customs Released'],
                self::DE => ['label' => 'DE', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delivery Exception'],
                self::DEX03 => ['label' => 'DEX03', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Has Undeliverable Address'],
                self::DEX08 => ['label' => 'DEX08', 'icon' => 'clock', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Delivery Attempted - Customer Not Available/Delivery Rescheduled'],
                self::DEX14 => ['label' => 'DEX14', 'icon' => 'rotate-cw', 'color_bg' => '#f3e5f5', 'color_text' => '#6a1b9a', 'description' => 'Shipment Is Under Return Process To SMSA Facility'],
                self::DEX29 => ['label' => 'DEX29', 'icon' => 'corner-up-right', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Shipmet Is Being Rerouted To New Address'],
                self::DEX41 => ['label' => 'DEX41', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At SMSA Facility'],
                self::DEX42 => ['label' => 'DEX42', 'icon' => 'calendar', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Customer Address Is Closed for Holiday/Weekend - Delivery Will Be Attempted On Next Business Day'],
                self::DEX93 => ['label' => 'DEX93', 'icon' => 'dollar-sign', 'color_bg' => '#ffebee', 'color_text' => '#c62828', 'description' => 'Attempted Delivery - Unable To Collect Charges From The Customer'],
                self::DF => ['label' => 'DF', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Departed Facility'],
                self::DL => ['label' => 'DL', 'icon' => 'check-circle', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Delivered'],
                self::HIP => ['label' => 'HIP', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Received at SMSA Sorting Facility'],
                self::HOP => ['label' => 'HOP', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed SMSA Sorting Facility'],
                self::IC => ['label' => 'IC', 'icon' => 'globe', 'color_bg' => '#f1f8e9', 'color_text' => '#33691e', 'description' => 'Shipment Arrived at Destination Country - Under clearance process'],
                self::OD => ['label' => 'OD', 'icon' => 'map-pin', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'Out for Delivery'],
                self::PU => ['label' => 'PU', 'icon' => 'package', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Picked up from the shipper. Delivery is estimated within 10 days from the pick up day.'],
                self::RTIN => ['label' => 'RTIN', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Awaiting Collection at Retail Center'],
                self::RTOPS => ['label' => 'RTOPS', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Collected from Retail'],
                self::SOP => ['label' => 'SOP', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed SMSA Origin Facility'],
                self::ST44 => ['label' => 'ST44', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At SMSA Facility'],
                self::ST68 => ['label' => 'ST68', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'In Transit'],
                self::ST77 => ['label' => 'ST77', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed Origin Country'],
                self::UTI => ['label' => 'UTI', 'icon' => 'alert-circle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment On Hold - Attempting For Customer Address Validation'],
                self::UTLX => ['label' => 'UTLX', 'icon' => 'phone', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'At SMSA Facility for calling'],
                self::CD => ['label' => 'CD', 'icon' => 'file-alert', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Clearance Delay'],
                self::ST41 => ['label' => 'ST41', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At SMSA Retail Center'],
                self::ST64 => ['label' => 'ST64', 'icon' => 'file-check', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Shipment Is Under Processign For Customer Broker Clearance'],
                self::DEX8X => ['label' => 'DEX8X', 'icon' => 'clock', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Recipient not available at residence'],
                self::DEX09 => ['label' => 'DEX09', 'icon' => 'check-circle', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Delivered'],
                self::BA => ['label' => 'BA', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Undeliverable Address'],
                self::CA => ['label' => 'CA', 'icon' => 'lock', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Closed on Arrival'],
                self::FD => ['label' => 'FD', 'icon' => 'corner-right-up', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Forwarded for delivery'],
                self::NH => ['label' => 'NH', 'icon' => 'clock', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Recipient not available at residence'],
                self::MS => ['label' => 'MS', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Miss sort at facility'],
                self::OH => ['label' => 'OH', 'icon' => 'alert-circle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'On hold for customs'],
                self::RD => ['label' => 'RD', 'icon' => 'refresh-cw', 'color_bg' => '#f3e5f5', 'color_text' => '#6a1b9a', 'description' => 'Rerouted Delivery'],
                self::WC => ['label' => 'WC', 'icon' => 'calendar', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Delivery Rescheduled'],
                self::PRC => ['label' => 'PRC', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Processing for Return to Customer'],
                self::TN => ['label' => 'TN', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Transit Notification'],
                self::POD => ['label' => 'POD', 'icon' => 'check-circle', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Proof of Delivery'],
                self::RPMX => ['label' => 'RPMX', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Return Processed to SMSA Facility'],
                self::DEX03_1 => ['label' => 'DEX03-1', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Correction Needed'],
                self::DEX03_2 => ['label' => 'DEX03-2', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Not Available'],
                self::DEX03_3 => ['label' => 'DEX03-3', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Wrong Address Provided'],
                self::DEX03_4 => ['label' => 'DEX03-4', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Incomplete Address'],
                self::DEX03_7 => ['label' => 'DEX03-7', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Incorrect Postal Code'],
                self::DEX03_8 => ['label' => 'DEX03-8', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Requires Verification'],
                self::DEX03_9 => ['label' => 'DEX03-9', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Recipient Unreachable'],
                self::DEX03_10 => ['label' => 'DEX03-10', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Not Found'],
                self::DEX03_12 => ['label' => 'DEX03-12', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Refused Delivery'],
                self::DEX03_13 => ['label' => 'DEX03-13', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Returned to Origin'],
                self::DEX03_14 => ['label' => 'DEX03-14', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Correction Complete'],
                self::DEX03_15 => ['label' => 'DEX03-15', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delivery Attempt Failed'],
                self::DEX03_16 => ['label' => 'DEX03-16', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Not Responding'],
                self::DEX07 => ['label' => 'DEX07', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Delivery In Progress'],
                self::DEX07_3 => ['label' => 'DEX07-3', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment In Transit'],
                self::DEX07_4 => ['label' => 'DEX07-4', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment At Hub'],
                self::DEX07_5 => ['label' => 'DEX07-5', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed Hub'],
                self::DEX07_6 => ['label' => 'DEX07-6', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Arrived Hub'],
                self::DEX07_7 => ['label' => 'DEX07-7', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Processing At Facility'],
                self::DEX07_8 => ['label' => 'DEX07-8', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Ready For Delivery'],
                self::DEX93_1 => ['label' => 'DEX93-1', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Unable to Deliver'],
                self::DEX93_2 => ['label' => 'DEX93-2', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Attempted Delivery'],
                self::DEX93_3 => ['label' => 'DEX93-3', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delivery Postponed'],
                self::DEX93_4 => ['label' => 'DEX93-4', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Unavailable'],
                self::DEX93_5 => ['label' => 'DEX93-5', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Recipient Refused Delivery'],
                self::RTS => ['label' => 'RTS', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Returned To Shipper'],
                self::RTI => ['label' => 'RTI', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Return To Inventory'],
                self::ST50 => ['label' => 'ST50', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At Facility'],
                self::Data => ['label' => 'Data', 'icon' => 'database', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'Data Entry Process'],
                self::RTO => ['label' => 'RTO', 'icon' => 'corner-up-left', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Return To Origin'],
                self::SMS => ['label' => 'SMS', 'icon' => 'message-circle', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'SMS Sent to Customer'],
                self::DEX17 => ['label' => 'DEX17', 'icon' => 'file-alert', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Damaged'],
                self::PUX43 => ['label' => 'PUX43', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Scheduled'],
                self::ADV => ['label' => 'ADV', 'icon' => 'alert-circle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Advice Given'],
                self::PUX_17 => ['label' => 'PUX-17', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Processed'],
                self::PUX03_1 => ['label' => 'PUX03-1', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Attempted'],
                self::PUX3 => ['label' => 'PUX3', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Confirmed'],
                self::ST60_10 => ['label' => 'ST60-10', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment In Transit'],
                self::ST60_5 => ['label' => 'ST60-5', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Processing'],
                self::ST60_6 => ['label' => 'ST60-6', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Arrived Destination Hub'],
                self::BTRO => ['label' => 'BTRO', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Back To Origin'],
                self::BTRI => ['label' => 'BTRI', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Back To Inventory'],

                // Additional status codes from scans.json (not duplicates)
                self::HAL => ['label' => 'HAL', 'icon' => 'archive', 'color_bg' => '#fff3e0', 'color_text' => '#fb8c00', 'description' => 'Hold At Location'],
                self::DEX34 => ['label' => 'DEX34', 'icon' => 'trash-2', 'color_bg' => '#ffebee', 'color_text' => '#c62828', 'description' => 'Destroyed At Customer Request'],
                self::DEX84 => ['label' => 'DEX84', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delay Caused Beyond Our Control'],
                self::CODRTS => ['label' => 'CODRTS', 'icon' => 'dollar-sign', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'COD Return To Shipper'],
                self::RTSIN => ['label' => 'RTSIN', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Return To Shipper In'],
                self::HSSC => ['label' => 'HSSC', 'icon' => 'users', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Handed Over To SSC'],
            };
        } catch (UnhandledMatchError $e) {
            // dump the unhandled enum value
            dd('Unhandled enum value: ' . $this->value);
        }
    }

    public function  infoHub(): array
    {
        return match ($this) {
            self::CREATED => [
                'label' => 'Created',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment has been created in the system',
            ],
            self::EXPECTED_RECEIVE => [
                'label' => 'Expected Receive',
                'icon' => 'fa-inbox',
                'color_bg' => '#fff8e1',
                'color_text' => '#f57f17',
                'description' => 'AWB is expected to be received soon',
            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment has been picked up from sender',
            ],
            self::RECEIVED_OPERATION => [
                'label' => 'Received',
                'icon' => 'inbox',
                'color_bg' => '#f0f9eb',
                'color_text' => '#2e7d32',
                'description' => 'Shipment received in operations',
            ],

            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],
            self::SHELF_IN => [
                'label' => 'Shelf In',
                'icon' => 'archive',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Shipment placed in storage shelf',
            ],
            self::SHELF_OUT => [
                'label' => 'Shelf Out',
                'icon' => 'archive',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0288d1',
                'description' => 'Shipment removed from storage shelf',
            ],
            self::HUB_IN => [
                'label' => 'Received Hub',
                'icon' => 'home',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment arrived at hub facility',
            ],
            self::HUB_OUT => [
                'label' => 'Hub Departure',
                'icon' => 'log-out',
                'color_bg' => '#ffe0b2',
                'color_text' => '#ef6c00',
                'description' => 'Shipment departed from hub facility',
            ],
            self::OPERATION_INBOUND => [
                'label' => 'Received Operations',
                'icon' => 'download',
                'color_bg' => '#dcedc8',
                'color_text' => '#558b2f',
                'description' => 'Shipment received by operations team',
            ],
            self::OPERATION_OUTBOUND => [
                'label' => 'Operations Out',
                'icon' => 'upload',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Shipment dispatched by operations team',
            ],
            self::DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully delivered',
            ],
            self::REVERSE_DELIEVERY => [
                'label' => 'Reverse Delivery',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::DEBRIEFED_OPERATION => [
                'label' => 'Debrief Operation',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::OPERATION_OUTSTANDING => [
                'label' => 'Operation Outstanding',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::DEBRIEFED_FINANCE => [
                'label' => 'Debrief Finance',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],

            self::ADDRESS_CHANGED => [
                'label' => 'Address changed',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#15c0a4ff',
                'description' => 'Shipment created in system',
            ],
            self::ADDRESS_VALIDATED => [
                'label' => 'Address validated',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#d482d8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_DIMENSIONS => [
                'label' => 'Updated Dimensions',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_WEIGHT => [
                'label' => 'Updated Weight',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Shipment cancelled',
            ],
            self::CHANGE_ROUTE => [
                'label' => 'Change Route',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment changed route',
            ],
            self::OUT_FOR_DELIVERY => [
                'label' => 'Pending Scan',
                'icon' => 'fa-hourglass-half',
                'color_bg' => '#9e9e9e',
                'color_text' => '#ffffff',
                'description' => 'MAWB is out'
            ],
            self::SMS_SENT => [
                'label' => 'SMS Sent',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment is out with courier',
            ],
            self::SHIPPER_CANCELED => [
                'label' => 'Cancelled by shipper',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment cancelled by shipper',
            ],
            self::IN_TRANSIT => [
                'label' => 'In Transit',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in transit',
            ],
            self::GATEWAY_INBOUND => [
                'label' => 'Gateway inbound',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::GATEWAY_OUTBOUND => [
                'label' => 'Gateway Outbound',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::HOLD_FOR_PICKUP => [
                'label' => 'Hold For Pickup',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment is held for pickup by recipient',
            ],
            self::HOLD => [
                'label' => 'On Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],
            self::RELEASE_HOLD => [
                'label' => 'Release Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],
            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::RELEASE_CUSTOMS => [
                'label' => 'Release Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::RETURN_IN => [
                'label' => 'Return In',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned in',
            ],
            self::RETURN_OUT => [
                'label' => 'Return Out',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned out',
            ],
            self::RETURN_TO_SHIPPER => [
                'label' => 'Return to shipper',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned out',
            ],
            self::SCAN_RUNSHEET => [
                'label' => 'Scan Runsheet',
                'icon' => 'clipboard-list',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment scanned and assigned to runsheet',
            ],

            self::SCAN_RUNSHEET_UNASSIGNED => [
                'label' => 'Unassigned Runsheet',
                'icon' => 'clipboard-list',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment unassigned from the runsheet',
            ],
            self::SCAN_RUNSHEET_VERIFIED => [
                'label' => 'Runsheet Verified',
                'icon' => 'clipboard-check',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment verification completed on runsheet',
            ],
            self::NOT_AVAILABLE_MOBILE_CLOSED => [
                'label' => 'Mobile Closed',
                'icon' => 'phone-off',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient mobile phone is closed, could not contact',
            ],
            self::NOT_AVAILABLE_NO_ANSWER => [
                'label' => 'No Answer',
                'icon' => 'phone-missed',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient did not answer the call',
            ],
            self::NOT_AVAILABLE_RESCHEDULE => [
                'label' => 'Reschedule',
                'icon' => 'calendar',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Delivery attempt needs to be rescheduled',
            ],
            self::NOT_AVAILABLE_TRAVELING => [
                'label' => 'Traveling',
                'icon' => 'map-pin',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient is traveling, cannot receive shipment',
            ],
            self::NOT_AVAILABLE_WRONG_PHONE => [
                'label' => 'Wrong Phone Number',
                'icon' => 'phone-slash',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery could not be completed due to wrong phone number',
            ],
            self::NOT_AVAILABLE_WRONG_CUSTOMER => [
                'label' => 'Wrong Customer',
                'icon' => 'user-x',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery could not be completed due to wrong recipient',
            ],
            self::NOT_AVAILABLE_WRONG_CITY => [
                'label' => 'Wrong City',
                'icon' => 'map',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Shipment address does not match city',
            ],
            self::NOT_AVAILABLE_ROUTE => [
                'label' => 'Route Issue',
                'icon' => 'map-signs',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery route needs verification',
            ],
            self::NOT_AVAILABLE_OUT_OF_AREA => [
                'label' => 'Out of Delivery Area',
                'icon' => 'map-pin-off',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery address is outside the service area',
            ],
            self::HOLD_PICKUP => [
                'label' => 'Hold for Pickup',
                'icon' => 'archive',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Shipment is held for pickup by recipient',
            ],
            self::UPDATED_DIMENSIONS => [
                'label' => 'Updated Dimensions',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_WEIGHT => [
                'label' => 'Updated Weight',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::REFUSED => [
                'label' => 'Refused',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Recipient refused the shipment',
            ],

            // NOT PICKED UP STATUSES
            self::NOT_PICKED_UP_RESCHEDULE => [
                'label' => 'Reschedule Pickup',
                'icon' => 'calendar',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Pickup attempt needs to be rescheduled',
            ],
            self::NOT_PICKED_UP_NO_ANSWER => [
                'label' => 'No Answer',
                'icon' => 'phone-missed',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient did not answer during pickup attempt',
            ],
            self::NOT_PICKED_UP_ADDRESS_CHANGED => [
                'label' => 'Address Changed',
                'icon' => 'map-pin',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Pickup could not be completed due to address change',
            ],

            // REFUSED STATUSES
            self::REFUSED_OPEN_SHIPMENT => [
                'label' => 'Refused to Open Shipment',
                'icon' => 'x-circle',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Recipient refused to open the shipment before delivery',
            ],
            self::REFUSED_MONEY => [
                'label' => 'Refused - Money Issue',
                'icon' => 'fa fa-money-bill-wave',
                'color' => 'text-red-600',
                'bg' => 'bg-red-100',
                'description' => 'Shipment was refused due to payment or money-related issues.',
            ],

            self::REFUSED_ALREADY_RECEIVED => [
                'label' => 'Refused - Already Received',
                'icon' => 'fa fa-box-open',
                'color' => 'text-red-600',
                'bg' => 'bg-red-100',
                'description' => 'Recipient refused the shipment, claiming they have already received it.',
            ],
            self::REFUSED_NO_LONGER_NEEDED => [
                'label' => 'Refused – No Longer Needed',
                'icon' => 'x-circle',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Recipient refused shipment, no longer needed',
            ],
            self::OVERAGE => [
                'label' => 'Overage',
                'icon' => 'plus-circle',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Extra shipment found',
            ],



            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],

            self::REFUSED_DELAYED => [
                'label' => 'Refused - Delayed',
                'icon' => 'clock',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Refused delivery due to delay',
            ],

            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],

            self::DAMAGED => [
                'label' => 'Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment damaged',
            ],

            self::LOST => [
                'label' => 'Lost',
                'icon' => 'help-circle',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment lost in transit',
            ],

            self::CLOSED => [
                'label' => 'Closed',
                'icon' => 'lock',
                'color_bg' => '#eeeeee',
                'color_text' => '#424242',
                'description' => 'Shipment closed',
            ],


            self::CRN_IN => [
                'label' => 'CRN In',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'Shipment successfully scanned in CRN'
            ],
            self::CRN_OUT => [
                'label' => 'CRN Out',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'Shipment successfully scanned out of CRN'
            ],

            self::AF => ['label' => 'AF', 'icon' => 'file-plus', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Arrived Delivery Facility'],
            self::CC => ['label' => 'CC', 'icon' => 'file-plus', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Processing for Consignee Collection'],
            self::CR => ['label' => 'CR', 'icon' => 'file-check', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Customs Released'],
            self::DE => ['label' => 'DE', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delivery Exception'],
            self::DEX03 => ['label' => 'DEX03', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Has Undeliverable Address'],
            self::DEX08 => ['label' => 'DEX08', 'icon' => 'clock', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Delivery Attempted - Customer Not Available/Delivery Rescheduled'],
            self::DEX14 => ['label' => 'DEX14', 'icon' => 'rotate-cw', 'color_bg' => '#f3e5f5', 'color_text' => '#6a1b9a', 'description' => 'Shipment Is Under Return Process To SMSA Facility'],
            self::DEX29 => ['label' => 'DEX29', 'icon' => 'corner-up-right', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Shipmet Is Being Rerouted To New Address'],
            self::DEX41 => ['label' => 'DEX41', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At SMSA Facility'],
            self::DEX42 => ['label' => 'DEX42', 'icon' => 'calendar', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Customer Address Is Closed for Holiday/Weekend - Delivery Will Be Attempted On Next Business Day'],
            self::DEX93 => ['label' => 'DEX93', 'icon' => 'dollar-sign', 'color_bg' => '#ffebee', 'color_text' => '#c62828', 'description' => 'Attempted Delivery - Unable To Collect Charges From The Customer'],
            self::DF => ['label' => 'DF', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Departed Facility'],
            self::DL => ['label' => 'DL', 'icon' => 'check-circle', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Delivered'],
            self::HIP => ['label' => 'HIP', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Received at SMSA Sorting Facility'],
            self::HOP => ['label' => 'HOP', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed SMSA Sorting Facility'],
            self::IC => ['label' => 'IC', 'icon' => 'globe', 'color_bg' => '#f1f8e9', 'color_text' => '#33691e', 'description' => 'Shipment Arrived at Destination Country - Under clearance process'],
            self::OD => ['label' => 'OD', 'icon' => 'map-pin', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'Out for Delivery'],
            self::PU => ['label' => 'PU', 'icon' => 'package', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Picked up from the shipper. Delivery is estimated within 10 days from the pick up day.'],
            self::RTIN => ['label' => 'RTIN', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Awaiting Collection at Retail Center'],
            self::RTOPS => ['label' => 'RTOPS', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Collected from Retail'],
            self::SOP => ['label' => 'SOP', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed SMSA Origin Facility'],
            self::ST44 => ['label' => 'ST44', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At SMSA Facility'],
            self::ST68 => ['label' => 'ST68', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'In Transit'],
            self::ST77 => ['label' => 'ST77', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed Origin Country'],
            self::UTI => ['label' => 'UTI', 'icon' => 'alert-circle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment On Hold - Attempting For Customer Address Validation'],
            self::UTLX => ['label' => 'UTLX', 'icon' => 'phone', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'At SMSA Facility for calling'],
            self::CD => ['label' => 'CD', 'icon' => 'file-alert', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Clearance Delay'],
            self::ST41 => ['label' => 'ST41', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At SMSA Retail Center'],
            self::ST64 => ['label' => 'ST64', 'icon' => 'file-check', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Shipment Is Under Processign For Customer Broker Clearance'],
            self::DEX8X => ['label' => 'DEX8X', 'icon' => 'clock', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Recipient not available at residence'],
            self::DEX09 => ['label' => 'DEX09', 'icon' => 'check-circle', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Delivered'],
            self::BA => ['label' => 'BA', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Undeliverable Address'],
            self::CA => ['label' => 'CA', 'icon' => 'lock', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Closed on Arrival'],
            self::FD => ['label' => 'FD', 'icon' => 'corner-right-up', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Forwarded for delivery'],
            self::NH => ['label' => 'NH', 'icon' => 'clock', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Recipient not available at residence'],
            self::MS => ['label' => 'MS', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Miss sort at facility'],
            self::OH => ['label' => 'OH', 'icon' => 'alert-circle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'On hold for customs'],
            self::RD => ['label' => 'RD', 'icon' => 'refresh-cw', 'color_bg' => '#f3e5f5', 'color_text' => '#6a1b9a', 'description' => 'Rerouted Delivery'],
            self::WC => ['label' => 'WC', 'icon' => 'calendar', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'Delivery Rescheduled'],
            self::PRC => ['label' => 'PRC', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Processing for Return to Customer'],
            self::TN => ['label' => 'TN', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Transit Notification'],
            self::POD => ['label' => 'POD', 'icon' => 'check-circle', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Proof of Delivery'],
            self::RPMX => ['label' => 'RPMX', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Return Processed to SMSA Facility'],
            self::DEX03_1 => ['label' => 'DEX03-1', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Correction Needed'],
            self::DEX03_2 => ['label' => 'DEX03-2', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Not Available'],
            self::DEX03_3 => ['label' => 'DEX03-3', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Wrong Address Provided'],
            self::DEX03_4 => ['label' => 'DEX03-4', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Incomplete Address'],
            self::DEX03_7 => ['label' => 'DEX03-7', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Incorrect Postal Code'],
            self::DEX03_8 => ['label' => 'DEX03-8', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Requires Verification'],
            self::DEX03_9 => ['label' => 'DEX03-9', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Recipient Unreachable'],
            self::DEX03_10 => ['label' => 'DEX03-10', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Not Found'],
            self::DEX03_12 => ['label' => 'DEX03-12', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Refused Delivery'],
            self::DEX03_13 => ['label' => 'DEX03-13', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Returned to Origin'],
            self::DEX03_14 => ['label' => 'DEX03-14', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Address Correction Complete'],
            self::DEX03_15 => ['label' => 'DEX03-15', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delivery Attempt Failed'],
            self::DEX03_16 => ['label' => 'DEX03-16', 'icon' => 'map-pin-off', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Not Responding'],
            self::DEX07 => ['label' => 'DEX07', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Delivery In Progress'],
            self::DEX07_3 => ['label' => 'DEX07-3', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment In Transit'],
            self::DEX07_4 => ['label' => 'DEX07-4', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment At Hub'],
            self::DEX07_5 => ['label' => 'DEX07-5', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Departed Hub'],
            self::DEX07_6 => ['label' => 'DEX07-6', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Arrived Hub'],
            self::DEX07_7 => ['label' => 'DEX07-7', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Processing At Facility'],
            self::DEX07_8 => ['label' => 'DEX07-8', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Ready For Delivery'],
            self::DEX93_1 => ['label' => 'DEX93-1', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Unable to Deliver'],
            self::DEX93_2 => ['label' => 'DEX93-2', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Attempted Delivery'],
            self::DEX93_3 => ['label' => 'DEX93-3', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delivery Postponed'],
            self::DEX93_4 => ['label' => 'DEX93-4', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Customer Unavailable'],
            self::DEX93_5 => ['label' => 'DEX93-5', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Recipient Refused Delivery'],
            self::RTS => ['label' => 'RTS', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Returned To Shipper'],
            self::RTI => ['label' => 'RTI', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Return To Inventory'],
            self::ST50 => ['label' => 'ST50', 'icon' => 'home', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'At Facility'],
            self::Data => ['label' => 'Data', 'icon' => 'database', 'color_bg' => '#e0f7fa', 'color_text' => '#006064', 'description' => 'Data Entry Process'],
            self::RTO => ['label' => 'RTO', 'icon' => 'corner-up-left', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Return To Origin'],
            self::SMS => ['label' => 'SMS', 'icon' => 'message-circle', 'color_bg' => '#fffde7', 'color_text' => '#f9a825', 'description' => 'SMS Sent to Customer'],
            self::DEX17 => ['label' => 'DEX17', 'icon' => 'file-alert', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Shipment Damaged'],
            self::PUX43 => ['label' => 'PUX43', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Scheduled'],
            self::ADV => ['label' => 'ADV', 'icon' => 'alert-circle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Advice Given'],
            self::PUX_17 => ['label' => 'PUX-17', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Processed'],
            self::PUX03_1 => ['label' => 'PUX03-1', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Attempted'],
            self::PUX3 => ['label' => 'PUX3', 'icon' => 'package', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Pickup Confirmed'],
            self::ST60_10 => ['label' => 'ST60-10', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment In Transit'],
            self::ST60_5 => ['label' => 'ST60-5', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Processing'],
            self::ST60_6 => ['label' => 'ST60-6', 'icon' => 'truck', 'color_bg' => '#e3f2fd', 'color_text' => '#1565c0', 'description' => 'Shipment Arrived Destination Hub'],
            self::BTRO => ['label' => 'BTRO', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Back To Origin'],
            self::BTRI => ['label' => 'BTRI', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Back To Inventory'],

            // Additional status codes from scans.json (not duplicates)
            self::HAL => ['label' => 'HAL', 'icon' => 'archive', 'color_bg' => '#fff3e0', 'color_text' => '#fb8c00', 'description' => 'Hold At Location'],
            self::DEX34 => ['label' => 'DEX34', 'icon' => 'trash-2', 'color_bg' => '#ffebee', 'color_text' => '#c62828', 'description' => 'Destroyed At Customer Request'],
            self::DEX84 => ['label' => 'DEX84', 'icon' => 'alert-triangle', 'color_bg' => '#fff3e0', 'color_text' => '#ef6c00', 'description' => 'Delay Caused Beyond Our Control'],
            self::CODRTS => ['label' => 'CODRTS', 'icon' => 'dollar-sign', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'COD Return To Shipper'],
            self::RTSIN => ['label' => 'RTSIN', 'icon' => 'archive', 'color_bg' => '#e1f5fe', 'color_text' => '#0277bd', 'description' => 'Return To Shipper In'],
            self::HSSC => ['label' => 'HSSC', 'icon' => 'users', 'color_bg' => '#e8f5e9', 'color_text' => '#2e7d32', 'description' => 'Handed Over To SSC'],
        };
    }



    public static function statuses(): array
    {
        $exclude = [
            'AF',
            'CC',
            'CR',
            'DE',
            'DEX03',
            'DEX08',
            'DEX14',
            'DEX29',
            'DEX41',
            'DEX42',
            'DEX93',
            'DF',
            'DL',
            'HIP',
            'HOP',
            'IC',
            'OD',
            'PU',
            'RTIN',
            'RTOPS',
            'SOP',
            'ST44',
            'ST68',
            'ST77',
            'UTI',
            'UTLX',
            'CD',
            'ST41',
            'ST64',
            'DEX8X',
            'DEX09',
            'BA',
            'CA',
            'FD',
            'NH',
            'MS',
            'OH',
            'RD',
            'WC',
            'PRC',
            'TN',
            'POD',
            'RPMX',
            'DEX03-1',
            'DEX03-2',
            'DEX03-3',
            'DEX03-4',
            'DEX03-7',
            'DEX03-8',
            'DEX03-9',
            'DEX03-10',
            'DEX03-12',
            'DEX03-13',
            'DEX03-14',
            'DEX03-15',
            'DEX03-16',
            'DEX07',
            'DEX07-3',
            'DEX07-4',
            'DEX07-5',
            'DEX07-6',
            'DEX07-7',
            'DEX07-8',
            'DEX93-1',
            'DEX93-2',
            'DEX93-3',
            'DEX93-4',
            'DEX93-5',
            'RTS',
            'RTI',
            'ST50',
            'Data',
            'RTO',
            'SMS',
            'DEX17',
            'PUX43',
            'ADV',
            'PUX-17',
            'PUX03-1',
            'PUX3',
            'ST60-10',
            'ST60-5',
            'ST60-6',
            'BTRO',
            'BTRI',
            'HAL',
            'DEX34',
            'DEX84',
            'CODRTS',
            'RTSIN',
            'HSSC'
        ];

        return array_values(array_filter(
            array_map(fn($case) => $case->value, self::cases()),
            fn($value) => !in_array($value, $exclude)
        ));
    }
}