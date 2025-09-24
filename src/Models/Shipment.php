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
        'transaction_id',
        'billing_reference',
        'currency',
        'product_code',
        'product_group',
        'payment_type',
        'payment_method',
        'dutiable',
        'sender_address_id',
        'receiver_address_id',
        'client_id',
        'customer_id',
        // ROZANA
        'eta',
        'etd',
        // end ROZANA
    ];

    public function awbs()
    {
        return $this->hasMany(\twa\smsautils\Models\Awb::class);
    }

    public function sender()
    {
        return $this->belongsTo(Address::class, 'sender_address_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Address::class, 'receiver_address_id');
    }
}
