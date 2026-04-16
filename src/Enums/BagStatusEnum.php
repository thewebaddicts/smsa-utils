<?php

namespace twa\smsautils\Enums;

enum BagStatusEnum: string
{
    case PENDING = 'pending';

    case COMPLETED = 'completed';
    case ASSIGNED_RUNSHEET = 'assigned_runsheet';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case NOT_DELIVERED = 'not_delivered';

    public function info(): array
    {
        return match ($this) {
            self::PENDING => [
                'label' => 'Pending',
                'icon' => '',
                'color_bg' => '#f54a00',
                'color_text' => '#ffffff',
                'description' => ''
            ],
            self::COMPLETED => [
                'label' => 'Completed',
                'icon' => '',
                'color_bg' => '#008000',
                'color_text' => '#ffffff',
                'description' => ''
            ],
            self::ASSIGNED_RUNSHEET => [
                'label' => 'Assigned to Runsheet',
                'icon' => '',
                'color_bg' => '#008000',
                'color_text' => '#ffffff',
                'description' => ''
            ],
            self::OUT_FOR_DELIVERY => [
                'label' => 'Out for Delivery',
                'icon' => '',
                'color_bg' => '#008000',
                'color_text' => '#ffffff',
                'description' => ''
            ],
            self::DELIVERED => [
                'label' => 'Delivered',
                'icon' => '',
                'color_bg' => '#008000',
                'color_text' => '#ffffff',
                'description' => ''
            ],
            self::NOT_DELIVERED => [
                'label' => 'Not Delivered',
                'icon' => '',
                'color_bg' => '#008000',
                'color_text' => '#ffffff',
                'description' => ''
            ],
            default => [
                'label' => 'Unknown',
                'icon' => '',
                'color_bg' => '#008000',
                'color_text' => '#ffffff',
                'description' => ''
            ]
        };
    }
    public function values(): array
    {
        return [
            self::PENDING,
            self::COMPLETED,
            self::ASSIGNED_RUNSHEET,
            self::OUT_FOR_DELIVERY,
            self::DELIVERED,
            self::NOT_DELIVERED,
        ];
    }
}