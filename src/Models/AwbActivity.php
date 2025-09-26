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
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function activityBy()
    {
        return $this->morphTo();
    }
}