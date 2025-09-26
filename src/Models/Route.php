<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hub_id',
        'label',
        'capacity',
        'mode',
    ];

        public function hub()
    {
        return $this->belongsTo(\twa\smsautils\Models\Hub::class, 'hub_id');
    }

} 