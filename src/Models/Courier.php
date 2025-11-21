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

    public function getEmployeeReferenceAttribute()
    {
        return $this->courier_id;
    }

    public function getDisplayNameAttribute()
    {
        return $this->getFullNameAttribute();
    }
   
    public function assignedVehicle()
    {
        return $this->belongsTo(\App\Models\Vehicle::class, 'assigned_vehicle')->with(['brand']);
    }


    public function format()
    {
        $vehicle = $this->assignedVehicle;

        return [
            'id' => $this->id,
            'courier_id' => $this->courier_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'display_name' => $this->display_name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'national_id' => $this->national_id,
            'address' => $this->address,
            'driving_license_number' => $this->driving_license_number,
            'license_expiry_date' => $this->license_expiry_date,
            'license_type' => $this->license_type,
            'hire_date' => $this->hire_date,
            'status' => optional(\App\Enums\CourierStatusesEnum::tryFrom($this->status))->info(),
            'assigned_vehicle' => $this->assigned_vehicle,
            'vehicle' => $vehicle ? [
                'id' => $vehicle->id,
                'license_plate_number' => $vehicle->license_plate_number,
                'model' => $vehicle->model,
                'palette_capacity' => $vehicle->palette_capacity,
                'year' => $vehicle->year_of_manufacture,
                'brand' => $vehicle->brand ? [
                    'id' => $vehicle->brand->id,
                    'label' => $vehicle->brand->name
                ] : null,
            ] : null,
            'type' => $this->type,
            'company_id' => $this->company_id,
            'company' => $this->company?->label,
            'contact_person_name' => $this->contact_person_name,
            'contact_phone_number' => $this->contact_phone_number,
            'relationship' => $this->relationship,
            'driving_license_scan' => $this->driving_license_scan,
            'national_id_scan' => $this->national_id_scan,
            'notes' => $this->notes,
            'assigned_routes' => $this->assignedRoutes->map(fn($route) => [
                'id' => $route->id,
                'display_name' => $route->label,
            ])->values()->toArray(),
        ];
    }
}