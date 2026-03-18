<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowActivityEventStatus extends Model
{
    protected $fillable = [
        'awb_activity_id',
        'event_identifier',
        'status',
        'payload',
        'variables',
    ];

    protected $casts = [
        'payload' => 'array',
        'variables' => 'array',
    ];
}
