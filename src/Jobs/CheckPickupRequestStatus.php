<?php

namespace twa\smsautils\Jobs;

use twa\smsautils\Models\PickupRequest;
use twa\smsautils\Enums\PickupStatusEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckPickupRequestStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pickupRequestId;
    protected $checkType; // 'delayed' or 'failed'

    /**
     * Create a new job instance.
     */
    public function __construct($pickupRequestId, $checkType)
    {
        $this->pickupRequestId = $pickupRequestId;
        $this->checkType = $checkType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pickupRequest = PickupRequest::find($this->pickupRequestId);
        if (!$pickupRequest) {
           
            return;
        }

        // Always cast to int to avoid Carbon error
        $failMinutes = (int) env('PICKUP_FAIL_MINUTES', 180);

        // dd($this->checkType);

        if ($this->checkType === 'delayed') {
            if ($pickupRequest->status === PickupStatusEnum::PENDING->value) {
                $pickupRequest->update(['status' => PickupStatusEnum::DELAYED->value]);
                $pickupRequest->logActivity('Pickup request automatically marked as delayed (1 minute after pickup time end)',null);
               
            }
        } elseif ($this->checkType === 'failed') {
            if ($pickupRequest->status === PickupStatusEnum::DELAYED->value) {
                $pickupRequest->update(['status' => PickupStatusEnum::FAILED->value]);
                $pickupRequest->logActivity("Pickup request automatically marked as failed ({$failMinutes} minutes after pickup time end)",null);
                
            }
        }
    }
} 