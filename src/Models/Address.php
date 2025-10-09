<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'label',
        'company',
        'attention',
        'address1',
        'address2',
        'email',
        'phone',
        'zip',
        'city',
        'province',
        'country',
        'address_type',
        'latitude',
        'longitude',
        'client_id',
        'user_id',
        'address_for',
        'target_id'
    ];
}
