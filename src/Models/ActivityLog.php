<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'mode',
        'record_id',
        'record_type',
        'payload',
        'created_at',
        'operator_id',
        'operator_email',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}