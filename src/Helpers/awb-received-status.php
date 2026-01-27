<?php

use twa\smsautils\Models\Awb;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use twa\smsautils\Enums\AwbStatusEnum;
use Illuminate\Support\Facades\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
if (!function_exists('update_awb_received_status')) {

    function update_awb_received_status(Awb &$awb, $hub_id = null, $location = null, $save = true)
    {
        $user = request()->user();
        $user_type = request()->input('user_type');
        $location = $location ?? 'operations';
        $awb = extract_awb_model_from_record($awb);

        // Debug log to understand which branch is taken
        Log::info('update_awb_received_status:before', [
            'awb_id' => $awb->id ?? null,
            'awb_number' => $awb->awb ?? null,
            'origin_hub_id' => $awb->origin_hub_id ?? null,
            'destination_hub_id' => $awb->destination_hub_id ?? null,
            'origin_received_at' => $awb->origin_received_at ?? null,
            'destination_received_at' => $awb->destination_received_at ?? null,
            'passed_hub_id' => $hub_id,
            'location' => $location,
        ]);

        // Origin receive: first time the AWB is received at its origin hub
        // Use empty() so we also treat "zero" dates as not received yet.
        if ($awb->origin_hub_id == $hub_id && empty($awb->origin_received_at)) {

            $awb->last_status = AwbStatusEnum::ORIGIN_RECEIVED;
            $awb->origin_received_at = Carbon::now();

            Log::info('update_awb_received_status:origin_branch', [
                'awb_id' => $awb->id ?? null,
                'hub_id' => $hub_id,
            ]);

            log_awb_activity(
                AwbStatusEnum::ORIGIN_RECEIVED,
                $awb->awb,
                $awb->id,
                $user->id ?? null,
                $user_type ?? null,
                'Origin received from ' . $location
            );
        }
        // Destination receive: international only (different origin/destination hubs)
        // and origin already received, but destination not yet.
        elseif (
            $awb->origin_hub_id != $awb->destination_hub_id
            && !empty($awb->origin_received_at)
            && empty($awb->destination_received_at)
        ) {

            $awb->last_status = AwbStatusEnum::DESTINATION_RECEIVED;

            Log::info('update_awb_received_status:destination_branch', [
                'awb_id' => $awb->id ?? null,
                'hub_id' => $hub_id,
            ]);

            $awb->destination_received_at = Carbon::now();
            log_awb_activity(
                AwbStatusEnum::DESTINATION_RECEIVED,
                $awb->awb,
                $awb->id,
                $user->id ?? null,
                $user_type ?? null,
                'Destination received from ' . $location
            );
        } else {

            $awb->last_status = AwbStatusEnum::RECEIVED_OPERATION;

            Log::info('update_awb_received_status:operation_branch', [
                'awb_id' => $awb->id ?? null,
                'hub_id' => $hub_id,
            ]);

            log_awb_activity(
                AwbStatusEnum::RECEIVED_OPERATION,
                $awb->awb,
                $awb->id,
                $user->id ?? null,
                $user_type ?? null,
                'Received operation from ' . $location
            );
        }

        if ($save) {
            $awb->save();
            return $awb;
        }
    }
}
