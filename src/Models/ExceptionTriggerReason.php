<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExceptionTriggerReason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label',
        'code',
        'description',
        'sort_order',
    ];
}
