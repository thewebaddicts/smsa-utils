<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'route_assignments';
    protected $fillable = [
        'route_id',
        'courier_id',
        'assigned_at',
        'unassigned_at',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }
} 