<?php

namespace twa\smsautils\Classes\AWB;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AwbStatus
{
    public function __construct(public string $status) {}

    public function info(): array
    {
        $lang = app()->getLocale();
        $cacheKey = "awb_status_{$this->status}";

        $status = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return DB::table('shipment_statuses')
                ->where('code', $this->status)
                ->whereNull('deleted_at')
                ->first();
        });


        if (!$status) {
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


        return [
            'label' => $lang === 'ar' ? $status->label_ar ?? '' : $status->label_en ?? '',
            'icon' => $status->icon ?? 'file-plus',
            'color_bg' => $status->color_bg ?? '#e3f2fd',
            'color_text' => $status->color_text ?? '#1565c0',
            'description' => $status->description ?? '',
            'category' => $status->category ?? null,
            'tags' => $status->tags ? json_decode($status->tags, true) : ['all'],
        ];
    }
}
