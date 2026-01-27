<?php

use twa\smsautils\Models\Awb;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use twa\smsautils\Enums\AwbStatusEnum;
use Illuminate\Support\Facades\Carbon;
use Illuminate\Support\Facades\Request;

if (!function_exists('update_awb_received_status')) {

    function update_awb_received_status(Awb &$awb, $hub_id = null, $location = null, $save = true)
    {
        $user = request()->user();
        $user_type = request()->input('user_type');
$location = $location ?? 'operations';
        $awb = extract_awb_model_from_record($awb);
        if ($awb->origin_hub_id == $hub_id && $awb->origin_received_at == null) {
            $awb->status = AwbStatusEnum::ORIGIN_RECEIVED;
            $awb->origin_received_at = Carbon::now();
            log_awb_activity(AwbStatusEnum::ORIGIN_RECEIVED, $awb->id,  $user->id ?? null, $user_type ?? null, 'Origin received from '.$location);
        } elseif ($awb->origin_hub_id != $awb->destination_hub_id && $awb->origin_received_at  && $awb->destination_received_at == null) {
            $awb->status = AwbStatusEnum::DESTINATION_RECEIVED;
            $awb->destination_received_at = Carbon::now();
            log_awb_activity(AwbStatusEnum::DESTINATION_RECEIVED, $awb->id, $user->id ?? null, $user_type ?? null, 'Destination received from '.$location);
        } else {
            $awb->status = AwbStatusEnum::RECEIVED_OPERATION;
            log_awb_activity(AwbStatusEnum::RECEIVED_OPERATION, $awb->id,  $user->id ?? null, $user_type ?? null, 'Received operation from '.$location);
        }

        if ($save) {
            $awb->save();
            return $awb;
        }
    }
}
