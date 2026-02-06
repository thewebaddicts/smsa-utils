<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwbActivity extends Model
{
    use HasFactory;


    protected $fillable = [
        'code',
        'comment',
        'activity',
        'activity_by_id',
        'activity_by_type',
        'files',
        'target',
        'target_id',
        'activity_location',
        'activity_by',
        'source',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function activityBy()
    {
        return $this->morphTo();
    }
}