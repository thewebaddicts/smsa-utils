<?php

namespace twa\smsautils\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyTokenAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'attempt_at',
        'driver',
        'otp',
        'expires_at',
    ];

    protected $casts = [
        'attempt_at' => 'datetime',
    ];
} 