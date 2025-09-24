<?php

use twa\smsautils\Models\Awb;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

if (!function_exists('awb_location_change')) {

    function awb_location_change(Awb &$awb, $location, $save)
    {
        $awb->current_location = $location;

        if ($save) {
            $awb->save();
            return $awb;
        }
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

function on_awb_created(Awb|string|int &$record, $address_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'shipper_address_' . $address_id;

    awb_location_change($awb, $location, $save);
}

function on_awb_collected(Awb|string|int &$record, $courier_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'courier_' . $courier_id;

    awb_location_change($awb, $location, $save);
}

function on_awb_delivered(Awb|string|int &$record, $courier_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'courier_' . $courier_id . '_delivered';

    awb_location_change($awb, $location, $save);
}


function on_awb_debreifed(Awb|string|int &$record, $courier_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'debriefed_courier_' . $courier_id;

    awb_location_change($awb, $location, $save);
}

function on_awb_outstanded(Awb|string|int &$record, $courier_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'outstanding_courier_' . $courier_id;

    awb_location_change($awb, $location, $save);
}



function on_awb_operation_received(Awb|string|int &$record, $hub_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'hub_' . $hub_id . '_operations';

    awb_location_change($awb, $location, $save);
}

function on_awb_untracked(Awb|string|int &$record, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'unknown';

    awb_location_change($awb, $location, $save);
}





function on_awb_closed(Awb|string|int &$record, $hub_id, $save = true)
{

    $awb = extract_awb_model_from_record($record);

    $location = 'hub_' . $hub_id . '_closed';

    awb_location_change($awb, $location, $save);
}



function on_awbs_debriefed(Builder|EloquentBuilder &$records, $courier_id, $params = [])
{

    $records->update([
        ...$params,
        'current_location' => 'debriefed_courier_' . $courier_id
    ]);
}



function on_awbs_outstanded(Builder|EloquentBuilder &$records, $courier_id,  $params = [])
{

    $records->update([
        ...$params,
        'current_location' => 'outstanding_courier_' . $courier_id
    ]);
}
function on_awbs_untracked(Builder|EloquentBuilder &$records, $params = [])
{

    $records->update([
        ...$params,
        'current_location' => 'unknown'
    ]);
}


function on_awbs_undebriefed(Builder|EloquentBuilder &$records, $courier_id,  $params = [])
{

    $records->update([
        ...$params,
        'current_location' => 'courier_' . $courier_id
    ]);
}


function on_awbs_dispatched(Builder|EloquentBuilder &$records, $courier_id,  $params = [])
{

    $records->update([
        ...$params,
        'current_location' => 'courier_' . $courier_id
    ]);
}
