<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnknownAwb extends Model
{
    use HasFactory, SoftDeletes;


    //
    public function shipper()
    {
        return $this->belongsTo(\twa\smsautils\Models\Shipper::class, 'seller_id');
    }

    public function branch()
    {
        return $this->belongsTo(\twa\smsautils\Models\Hub::class, 'hub_id');
    }
}
