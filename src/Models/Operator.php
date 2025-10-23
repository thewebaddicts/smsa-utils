<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'operators';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'hub_id',
        'status',
        'notes',
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    /**
     * Get the full name of the operator
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

     public function getEmployeeReferenceAttribute()
    {
      return $this->employee_id;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }
} 