<?php

namespace twa\smsautils\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consignee extends Model
{
    use SoftDeletes;

    protected $table = 'consignees';

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
} 