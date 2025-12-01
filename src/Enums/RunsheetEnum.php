<?php

namespace twa\smsautils\Enums;

enum RunsheetEnum: string
{
    case PREPARING          = 'preparing';
    case PENDING            = 'pending';
    case COMPLETED          = 'completed';
    case CANCELLED          = 'cancelled';
    case FULLY_DELIVERED    = 'fully-delivered';
    case PARTIALLY_DELIVERED = 'partially-delivered';
    case NOT_DELIVERED      = 'not-delivered';

    public function info(): array
    {
        return match ($this) {

            self::PREPARING => [
                'label' => 'Preparing',
                'icon' => '',
                'color_bg' => '#f54a00',
                'color_text' => '#ffffff',
                'description' => ''
            ],

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

            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => '',
                'color_bg' => '#ff0000',
                'color_text' => '#ffffff',
                'description' => ''
            ],

            self::FULLY_DELIVERED => [
                'label' => 'Fully Delivered',
                'icon' => '',
                'color_bg' => '#0066cc',
                'color_text' => '#ffffff',
                'description' => ''
            ],

            self::PARTIALLY_DELIVERED => [
                'label' => 'Partially Delivered',
                'icon' => '',
                'color_bg' => '#ffcc00',
                'color_text' => '#000000',
                'description' => ''
            ],

            self::NOT_DELIVERED => [
                'label' => 'Not Delivered',
                'icon' => '',
                'color_bg' => '#808080',
                'color_text' => '#ffffff',
                'description' => ''
            ],
        };
    }
}