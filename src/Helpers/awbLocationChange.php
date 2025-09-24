<?php

use twa\smsautils\Models\Awb;

if (!function_exists('awb_location_change')) {

    function awb_location_change(Awb &$awb, $location)
    {
        $awb->current_location = $location;
    }
}


if (!function_exists('extract_awb_model_from_record')) {

    function extract_awb_model_from_record(Awb|string|int $record)
    {

        if (is_numeric($record)) {
            $record = Awb::find($record);
        }

        return $record;
    }
}

if (!function_exists('on_awb_created')) {
    function on_awb_created(Awb|string|int &$record, $save = false)
    {

        $awb = extract_awb_model_from_record($record);
        $location = 'shipper_' . $awb->shipment->client_id;

        awb_location_change($awb, $location);

        if ($save) {
            $awb->save();
            return $awb;
        }

        return $awb;
    }
}
