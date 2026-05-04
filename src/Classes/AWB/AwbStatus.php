<?php

namespace twa\smsautils\Classes\AWB;


class AwbStatus
{

    public function __construct(public string $status) {}


    public function info(): array
    {
     
    
        
        return [
            'label' => '',
            'icon' => 'file-plus',
            'color_bg' => '#e3f2fd',
            'color_text' => '#1565c0',
            'description' => 'This Shipment has been attempted 2 times',
            'category' => null,
            'tags' => ["all"],
        ];
    }
}
