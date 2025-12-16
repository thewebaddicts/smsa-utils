<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;

class AwbItem extends Model
{
    protected $fillable = [
        'awb_id',
        'sku_code',
        'hsn_code',
        'description',
        'origin_country',
        'quantity',
        'unit_price',
        'unit_currency',
    ];

   
    public function awb()
    {
        return $this->belongsTo(\twa\smsautils\Models\Awb::class, 'awb_id', 'id');
    }
}