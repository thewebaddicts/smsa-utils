<?php

namespace twa\smsautils\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use twa\smsautils\Models\AwbActivity;
use twa\smsautils\Models\ExceptionTriggerReason;

class ExceptionOnPendingBeyondDefinedTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected  $awb;
    protected  $last_awb_activity_log_id;
    protected  $exceptionTriggerReasonId;

    public function __construct($awb, $last_awb_activity_log_id, $exceptionTriggerReasonId)
    {
        $this->awb = $awb;
        $this->last_awb_activity_log_id = $last_awb_activity_log_id;
        $this->exceptionTriggerReasonId = $exceptionTriggerReasonId;
    }

    public function handle(): void
    {
        $last_actual_activity_id = AwbActivity::query()
            ->where('target', $this->awb)
            ->orderBy('id', 'DESC')
            ->value('id');


        if ($last_actual_activity_id != $this->last_awb_activity_log_id) {
            return;
        }


        $exceptionTriggerReason = ExceptionTriggerReason::query()
            ->with('exceptionCategory')
            ->whereNull('deleted_at')
            ->where('id', $this->exceptionTriggerReasonId)
            ->first();

        if (! $exceptionTriggerReason) {
            return;
        }

        $payload = [
            'awb' => $this->awb,
            'exception_category_id' => $exceptionTriggerReason->exceptionCategory->id,
            'exception_trigger_reason_id' => $exceptionTriggerReason->id,
            'comments' => null,
            'files' => [],
            'created_by_id' => null,
            'created_by_type' => 'system',
        ];


        create_record_in_exception($payload);
    }
}
