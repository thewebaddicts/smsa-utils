<?php

if (!function_exists('create_record_in_exception')) {
    function create_record_in_exception(array $payload)
    {


        $awb_exception_exists = \twa\smsautils\Models\ExceptionCase::where('awb', $payload['awb'])
            ->where('exception_category_id', $payload['exception_category_id'])
            ->where('exception_trigger_reason_id', $payload['exception_trigger_reason_id'])
            ->whereNotNull('resolved_at')->whereNull('deleted_at')->exists();

        if ($awb_exception_exists) {
            return false;
        }


        $exceptionCase = new \twa\smsautils\Models\ExceptionCase();
        $exceptionCase->awb = $payload['awb'];
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
