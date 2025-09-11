<?php


namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courier_statuses';
    protected $fillable = ['name'];
} 