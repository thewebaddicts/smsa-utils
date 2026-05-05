<?php

namespace twa\smsautils\Classes\AWB;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use twa\smsautils\Models\ShipmentStatus;

class AwbStatus
{

    public $status;
    public $identifier;
    public $db;


    public function __construct(?string $status = null, ?string $identifier = null, ?ShipmentStatus $db = null)
    {
        $this->status = $status;
        $this->identifier = $identifier;

        if ($db) {
            $this->db = $db;
        } elseif ($this->status) {

            $cacheKey = "awb_status_code_{$this->status}";

            $this->db = Cache::rememberForever($cacheKey, function () {
                return ShipmentStatus::query()
                    ->where('code', $this->status)
                    ->whereNull('deleted_at')
                    ->first();
            });
        } elseif ($this->identifier) {
            $cacheKey = "awb_status_identifier_{$this->identifier}";

            $this->db = Cache::rememberForever($cacheKey, function () {
                return ShipmentStatus::query()
                    ->where('identifier', $this->identifier)
                    ->whereNull('deleted_at')
                    ->first();
            });
        }
    }


    public function value(): string
    {
        return $this->db->code;
    }


    public function info(): array
    {


        $lang = app()->getLocale();


        if (!$this->db) {
            return [
                'label' => 'Unknown',
                'icon' => 'file-question',
                'color_bg' => '#eeeeee',
                'color_text' => '#616161',
                'description' => 'Status not found',
                'category' => null,
                'tags' => ['all'],
            ];
        }

        $status = $this->db;

        return [
            'label' => $lang === 'ar' ? $status->label_ar ?? '' : $status->label_en ?? '',
            'icon' => $status->icon ?? 'file-plus',
            'color_bg' => $status->color_bg ?? '#e3f2fd',
            'color_text' => $status->color_text ?? '#1565c0',
            'description' => $lang === 'ar' ? ($status->description_ar ?? '') : ($status->description_en ?? ''),
            'category' => $status->shipment_status_category_id ?? null,
            'tags' => $status->shipment_status_tags ? json_decode($status->shipment_status_tags, true) : ['all'],
        ];
    }
}
