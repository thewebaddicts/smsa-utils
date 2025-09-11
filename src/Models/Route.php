<?php

namespace twa\smsautils\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hub_id',
        'label',
        'capacity',
        'mode',
    ];

 
   
   
} 