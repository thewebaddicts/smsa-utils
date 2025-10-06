<?php

namespace twa\smsautils\Enums;

enum PickupStatusEnum: string
{
    case PENDING = 'pending';
    case PICKED_UP = 'picked_up';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case DELAYED = 'delayed';
    case COMPLETED = 'completed';

    public function info(): array
    {
        return match ($this) {
            self::PENDING => [
                'label' => 'Pending',
                "icon" => "calendar",
                "color_bg" => "#FFF4E6", // soft orange background
                "color_text" => "#D97706", // amber-700
                'description' => 'Pending'
            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                "icon" => "truck",
                "color_bg" => "#E0F2FE", // light blue background
                "color_text" => "#0369A1", // sky-800
                'description' => 'Picked Up'
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                "icon" => "x",
                "color_bg" => "#FEE2E2", // light red background
                "color_text" => "#B91C1C", // red-700
                'description' => 'Cancelled'
            ],
            self::FAILED => [
                'label' => 'Failed',
                "icon" => "alert-triangle",
                "color_bg" => "#FEF2F2", // very light red background
                "color_text" => "#DC2626", // red-600
                'description' => 'Failed'
            ],
            self::DELAYED => [
                'label' => 'Delayed',
                "icon" => "clock",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Delayed'
            ],
            self::COMPLETED => [
                'label' => 'Completed',
                'icon' => 'check',
                'color_bg' => '#DCFCE7', // light green background
                'color_text' => '#15803D', // green-700
                'description' => 'Completed',
            ],
        };
    }

    public function values(): array
    {
        return [
            self::PENDING,
            self::PICKED_UP,
            self::CANCELLED,
            self::FAILED,
            self::DELAYED,
        ];
    }

    public function apiOptions(): array
    {
        $cases = $this->cases();

        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->info()['label'],
                'icon' => $case->info()['icon'],
                'color_bg' => $case->info()['color_bg'],
                'color_text' => $case->info()['color_text'],
            ];
        }, $cases);
    }
}