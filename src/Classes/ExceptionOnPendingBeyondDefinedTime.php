<?php

namespace twa\smsautils\Classes;


class ExceptionOnPendingBeyondDefinedTime extends HandlerParent
{
    public function label(): string
    {
        return 'Exception On Pending Beyond Defined Time';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'exception_trigger_reason_id',
                'label' => 'Exception Trigger Reason',
                'type' => 'select',
                'required' => true,
                'options' => \twa\smsautils\Models\ExceptionTriggerReason::query()
                    ->with('exceptionCategory')
                    ->whereNull('deleted_at')
                    ->get()
                    ->map(function ($exception_trigger_reason) {
                        $categoryLabel = $exception_trigger_reason->exceptionCategory?->label;

                        return [
                            'label' => ($categoryLabel ?? 'Uncategorized') . ' | ' . $exception_trigger_reason->label,
                            'value' => $exception_trigger_reason->id,
                        ];
                    }),
            ],
            [
                'column' => 'sla_defined_hours',
                'label' => 'SLA Defined Hours',
                'type' => 'number',
                'required' => true,
                'placeholder' => 'Enter SLA Defined Hours',
            ],

        ];
    }

    public function handle(array $variables, string|null $payload): bool
    {


        $status = "";

        $payload = $this->validatePayload($variables, $payload);
        if (!$payload) {
            return false;
        }
        $trigger_reason = \twa\smsautils\Models\ExceptionTriggerReason::query()->with('exceptionCategory')->where('id', $payload['exception_trigger_reason_id'])->first();
        if (!$trigger_reason || !$trigger_reason->exceptionCategory) {
            return false;
        }

        \twa\smsautils\Jobs\ExceptionOnPendingBeyondDefinedTime::dispatch(
            $variables['parent_awb'],
            $this->awb_activity_log_id,
            $payload['exception_trigger_reason_id']
        )->delay(now()->parse(now()->addHours($payload['sla_defined_hours'])));

        return true;
    }
}
