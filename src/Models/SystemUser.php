<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUser extends Model
{
    protected $table = 'system_users';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the display full name attribute
     */
    public function getDisplayFullNameAttribute(): string
    {
        return $this->name ?? 'Guest User';
    }
}
