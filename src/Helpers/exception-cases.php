<?php

use twa\smsautils\Models\ExceptionTriggerReason;

if (!function_exists('create_record_in_exception')) {
    function create_record_in_exception(array $payload)
    {

        if(isset($payload['awb'])){
            $awb = \twa\smsautils\Models\Awb::where('awb', $payload['awb'])->whereNull('deleted_at')->first();

            if (!$awb) {
                return false;
            }

            $awb_exception_exists = \twa\smsautils\Models\ExceptionCase::query()

            ->where(function ($query) use ($payload, $awb) {
                $query->where('awb', $payload['awb']);
                $query->orWhere(function ($q) use ($awb) {
                    $q->where('targetable_id', $awb['id']);
                    $q->where('targetable_type', 'awb');
                });
            })
            ->where('exception_category_id', $payload['exception_category_id'])
            ->where('exception_trigger_reason_id', $payload['exception_trigger_reason_id'])
            ->whereNotNull('resolved_at')->whereNull('deleted_at')->exists();

            if ($awb_exception_exists) {
                return false;
        }

        
        $targetable_id = $awb->id;
        $targetable_type = 'awb';


        }else{


            $targetable_id = $payload['targetable_id'];
            $targetable_type = $payload['targetable_type'];

        }


        if(!$targetable_id || !$targetable_type){
            return false;
        }


        $exceptionCase = new \twa\smsautils\Models\ExceptionCase();
        $exceptionCase->targetable_id = $targetable_id;
        $exceptionCase->targetable_type = $targetable_type;
        $exceptionCase->exception_category_id = $payload['exception_category_id'];
        $exceptionCase->exception_trigger_reason_id = $payload['exception_trigger_reason_id'];
        $exceptionCase->comments = $payload['comments'] ?? null;
        $exceptionCase->file_ids = $payload['files'] ?? [];
        $exceptionCase->created_by_id = $payload['created_by_id'] ?? null;
        $exceptionCase->created_by_type = $payload['created_by_type'] ?? null;
        $exceptionCase->save();

        $exceptionCase->reference = generate_reference_number($exceptionCase->id, 'EX-');
        $exceptionCase->save();

        return $exceptionCase;
    }
}

if (!function_exists('get_sla_defined_hours')) {
    function get_sla_defined_hours()
    {

        $exception_trigger_reason_id = 11;

        return cache()->remember('sla_defined_hours_' . $exception_trigger_reason_id, 60, function () use ($exception_trigger_reason_id) {
            $exception_trigger_reason = ExceptionTriggerReason::query()
                ->whereNull('deleted_at')
                ->where('id', $exception_trigger_reason_id)
                ->first();

            return $exception_trigger_reason?->sla_defined_hours;
        });


        // (new \App\Jobs\NoActivitybeyondDefinedTime(31728, ))->handle();
    }
}