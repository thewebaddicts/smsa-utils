<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;


  protected $fillable = [

        'scheduled_date',
        'reference1',
        'reference2',
        'reference3',
        'payment_reference1',
        'payment_reference2',
        'payment_reference3',
        'added_services',
        'transaction_id',
        'billing_reference',
        'currency',
        'product_code',
        'product_group',
        'payment_type',
        'payment_method',
        'sender_address_id',
        'receiver_address_id',
        'client_id',
        'customer_id',
        'customs_payment_type',
        'freight_payment_method',
        'customs_payment_method',
        'freight_payment_type',
        'insurance',
        'freight_payment_type',
        'sla_delivery_date',
        'dutiable',
        // ROZANA
        'eta',
        'etd',
        // end ROZANA
    ];
    public function awbs()
    {
        return $this->hasMany(\twa\smsautils\Models\Awb::class);
    }

      public function client()
    {
        return $this->belongsTo(\twa\smsautils\Models\Client::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(\twa\smsautils\Models\Customer::class);
    }
 
}