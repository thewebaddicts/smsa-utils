<?php

namespace twa\smsautils\Classes;


class ExceptionCase extends HandlerParent
{
    public function label(): string
    {
        return 'Exception Case';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'exception_trigger_reason_id',
                'label' => 'Exception Trigger Reason',
                'type' => 'select',
                'required' => true,
                'options' => \twa\smsautils\Models\ExceptionTriggerReason::query()->with('exceptionCategory')->whereNull('deleted_at')->get()->map(function ($exception_trigger_reason) {
                    
                    return [
                        'label' =>$exception_trigger_reason->exceptionCategory->label. ' | ' . $exception_trigger_reason->label  ,
                        'value' => $exception_trigger_reason->id,
                    ];
                }),
            ]
        ];
    }

    public function handle(array $variables, string|null $payload): bool
    {

        $payload = $this->validatePayload($variables, $payload);
        if (!$payload) {
            return false;
        }
        $trigger_reason = \twa\smsautils\Models\ExceptionTriggerReason::query()->with('exceptionCategory')->where('id', $payload['exception_trigger_reason_id'])->first();
        if (!$trigger_reason) {
            return false;
        }


        $array = [
            'awb' => $variables['parent_awb'],
            'exception_category_id' => $trigger_reason->exceptionCategory->id,
            'exception_trigger_reason_id' => $payload['exception_trigger_reason_id'],
            'comments' =>  null,
            'files' => null,
            'created_by_id' => 2,
            'created_by_type' => 'workflow',
        ];

        try {
            create_record_in_exception($array);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
