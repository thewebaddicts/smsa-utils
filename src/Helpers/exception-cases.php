<?php

use twa\smsautils\Models\Awb;
use twa\smsautils\Models\Courier;
use twa\smsautils\Models\ExceptionTriggerReason;
use twa\smsautils\Models\PickupRequest;
use twa\smsautils\Models\UnknownAwb;

if (!function_exists('resolve_hub_id_from_awb_model')) {

    function resolve_hub_id_from_awb_model(?Awb $awb): ?int
    {
        if (! $awb) {
            return null;
        }

        $loc = $awb->current_location ?? null;
        if (! is_string($loc) || $loc === '') {
            return resolve_hub_id_from_awb_route_fallback($awb);
        }

        if ($loc === 'hub_out') {
            $dest = $awb->destination_hub_id ?? null;

            return $dest !== null && $dest !== '' ? (int) $dest : null;
        }

        if (preg_match('/^hub_(\d+)_/', $loc, $m)) {
            return (int) $m[1];
        }

        if (preg_match('/^courier_(\d+)/', $loc, $m)) {
            $id = (int) $m[1];
            $courierIdStr = $m[1];

            $hubId = Courier::query()
                ->whereKey($id)
                ->whereNull('deleted_at')
                ->value('hub_id');

            if ($hubId === null || $hubId === '') {
                $hubId = Courier::query()
                    ->where('courier_id', $courierIdStr)
                    ->whereNull('deleted_at')
                    ->value('hub_id');
            }

            return $hubId !== null && $hubId !== '' ? (int) $hubId : null;
        }

        if (str_starts_with($loc, 'shipper_address_')) {
            $origin = $awb->origin_hub_id ?? null;

            return $origin !== null && $origin !== '' ? (int) $origin : null;
        }

        return resolve_hub_id_from_awb_route_fallback($awb);
    }
}

if (!function_exists('resolve_hub_id_from_awb_route_fallback')) {
    function resolve_hub_id_from_awb_route_fallback(Awb $awb): ?int
    {
        $fromRoute = $awb->origin_hub_id ?? $awb->destination_hub_id ?? null;

        return $fromRoute !== null && $fromRoute !== '' ? (int) $fromRoute : null;
    }
}

if (!function_exists('resolve_exception_case_hub_id')) {

    function resolve_exception_case_hub_id(array $payload, string $targetableType, int $targetableId, ?Awb $awb = null): ?int
    {
        if (! empty($payload['hub_id'])) {
            return (int) $payload['hub_id'];
        }

        return match ($targetableType) {
            'awb' => resolve_hub_id_from_awb_model(
                $awb ?? Awb::query()->whereKey($targetableId)->whereNull('deleted_at')->first()
            ),
            'pickup_request' => PickupRequest::query()
                ->whereKey($targetableId)
                ->whereNull('deleted_at')
                ->value('hub_id'),
            'unknown_awb' => UnknownAwb::query()
                ->whereKey($targetableId)
                ->whereNull('deleted_at')
                ->value('hub_id'),
            default => null,
        };
    }
}

if (!function_exists('create_record_in_exception')) {
    function create_record_in_exception(array $payload)
    {
        $awb = null;

        if (isset($payload['awb'])) {
            // dd($payload['awb']);
            $awb = Awb::where('awb', $payload['awb'])->whereNull('deleted_at')->first();

            if (! $awb) {
                return false;
            }

            if ($awb->master_awb != $awb->awb) {
                return false;
            }

            $awb_exception_exists = \twa\smsautils\Models\ExceptionCase::query()

                ->where(function ($query) use ($payload, $awb) {
                    $query->where('awb', $payload['awb']);
                    $query->orWhere(function ($q) use ($awb) {
                        $q->where('targetable_id', $awb->id);
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
        } else {

            // dd($payload);

            $targetable_id = $payload['targetable_id'];
            $targetable_type = $payload['targetable_type'];
        }

        if (! $targetable_id || ! $targetable_type) {
            return false;
        }

        if ($targetable_type == 'awb' && $targetable_id) {
            $awb = Awb::where('id', $targetable_id)->whereNull('deleted_at')->first();

            if (! $awb) {
                return false;
            }

            if ($awb->master_awb != $awb->awb) {
                // dd($awb->master_awb);
                $awb = Awb::where('awb', $awb->master_awb)->whereNull('deleted_at')->first();
            }

            if (! $awb) {
                return false;
            }
            $targetable_id = $awb->id;

            // dd($targetable_id);

            $exceptionCase = \twa\smsautils\Models\ExceptionCase::query()
                ->where('targetable_id', $targetable_id)
                ->where('targetable_type', 'awb')
                ->where('exception_category_id', $payload['exception_category_id'])
                ->where('exception_trigger_reason_id', $payload['exception_trigger_reason_id'])
                ->whereNotNull('resolved_at')
                ->whereNull('deleted_at')
                ->first();

            if ($exceptionCase) {
                return $exceptionCase;
            }
        }

        $hubId = resolve_exception_case_hub_id($payload, (string) $targetable_type, (int) $targetable_id, $awb);

        $exceptionCase = new \twa\smsautils\Models\ExceptionCase();
        $exceptionCase->targetable_id = $targetable_id;
        $exceptionCase->targetable_type = $targetable_type;
        $exceptionCase->exception_category_id = $payload['exception_category_id'];
        $exceptionCase->exception_trigger_reason_id = $payload['exception_trigger_reason_id'];
        $exceptionCase->hub_id = $hubId;
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

        return cache()->remember('sla_defined_hours_'.$exception_trigger_reason_id, 60, function () use ($exception_trigger_reason_id) {
            $exception_trigger_reason = ExceptionTriggerReason::query()
                ->whereNull('deleted_at')
                ->where('id', $exception_trigger_reason_id)
                ->first();

            return $exception_trigger_reason?->sla_defined_hours;
        });

        // (new \App\Jobs\NoActivitybeyondDefinedTime(31728, ))->handle();
    }
}
