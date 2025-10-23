<?php


namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'couriers';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'national_id',
        'address',
        'driving_license_number',
        'license_expiry_date',
        'license_type',
        'hire_date',
        'status',
        'assigned_vehicle',
        'type',
        'contact_person_name',
        'contact_phone_number',
        'relationship',
        'driving_license_scan',
        'national_id_scan',
        'notes',
    ];

    public function status()
    {
        return $this->belongsTo(CourierStatus::class, 'status_id');
    }
    public function company()
    {
        return $this->belongsTo(\twa\smsautils\Models\Company::class, 'company_id');
    }

    public function assignedRoutes()
    {
        return $this->belongsToMany(
            \twa\smsautils\Models\Route::class,
            'route_assignments',
            'courier_id',
            'route_id'
        )->whereNull('route_assignments.deleted_at');
    }



    public function getFullNameAttribute()
    {
        return $this->courier_id . ' | ' . trim($this->first_name . ' ' . $this->last_name);
    }

    public function getEmployeeReference()
    {
      return $this->employee_id;
    }

    public function getDisplayNameAttribute()
    {
        return $this->getFullNameAttribute();
    }
}