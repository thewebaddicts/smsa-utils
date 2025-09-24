<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Awb extends Model
{
    use HasFactory;

    protected $fillable = [
        'runsheet_id',
        'number',
    ];

    public function shipment()
    {
        return $this->belongsTo(\twa\smsautils\Models\Shipment::class, 'shipment_id');
    }






    

}