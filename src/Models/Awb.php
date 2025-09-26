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


    public function shelf()
    {
        return $this->belongsTo(\twa\smsautils\Models\Shelf::class, 'shelf_id');
    }



    public function pickupRoute()
    {
        return $this->belongsTo(\twa\smsautils\Models\Route::class, 'pickup_route_id');
    }

    public function deliveryRoute()
    {
        return $this->belongsTo(\twa\smsautils\Models\Route::class, 'delivery_route_id');
    }




    public function destinationHub()
    {
        return $this->belongsTo(\twa\smsautils\Models\Hub::class, 'destination_hub_id');
    }


    public function originHub()
    {
        return $this->belongsTo(\twa\smsautils\Models\Hub::class, 'origin_hub_id');
    }

    public function activities()
    {
        return $this->belongsTo(\twa\smsautils\Models\AwbActivity::class, 'target_id');
    }

    public function sender()
    {
        return $this->belongsTo(Address::class, 'sender_address_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Address::class, 'receiver_address_id');
    }


    public function items()
    {
        return $this->hasMany(AwbItem::class, 'awb_id');
    }
}