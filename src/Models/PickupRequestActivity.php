<?php

namespace twa\smsautils\Models;

use App\Models\Traits\HasPolymorphicActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickupRequestActivity extends Model
{
    use HasFactory, SoftDeletes, HasPolymorphicActivity;

    protected $table = 'pickup_request_activities';

    protected $fillable = [
        'pickup_request_id',
        'activity',
        'activity_by',
        'activity_by_model',
        'activity_by_id',
    ];

    protected $appends = [
        'activity_performer_name',
        'activity_performer_type'
    ];

    public function pickupRequest()
    {
        return $this->belongsTo(\twa\smsautils\Models\PickupRequest::class);
    }

    /**
     * Create an activity record
     */
    public static function logActivity($pickupRequestId, $activity, $performer = null)
    {
        return self::create([
            'pickup_request_id' => $pickupRequestId,
            'activity' => $activity,
            'activity_by' => $performer ? $performer->getMorphClass() : null,
            'activity_by_model' => $performer ? get_class($performer) : null,
            'activity_by_id' => $performer ? $performer->id : null,
        ]);
    }
}
