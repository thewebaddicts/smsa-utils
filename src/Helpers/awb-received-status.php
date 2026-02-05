<?php

use twa\smsautils\Models\Awb;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use twa\smsautils\Enums\AwbStatusEnum;
use Illuminate\Support\Facades\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use twa\smsautils\Models\Hub;

if (!function_exists('update_awb_received_status')) {

    function update_awb_received_status(Awb &$awb, $hub_id = null, $location = null, $save = true)
    {
        $user = request()->user();
        $user_type = request()->input('user_type');
        $hub = Hub::find($hub_id);
       
        $activity_location = get_branch_info($hub);
        $activity_by = get_operator_info($user);
        $location = $location ?? 'operations';
        $awb = extract_awb_model_from_record($awb);

        if ($awb->origin_hub_id == $hub_id && !$awb->origin_received_at) {

            $awb->last_status = AwbStatusEnum::ORIGIN_RECEIVED;
            $awb->origin_received_at = now();

            log_awb_activity(
                AwbStatusEnum::ORIGIN_RECEIVED,
                $awb->awb,
                $awb->id,
                $user->id ?? null,
                $user_type ?? null,
                'Origin received from ' . $location,
                [],
                $activity_by,
                $activity_location,
                now(),
                'web'
            );
        } elseif (
            $awb->origin_hub_id != $awb->destination_hub_id
            &&
            $awb->destination_hub_id == $hub_id
            &&
            $awb->origin_received_at
            &&
            !$awb->destination_received_at
        ) {

            $awb->last_status = AwbStatusEnum::DESTINATION_RECEIVED;
            $awb->destination_received_at = now();

            log_awb_activity(
                AwbStatusEnum::DESTINATION_RECEIVED,
                $awb->awb,
                $awb->id,
                $user->id ?? null,
                $user_type ?? null,
                'Destination received from ' . $location,
                [],
                $activity_by,
                $activity_location,
                now(),
                'web'

            );

        } else {

            $awb->last_status = AwbStatusEnum::RECEIVED_OPERATION;

            log_awb_activity(
                AwbStatusEnum::RECEIVED_OPERATION,
                $awb->awb,
                $awb->id,
                $user->id ?? null,
                $user_type ?? null,
                'Received operation from ' . $location,
                [],
                $activity_by,
                $activity_location,
                now(),
                'web'
            );
        }

        if ($save) {
            $awb->save();
            return $awb;
        }

        return $awb;
    }
}
