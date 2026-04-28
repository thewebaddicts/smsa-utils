<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use twa\smsautils\Models\Awb;

class ExceptionCase extends Model
{
    protected $fillable = [
        'reference',
        'awb',
        'exception_category_id',
        'exception_trigger_reason_id',
        'shipper',
        'consignee',
        'sla',
        'comments',
        'file_ids',

        'created_by_id',
        'created_by_type',
        'assigned_to_id',
        'assigned_to_type',
        'assigned_to_name',
        'assigned_by_id',
        'assigned_by_type',

        'assigned_by_name',
        'assigned_at',
        'resolved_by_id',
        'resolved_by_type',

        'resolved_by_name',
        'resolve_method_id',
        'resolved_at',
    ];

    protected $casts = [
        'file_ids' => 'array',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function exceptionCategory()
    {
        return $this->belongsTo(\twa\smsautils\Models\ExceptionCategory::class, 'exception_category_id')->whereNull('deleted_at');
    }
    public function exceptionTriggerReason()
    {
        return $this->belongsTo(\twa\smsautils\Models\ExceptionTriggerReason::class, 'exception_trigger_reason_id')->whereNull('deleted_at');
    }
    public function createdBy()
    {
        return $this->morphTo('created_by', 'created_by_type', 'created_by_id');
    }

    public function resolveMethod()
    {
        return $this->belongsTo(\twa\smsautils\Models\ExceptionResolveMethod::class, 'resolve_method_id')->whereNull('deleted_at');
    }

    // Relation name must not collide with `exception_cases.awb` column attribute (string).
    public function awbModel()
    {
        return $this->belongsTo(\twa\smsautils\Models\Awb::class, 'awb', 'awb')->whereNull('deleted_at');
    }
}
