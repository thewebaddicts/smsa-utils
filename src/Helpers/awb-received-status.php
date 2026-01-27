<?php

use twa\smsautils\Models\Awb;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
USE twa\smsautils\Enums\AwbStatusEnum;

if (!function_exists('update_awb_received_status')) {

    function update_awb_received_status(Awb &$awb, $hub_id, $save = true)
    {
        $awb = extract_awb_model_from_record($awb);
        if($awb->origin_hub_id == $hub_id && $awb->origin_received_at == null) {
            $awb->status = AwbStatusEnum::ORIGIN_RECEIVED;
        }elseif($awb->origin_hub_id != $awb->destination_hub_id && $awb->origin_received_at  && $awb->destination_received_at == null) {
            $awb->status = AwbStatusEnum::DESTINATION_RECEIVED;
        }else{
            $awb->status = AwbStatusEnum::RECEIVED_OPERATION;
        }

        if ($save) {
            $awb->saveQuietly();
            return $awb;
        }
    }
}
