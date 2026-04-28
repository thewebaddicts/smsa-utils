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
                'column' => 'exception_category_id',
                'label' => 'Exception Category',
                'type' => 'select',
                'required' => true,
                'options' => \twa\smsautils\Models\ExceptionCategory::all()->pluck('label', 'id'),
            ],
            [
                'column' => 'exception_trigger_reason_id',
                'label' => 'Exception Trigger Reason',
                'type' => 'select',
                'required' => true,
                'options' => \twa\smsautils\Models\ExceptionTriggerReason::all()->pluck('label', 'id'),
            ]
        ];
    }

    public function handle(array $variables, string|null $payload): bool
    {

        $array = [
            'awb' => $variables['parent_awb'],
            'exception_category_id' => $payload['exception_category_id'],
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
