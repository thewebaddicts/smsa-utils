<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use twa\smsautils\Events\PickupRequestCreated;

class PickupRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pickup_requests';

    protected $fillable = [
        'client_id',
        'address_id',
        'hub_id',
        'route_id',
        'courier_id',
        'nb_packages',
        'total_weight',
        'dimension_length',
        'dimension_width',
        'dimension_height',
        'pickup_date',
        'pickup_time',
        'instruction',
        'status',
        'assignment_type',
        'routine_id',
        'pickup_date_from',
        'pickup_date_to',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
        'pickup_time' => 'time',
        'expected_awbs' => 'array'
    ];

    protected static function booted()
    {
        static::created(function ($pickupRequest) {
            $failMinutes = (int) env('PICKUP_FAIL_MINUTES', 180);
            $delayedTime = \Carbon\Carbon::parse($pickupRequest->pickup_time_to)->addMinute();
            $failedTime = \Carbon\Carbon::parse($pickupRequest->pickup_time_to)->addMinutes($failMinutes);

            \twa\smsautils\Jobs\CheckPickupRequestStatus::dispatch($pickupRequest->id, 'delayed')->delay($delayedTime);
            \twa\smsautils\Jobs\CheckPickupRequestStatus::dispatch($pickupRequest->id, 'failed')->delay($failedTime);
            \twa\smsautils\Events\PickupRequestCreated::dispatch($pickupRequest);
        });
    }


    public function route()
    {
        return $this->belongsTo(\twa\smsautils\Models\Route::class, 'route_id');
    }
    public function hub()
    {
        return $this->belongsTo(\twa\smsautils\Models\Hub::class, 'hub_id');
    }

    public function courier()
    {
        return $this->belongsTo(\twa\smsautils\Models\Courier::class, 'courier_id');
    }

    public function activities()
    {
        return $this->hasMany(\twa\smsautils\Models\PickupRequestActivity::class);
    }

    /**
     * Log an activity for this pickup request
     */
    public function logActivity($activity, $performer = null)
    {
        return \twa\smsautils\Models\PickupRequestActivity::logActivity($this->id, $activity, $performer);
    }

    public function creator()
    {
        return $this->morphTo();
    }
}
