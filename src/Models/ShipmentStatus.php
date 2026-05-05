<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentStatus extends Model
{
    protected $casts = [
        'shipment_status_tags' => 'array',
    ];
    public function shipmentStatusCategory()
    {
        return $this->belongsTo(\twa\smsautils\Models\ShipmentStatusCategory::class, 'shipment_status_category_id');
    }
    public function exceptionCategory()
    {
        return $this->belongsTo(\twa\smsautils\Models\ExceptionCategory::class, 'exception_category_id');
    }
}
