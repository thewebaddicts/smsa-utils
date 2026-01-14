<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Awb extends Model
{
    use HasFactory;

    protected $table = 'awbs';

    protected $fillable = [
        'shipment_id',
        'awb',
        'master_awb',
        'runsheet_id',

        'package_reference',
        'package_description',
        'packaging_unit',
        'awb_sequence',
        'nb_packages',
        'type',
        'commodity_type',
        'declared_height',
        'declared_length',
        'declared_width',
        'declared_height_cm',
        'declared_length_cm',
        'declared_width_cm',
        'declared_dimension_unit',
        'declared_weight',
        'declared_weight_g',
        'declared_weight_unit',
        'declared_amount',
        'declared_amount_currency',
        'cod_amount',
        'cod_currency',
        'last_status',
        'pickup_route_id',
        'delivery_route_id',
        'origin_hub_id',
        'destination_hub_id',
        'sender_address_id',
        'receiver_address_id',
        'insurance',
        'assigned_at',
        'runsheet_id',
        'picked_at',
        'delivered_at',
        'master_awb',
        'commodity_type',
        'declared_height_cm',
        'declared_length_cm',
        'declared_width_cm',
        'declared_dimension_unit',
        'declared_weight_g',
        'declared_weight_unit',
        'declared_amount',
        'declared_amount_currency',
        'cod_amount',
        'cod_currency',
        'actual_height_cm',
        'actual_length_cm',
        'actual_width_cm',
        'actual_weight_g',
        'actual_weight_unit',
        'actual_amount',
        'actual_amount_currency',
        'actual_cod_amount',
        'actual_cod_currency',
        'actual_declared_amount',
        'actual_declared_amount_currency',
        'actual_cod_amount',
        'actual_cod_currency',
        'actual_declared_amount',
        'actual_declared_amount_currency',
        'actual_cod_amount',
        'deleted_at',
        'updated_at',
        'created_at',
        'origin_code',
        'destination_code'
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
        return $this->belongsTo(\twa\smsautils\Models\Address::class, 'sender_address_id');
    }

    public function receiver()
    {
        return $this->belongsTo(\twa\smsautils\Models\Address::class, 'receiver_address_id');
    }


    public function items()
    {
        return $this->hasMany(AwbItem::class, 'awb_id');
    }

   
}