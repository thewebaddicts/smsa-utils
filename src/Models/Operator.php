<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
    public function getDisplayFullNameAttribute()
    {
        return $this->employee_id . ' | ' . $this->name;
    }

    public function format()
    {
        $hub = $this->hub_id
            ? DB::table('hubs')
            ->where('id', $this->hub_id)
            ->whereNull('deleted_at')
            ->first(['id', 'label'])
            : null;

        $rolesIds = json_decode($this->roles_ids, true) ?? [];

        $roles = empty($rolesIds)
            ? collect()
            : DB::table('roles')
            ->whereIn('id', $rolesIds)
            ->whereNull('deleted_at')
            ->get(['id', 'label']);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'employee_id' => $this->employee_id,
            'phone_number' => $this->phone_number,
            'hub' => $hub ? [
            'id' => $hub->id,
            'label' => $hub->label,
        ] : null,

            'roles' => $roles->map(fn($role) => [
                'id' => $role->id,
                'label' => $role->label,
            ]),
            'superadmin' => (bool) $this->superadmin,
            'created_at' => format_date_time($this->created_at),
        ];
    }
}