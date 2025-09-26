<?php


namespace twa\smsautils\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    protected $hidden = [
        'account_pin'
    ];

    public function apiKeys()
    {
        return $this->hasMany(\twa\smsautils\Models\ClientApiKey::class);
    }

    public function addresses()
    {
        return $this->hasMany(\twa\smsautils\Models\Address::class);
    }
}