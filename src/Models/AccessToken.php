<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'token',
        'expires_at',
    ];

    public function tokenable()
    {
        return $this->morphTo();
    }
} 