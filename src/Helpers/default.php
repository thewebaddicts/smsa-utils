<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use twa\smsautils\Http\Controllers\OneSignalController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use twa\smsautils\Models\ActivityLog;

use Illuminate\Support\Facades\Storage;
use twa\smsautils\Enums\AwbStatusEnum;
use twa\smsautils\Enums\PickupStatusEnum;
use twa\smsautils\Jobs\TreatWorkflowActivity;
use twa\smsautils\Models\AccessToken;
use twa\smsautils\Models\Hub;
use twa\smsautils\Models\PickupRequest;
use twa\smsautils\Models\AttributeSchema;
use twa\smsautils\Facades\AwbStatusFacade;
use twa\smsautils\Models\ExceptionTriggerReason;
use twa\smsautils\Models\ShipmentStatus;

if (!function_exists('format_code_branch')) {

    function format_code_branch(?string $code, ?string $branch): ?string
    {
        if (!$code) return $branch ?? null;

        if (!$branch || $code === $branch) {
            return $code;
        }

        return "{$code} ({$branch})";
    }
}
if (!function_exists('generate_awb_number')) {
    function generate_awb_number($sequence, $prefix = "2", $identifier = '40')
    {
        // Ensure sequence is 8 digits (zero-padded if needed)
        $sequenceStr = str_pad($sequence, 8, "0", STR_PAD_LEFT);

        // Build first 11 digits
        $awbWithoutChecksum = $prefix . $identifier . $sequenceStr; // total 11 digits

        // Multipliers cycle
        $multipliers = [1, 5, 7];
        $sum = 0;

        // Apply multipliers starting from 11th digit backward
        $awbDigits = str_split($awbWithoutChecksum);
        $multiplierIndex = 0;

        for ($i = count($awbDigits) - 1; $i >= 0; $i--) {
            $digit = (int) $awbDigits[$i];
            $multiplier = $multipliers[$multiplierIndex];
            $sum += $digit * $multiplier;

            // Move to next multiplier (cycle through 1,5,7)
            $multiplierIndex = ($multiplierIndex + 1) % 3;
        }

        // Calculate remainder
        $remainder = $sum % 11;

        // Rules for check digit
        if ($remainder === 10 || $remainder === 0) {
            $checkDigit = 0;
        } else {
            $checkDigit = $remainder;
        }

        // Final 12-digit AWB
        return $awbWithoutChecksum . $checkDigit;

        // return str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
    }
}
if (!function_exists('awb_format')) {
    function awb_format($awb, $include_files = null, $product_group = null)
    {
        // dd($awb->activity);
        $data = [
            'awb' => $awb->awb,
            'awb_sequence' => $awb->awb_sequence,
            'id' => $awb->id,
            'nb_packages' => $awb->nb_packages,
            'master_awb' => $awb->master_awb,
            'shipment_id' => $awb->shipment_id,
            'status' => AwbStatusFacade::fromModel($awb->activity)->info(),
        ];

        if (!is_null($include_files)) {
            $data['files'] = (new twa\smsautils\Http\Controllers\DocumentSchemaController)
                ->getDocumentSchema('HAWB', $awb->destination_code, null, $include_files, $product_group);
        }

        return $data;
    }
}

if (!function_exists('identify_type')) {
    function identify_type($type)
    {
        switch ($type) {
            case 'rts':
                return 'rts';
            case 'cir':
                return 'cir';
            case 'ops-inbound':
                return 'ops-inbound';
            default:
                return null;
        }
        return null;
    }
}



if (!function_exists('validate_supervisor_credentials')) {
    function validate_supervisor_credentials($hub_id, $supervisor_email, $supervisor_password)
    {

        $credentials = get_supervisor_credentials($hub_id);

        $supervisor = collect($credentials)->first(function ($sup) use ($supervisor_email, $supervisor_password) {
            return $sup->email === $supervisor_email && $sup->password === md5($supervisor_password);
        });

        if (! $supervisor) {
            return false;
        }
        return $supervisor;
    }
}


if (!function_exists('get_supervisor_credentials')) {
    function get_supervisor_credentials($hub_id)
    {
        return DB::table('operators')
            ->where('hub_id', $hub_id)
            ->where('superadmin', 1)
            ->select('id', 'email', 'password', 'employee_id', 'name')
            ->get()
            ->toArray();
    }
}

//format 20-04-2026 16:48:00
if (!function_exists('format_date_time_with_timezone_local')) {
    function format_date_time_with_timezone_local($datetime, $timezone)
    {

        if (!$datetime) {
            return null;
        }

        return now()->parse($datetime)->setTimezone($timezone)->format('d-m-Y H:i:s');
    }
}

// format 20 Apr 2026 4:48 PM
if (!function_exists('format_date_time_with_timezone')) {
    function format_date_time_with_timezone($datetime, $timezone)
    {
        if (!$datetime) {
            return null;
        }

        return now()->parse($datetime)->setTimezone($timezone)->format('d M Y h:i A');
    }
}

if (!function_exists('format_time_with_timezone')) {
    function format_time_with_timezone($time, $timezone)
    {

        if (!$time) {
            return null;
        }

        return now()->parse($time)->setTimezone($timezone)->format('h:i A');
    }
}

if (!function_exists('format_date_with_timezone')) {
    function format_date_with_timezone($date, $timezone)
    {
        if (!$date) {
            return null;
        }

        return now()->parse($date)->setTimezone($timezone)->format('d M Y');
    }
}



if (!function_exists('get_pickup_available_date_time')) {
    function get_pickup_available_date_time($pickup_hub, $pickup_route_id)
    {
        $pickupForm = [
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => '16:00-16:30',
        ];

        return $pickupForm;
    }
}
if (!function_exists('find_overlapping_pending_pickup')) {
    function find_overlapping_pending_pickup(
        $clientId,
        $addressId,
        string $pickupDate,
        string $pickupTimeFrom,
        string $pickupTimeTo,
        ?int $excludePickupId = null
    ): ?PickupRequest {
        return PickupRequest::query()
            ->whereNull('deleted_at')
            ->when(!is_null($clientId), function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->where('address_id', $addressId)
            ->whereDate('pickup_date', $pickupDate)
            ->where('status', PickupStatusEnum::PICKUP_CREATED->value)
            ->when($excludePickupId, function ($query) use ($excludePickupId) {
                $query->where('id', '!=', $excludePickupId);
            })
            ->where(function ($query) use ($pickupTimeFrom, $pickupTimeTo) {
                // overlap condition:
                // existing_from <= new_to AND existing_to >= new_from
                $query->where('pickup_time_from', '<=', $pickupTimeTo)
                    ->where('pickup_time_to', '>=', $pickupTimeFrom);
            })
            ->first();
    }
}



if (!function_exists('create_pickup_from_shipment')) {
    function create_pickup_from_shipment(
        \twa\smsautils\Models\Shipment $shipment,
        $operator = null, // it can be null if the shipment is created by a system user
        array | null $form_data = null,
        bool $has_client = true,
        array $expected_awbs = [],
        bool $is_cir = false,
        bool $throw_exception = true

    ) {

        $awbs = \twa\smsautils\Models\Awb::where('shipment_id', $shipment->id)
            ->where('current_location', 'LIKE', 'shipper_address_%')
            ->when(!empty($expected_awbs), function ($query) use ($expected_awbs) {
                $query->whereIn('awb', $expected_awbs);
            })
            ->get();

        if (count($awbs) === 0) {
            return false;
        }
        $firstAwb = $awbs->first();

        if (!$firstAwb->origin_hub_id) {
            return false;
        }
        $hub = Hub::find($firstAwb->origin_hub_id);


        $total_height = 0;
        $total_width = 0;
        $total_length = 0;
        $total_weight = 0;
        foreach ($awbs as $awb) {
            $total_height += $awb->declared_height_cm;
            $total_width += $awb->declared_width_cm;
            $total_length += $awb->declared_length_cm;
            $total_weight += $awb->declared_weight_g / 1000;
        }
        $address = DB::table('addresses')->where('id', $firstAwb->sender_address_id)->first();

        $route_id = $address ? find_route_by_address([
            'city' => $address->city ?? null,
            'province' => $address->province ?? null,
            'postal_code' => $address->postal_code ?? null,
            'country' => $address->country ?? null,
        ]) : null;

        $hub_id = $hub->id;
        if ($route_id) {
            $route_hub_id = DB::table('routes')->where('id', $route_id)->value('hub_id');
            if (!empty($route_hub_id)) {
                $hub_id = $route_hub_id;
            }
        }
        $courier_id = null;
        if ($route_id) {
            $assignment = DB::table('route_assignments')
                ->where('route_id', $route_id)
                ->whereNull('deleted_at')
                ->orderByDesc('assigned_at')
                ->first();

            if ($assignment) {
                $courier_id = $assignment->courier_id;
            }
        }

        if (!$form_data) {
            $form_data = get_pickup_available_date_time($hub, $route_id);
        }

        $pickupTimes = explode('-', $form_data['pickup_time']);
        $pickupTimeFrom = isset($pickupTimes[0]) ? now()->parse(trim($pickupTimes[0]))->format('H:i') : null;
        $pickupTimeTo   = isset($pickupTimes[1]) ? now()->parse(trim($pickupTimes[1]))->format('H:i') : null;
        $pickupDate = now()->parse($form_data['pickup_date'])->format('Y-m-d');

        $firstAwb = $awbs->first();


        if (!$is_cir) {


            $existingPendingPickup = find_overlapping_pending_pickup(
                clientId: $has_client ? $shipment->client_id : null,
                addressId: $firstAwb->sender_address_id,
                pickupDate: $pickupDate,
                pickupTimeFrom: $pickupTimeFrom,
                pickupTimeTo: $pickupTimeTo
            );
            if ($existingPendingPickup) {
                // $existingPendingPickup->already_exists = true;
                if ($throw_exception) {
                    throw new \Exception('A pending pickup already exists for the same shipper address.');
                }
                return $existingPendingPickup;
            }
        }

        // $courier_id = courier_assignment_on_route($route_id);
        // if (!$courier_id) {
        //     $courier_id = null;
        // }

        $pickupRequest = new \twa\smsautils\Models\PickupRequest();

        $pickupRequest->operator_id = $operator ? $operator->id : null;
        $pickupRequest->hub_id = $hub_id;
        // $pickupRequest->address_id = $firstAwb->sender_address_id;
        $pickupRequest->address_id = $shipment->sender_address_id;

        // build and persist pickup address snapshot
        $pickupAddress = \twa\smsautils\Models\Address::where('id', $shipment->sender_address_id)
            ->whereNull('deleted_at')
            ->first();
        if (!$pickupAddress) {
            return false;
        }
        if ($pickupAddress) {
            $snapshot = function_exists('buildAddressSnapshot')
                ? \buildAddressSnapshot($pickupAddress)
                : [
                    'id' => $pickupAddress->id,
                    'label' => $pickupAddress->label,
                    'company' => $pickupAddress->company,
                    'attention' => $pickupAddress->attention,
                    'address1' => $pickupAddress->address1,
                    'address2' => $pickupAddress->address2,
                    'email' => $pickupAddress->email,
                    'phone' => $pickupAddress->phone,
                    'secondary_phone' => $pickupAddress->secondary_phone,
                    'area_code' => $pickupAddress->area_code,
                    'city' => $pickupAddress->city,
                    'province' => $pickupAddress->province,
                    'country' => $pickupAddress->country,
                    'address_type' => $pickupAddress->address_type,
                    'latitude' => $pickupAddress->latitude,
                    'longitude' => $pickupAddress->longitude,
                    'address_for' => $pickupAddress->address_for,
                    'target_id' => $pickupAddress->target_id,
                    'client_id' => $pickupAddress->client_id,
                    'created_at' => $pickupAddress->created_at,
                    'updated_at' => $pickupAddress->updated_at,
                ];

            $pickupRequest->address_snapshot = $snapshot;
        }

        $pickupRequest->expected_awbs = $expected_awbs;
        $pickupRequest->nb_packages = $awbs->count();
        $pickupRequest->total_weight = $total_weight;
        $pickupRequest->dimension_height = $total_height;
        $pickupRequest->dimension_width = $total_width;
        $pickupRequest->dimension_length = $total_length;
        $pickupRequest->client_id = $has_client ? $shipment->client_id : null;

        $pickupRequest->pickup_date = $pickupDate;
        $pickupRequest->pickup_time_from = $pickupTimeFrom;
        $pickupRequest->pickup_time_to = $pickupTimeTo;
        $pickupRequest->instruction = $form_data['instruction'] ?? null;
        $pickupRequest->route_id = $route_id;
        $pickupRequest->courier_id = $courier_id;
        $pickupRequest->assigned_at = now();
        $pickupRequest->expected_awbs = $expected_awbs;

        $pickupRequest->status = PickupStatusEnum::PICKUP_CREATED->value;
        $pickupRequest->is_return = $is_cir ? true : false;

        $pickupRequest->save();
        $pickupRequest->reference_code = 'PU-' . str_pad($pickupRequest->id, 4, '0', STR_PAD_LEFT);
        $pickupRequest->save();

        $pickupRequest->logActivity('Pickup request created', $operator);

        return $pickupRequest;
    }
}


if (!function_exists('operation_activity_log')) {
    function operation_activity_log($mode, $operator_id, $operator_email, $record_id, $record_type, $payload, $created_at = null)
    {

        $log = new ActivityLog();
        $log->mode = $mode;
        $log->operator_id = $operator_id;
        $log->operator_email = $operator_email;
        $log->record_id = $record_id;
        $log->record_type = $record_type;
        $log->payload = $payload;
        $log->created_at = $created_at ? $created_at : now();
        $log->save();
    }
}


function send_otp_by_email($email, $otp)
{
    // try {
    $appName = config('app.name');

    $htmlContent = "
                <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px;'>
                        <div style='max-width: 500px; margin: auto; background: #fff; border-radius: 8px; padding: 20px;'>
                            <h2 style='color: #333;'>SMSA EXPRESS</h2>
                            <p style='font-size: 16px;'>Hello,</p>
                            <p style='font-size: 16px;'>Your OTP is:</p>
                            <h1 style='text-align: center; background: #007bff; color: white; padding: 10px; border-radius: 6px;'>$otp</h1>
                            <p style='font-size: 14px; color: #555;'>This code will expire shortly. Please do not share it with anyone.</p>
             
                        </div>
                    </body>
                </html>
            ";

    Mail::html($htmlContent, function ($message) use ($email) {
        $message->to($email)
            ->subject('Your OTP Code');
    });

    return true;
}


if (!function_exists('unique_rule')) {
    function unique_rule($table, $column, $ignore_id = null)
    {
        $rule = Rule::unique($table, $column)
            ->where(function ($query) {
                $query->whereNull('deleted_at');
            });

        if ($ignore_id) {
            $rule->ignore($ignore_id, 'id');
        }

        return $rule;
    }
}

if (!function_exists('exists_rule')) {
    function exists_rule($table, $column)
    {
        return  Rule::exists($table, $column)->whereNull('deleted_at');
    }
}


if (!function_exists('create_record')) {
    function create_record(&$model, $fields, $save = false)
    {

        foreach ($fields as $key => $value) {
            $model->{$key} = $value;
        }

        if ($save) {
            $model->save();
        }
    }
}


if (!function_exists('awb_pdf_url')) {
    function awb_pdf_url($awb)
    {
        $base_url = env('AWB_URL', 'https://opssmsaexpressco_6874ad59df6e7.twalab.cloud');
        return $base_url . '/awb/' . $awb . '/pdf/' . generate_awb_token($awb);
    }
}


if (!function_exists('format_id_name')) {
    function format_id_name($id, $first_name, $last_name)
    {
        return $id . ' | ' . trim($first_name . ' ' . $last_name);
    }
}


if (!function_exists('money_object')) {
    function money_object($value, $currency, $round = true): array
    {
        $numeric = (float) $value;
        //hovig's update
        if ($round) {
            $number = round($numeric, 2);
        } else {
            $number = $numeric;
        }

        return [
            'value' => $number,
            'formatted' =>  $round ? trim(number_format($number, 2, '.', '') . ' ' . $currency) : trim(number_format(round($number, 2), 2, '.', '') . ' ' . $currency),
            'currency' => $currency,
        ];
    }
}


if (!function_exists('log_activity')) {

    function log_activity(
        $table,
        $status_code,
        $target = null,
        $target_id = null,
        $activity_by_id = null,
        $activity_by_type = null,
        $comment = null,
        $files = [],
        $activity_by = null,
        $activity_location = null,
        $created_at = null,
        $source = null
    ) {


        $data = DB::table($table)->insertGetId([
            'target' => $target,
            'target_id' => $target_id,
            'status_code' => $status_code,
            'activity_by_id' => $activity_by_id,
            'activity_by_type' => $activity_by_type,
            'comment' => $comment,
            'files' => $files ? json_encode($files) : null,
            'activity_location' => $activity_location ?? null,
            'activity_by' => $activity_by ?? null,
            'created_at' => $created_at ? $created_at : now(),
            'updated_at' => $created_at ? $created_at : now(),
            'source' => $source ?? null
        ]);
        return $data;
    }
}


if (!function_exists('log_awb_activity')) {

    function log_awb_activity(
        $status_code,
        $target,
        $target_id,
        $activity_by_id,
        $activity_by_type,
        $comment = null,
        $files = [],
        $activity_by = null,
        $activity_location = null,
        $created_at = null,
        $source = null
    ) {
        // Keep AWB's "last activity" timestamp in sync with the activity log row.
        $activityTime = $created_at ?? now();

        $awb_activity_log_id = log_activity(
            'awb_activities',
            $status_code,
            $target,
            $target_id,
            $activity_by_id,
            $activity_by_type,
            $comment,
            $files,
            $activity_by,
            $activity_location,
            $activityTime,
            $source

        );

        if ($target_id !== null) {
            DB::table('awbs')->where('id', $target_id)->update([
                'last_activity_at' => $activityTime,
            ]);
        }

        $shipment_status = ShipmentStatus::query()
            ->where('code', $status_code)
            ->where('module', 'awb')
            ->whereNull('deleted_at')
            ->first();

        if ($shipment_status?->exception_trigger_reason_id) {
            $triggerReason = ExceptionTriggerReason::query()
                ->with('exceptionCategory')
                ->where('id', $shipment_status->exception_trigger_reason_id)
                ->whereNull('deleted_at')
                ->first();

            if ($triggerReason?->exceptionCategory) {
                $exceptionPayload = [
                    'targetable_id' => $target_id,
                    'targetable_type' => 'awb',
                    'exception_category_id' => $triggerReason->exceptionCategory->id,
                    'exception_trigger_reason_id' => (int) $shipment_status->exception_trigger_reason_id,
                    'comments' => $comment,
                    'files' => $files ?? [],
                    'created_by_id' => $activity_by_id,
                    'created_by_type' => $activity_by_type,
                ];





                create_record_in_exception($exceptionPayload);
            }
        }

        \twa\smsautils\Events\OnAWBActivityLog::dispatch($awb_activity_log_id);
    }
}
if (!function_exists('log_awbs_activity')) {
    function log_awbs_activity(Builder $builder) {}
}

if (!function_exists('query_options_new_record')) {
    function query_options_new_record($id)
    {

        return [
            "id" => $id
        ];
    }
}


if (!function_exists('query_options_response')) {
    function query_options_response(
        $table,
        $columnValue,
        $columnLabel,
        $params = [],
        $extraFields = [],
        $separator = " ",
        $except = [],
        $wrapExtraDescriptions = false,
        $options = []
    ) {

        $values = request()->input('values');

        if (is_numeric($values)) {
            $values = [$values];
        } elseif (!is_array($values)) {
            $values = [];
        }

        // Build base query
        $baseQuery = DB::table($table)
            ->when(is_array($except) && count($except) > 0, function ($query) use ($except, $columnValue) {
                $query->whereNotIn($columnValue, $except);
            })
            ->whereNull('deleted_at');

        // Apply filters from params
        foreach ($params as $field => $value) {
            if (is_array($value)) {
                $type = $value['type'] ?? null;
                switch ($type) {
                    case 'array_to_array':
                        $baseQuery->where(function ($q) use ($value, $field) {
                            foreach ($value['value'] as $item) {
                                $q->orWhere($field, 'LIKE', '%"' . $item . '"%');
                            }
                        });
                        break;

                    case 'in':
                        $baseQuery->whereIn($field, $value['value']);
                        break;

                    default:
                        $baseQuery->where($field, $value['operand'] ?? '=', $value['value']);
                }
            } else {
                $baseQuery->where($field, $value);
            }
        }


        if (request()->input('search')) {
            $search = request()->input('search');
            $baseQuery->where(function ($query) use ($columnLabel, $extraFields, $search) {
                $query->where($columnLabel, 'like', "%{$search}%");
                foreach (collect($extraFields)->flatten()->values()->toArray() as $extraField) {
                    $query->orWhere($extraField, 'like', "%{$search}%");
                }
            });
        }


        $sortBy = $options['sort_by'] ?? 'value';
        $sortDirection = $options['sort_direction'] ?? 'asc';

        if (count($values) > 0) {
            $baseQuery->orderByRaw("CASE WHEN {$columnValue} IN (" . implode(',', array_map('intval', $values)) . ") THEN 0 ELSE 1 END")
                ->orderBy($sortBy === 'label' ? $columnLabel : $columnValue, $sortDirection);
        } else {
            $baseQuery->orderBy($sortBy === 'label' ? $columnLabel : $columnValue, $sortDirection);
        }


        return $baseQuery->paginate(400)->through(function ($item) use ($columnValue, $columnLabel, $extraFields, $separator, $wrapExtraDescriptions) {

            $extraFields = collect($extraFields)->map(function ($fieldName) use ($item, $separator) {
                if (!is_array($fieldName)) {
                    return $item->{$fieldName} ?? null;
                }

                return collect($fieldName)->map(function ($iteration) use ($item) {
                    return $item->{$iteration} ?? null;
                })->filter()->values()->implode($separator);
            })->filter()->values()->toArray();

            return [
                'value' => $item->$columnValue,
                'label' => $item->$columnLabel,
                'extra_descriptions' => $wrapExtraDescriptions
                    ? collect($extraFields)->map(fn($desc) => [$desc])->toArray()
                    : $extraFields,
            ];
        });
    }
}



if (!function_exists('get_currencies_rates')) {
    function get_currencies_rates()
    {

        $currencies = Cache::get('currencies_rates');

        if (!$currencies) {
            $currencies = DB::table('currencies')->whereNull('deleted_at')->pluck('rate', 'iso')->toArray();
            Cache::put('currencies_rates', $currencies, 86400);

            return $currencies;
        }

        return $currencies;
    }
}


if (!function_exists('currencies_conversion')) {
    function currencies_conversion($amounts, $currency)
    {
        $total = 0;

        foreach ($amounts as $amount) {
            $total += convert_from_to($amount['value'], $amount['currency'], $currency);
        }

        return $total;
    }
}


if (!function_exists('convert_from_to')) {
    function convert_from_to($amount, $from_currency, $to_currency)
    {

        if ($from_currency == $to_currency) {
            return $amount;
        }

        $rates = get_currencies_rates();

        if (!isset($rates[$from_currency]) || !isset($rates[$to_currency])) {
            return 0;
        }

        return ($amount * $rates[$to_currency]) / $rates[$from_currency];
    }
}


if (!function_exists('send_courier_notification')) {
    function send_courier_notification($courier_id, $code)
    {

        $notification_template = DB::table('notification_templates')->where('code', $code)->first();
        if (!$notification_template) {
            return false;
        }

        DB::table('courier_notifications')->insert([
            'courier_id' => $courier_id,
            'notification_template_id' => $notification_template->id,
            'read_at' => null
        ]);

        return true;
    }
}




if (!function_exists('find_route_by_address')) {
    /**
     * Find route by address using city and postal code matching
     */
    function find_route_by_address(array $address): ?int
    {
        // First, try to find route by city

        $routeByCity = DB::table('cities')
            ->where('old_sys_mapping', $address['city'])
            // ->where('province', $address['province'])
            ->where('country', $address['country'])
            ->whereNull('deleted_at')
            ->whereNotNull('route_id')
            ->select('route_id')
            ->first();

        if ($routeByCity) {
            return $routeByCity->route_id;
        }

        $routeByCity = DB::table('cities')
            ->where('code', $address['city'])
            ->where('province', $address['province'])
            ->where('country', $address['country'])
            ->whereNull('deleted_at')
            ->select('route_id')
            ->first();

        if ($routeByCity) {
            return $routeByCity->route_id;
        }

        // If no city route found and no postal code, return null
        if (empty($address['postal_code'])) {
            return null;
        }

        $postalCode = $address['postal_code'];

        // Try to find routes by postal code with different specificity levels
        $routes = find_routes_by_postal_code($address);

        if (count($routes) == 0) {
            return null;
        }

        // Try to match by numeric range first
        if (is_numeric($postalCode)) {
            $postalCode = (float) $postalCode;
            $rangeRoute = $routes->first(function ($route) use ($postalCode) {
                return isset($route->start_range, $route->end_range) &&
                    $route->start_range <= $postalCode && $route->end_range >= $postalCode &&
                    is_null($route->pattern);
            });
            if ($rangeRoute) {
                return $rangeRoute->route_id;
            }
        }

        // Try to match by pattern
        foreach ($routes as $route) {
            if ($route->pattern && is_null($route->start_range) && is_null($route->end_range) && strpos($postalCode, $route->pattern) === 0) {
                return $route->route_id;
            }
        }

        return null;
    }
}

if (!function_exists('find_routes_by_postal_code')) {
    /**
     * Find routes by postal code with fallback specificity levels
     */
    function find_routes_by_postal_code(array $address): \Illuminate\Support\Collection
    {
        // Level 1: Exact match (country, province, city)
        $routes = DB::table('route_by_area_codes')
            ->whereNull('deleted_at')
            ->where('country', $address['country'])
            ->where('province', $address['province'])
            ->where('city', $address['city'])
            ->get();

        if (count($routes) > 0) {
            return $routes;
        }

        // Level 2: Country and province only
        $routes = DB::table('route_by_area_codes')
            ->whereNull('deleted_at')
            ->where('country', $address['country'])
            ->where('province', $address['province'])
            ->whereNull('city')
            ->get();

        if (count($routes) > 0) {
            return $routes;
        }

        // Level 3: Country only
        $routes = DB::table('route_by_area_codes')
            ->whereNull('deleted_at')
            ->where('country', $address['country'])
            ->whereNull('province')
            ->whereNull('city')
            ->get();

        return $routes;
    }
}


if (!function_exists('courier_assignment_on_route')) {
    /**
     * Find route by address using city and postal code matching
     */
    function courier_assignment_on_route($route)
    {


        if (!$route) {
            return null;
        }

        $couriers = DB::table('route_assignments')
            ->join('couriers', 'couriers.id', '=', 'route_assignments.courier_id')
            ->where('route_assignments.route_id', $route->id)
            ->whereNull('route_assignments.deleted_at')
            ->whereNull('couriers.deleted_at')
            ->whereNotNull('couriers.assigned_vehicle')
            ->distinct()
            ->pluck('route_assignments.courier_id')
            ->values();

        if ($couriers->isEmpty()) {
            return null;
        }

        $runsheetLoads = DB::table('runsheets_awbs')
            ->whereIn('courier_id', $couriers)
            ->whereNull('deleted_at')
            ->whereNull('debriefed_at')
            ->where('status', '!=', AwbStatusEnum::OUT_FOR_DELIVERY->value)
            ->select('courier_id', DB::raw('COUNT(*) as count'))
            ->groupBy('courier_id')
            ->pluck('count', 'courier_id');

        // $pickupLoads = DB::table('session_pickup_awbs')
        //     ->whereIn('courier_id', $couriers)
        //     ->whereNull('deleted_at')
        //     ->whereNull('debriefed_at')
        //     ->select('courier_id', DB::raw('COUNT(*) as count'))
        //     ->groupBy('courier_id')
        //     ->pluck('count', 'courier_id');
        $pickupLoads = DB::table('pickup_requests')
            ->whereIn('courier_id', $couriers)
            ->where('pickup_date', now()->format('Y-m-d'))
            ->whereNull('deleted_at')
            ->where('status', \twa\smsautils\Enums\PickupStatusEnum::PICKUP_CREATED->value)
            ->select('courier_id', DB::raw('COUNT(*) as count'))
            ->groupBy('courier_id')
            ->pluck('count', 'courier_id');

        return $couriers
            ->mapWithKeys(function ($id) use ($runsheetLoads, $pickupLoads) {
                return [
                    $id => ($runsheetLoads[$id] ?? 0) + ($pickupLoads[$id] ?? 0)
                ];
            })
            ->sort()   // smallest load first
            ->keys()
            ->first();
    }
}


if (!function_exists('generate_awb_token')) {
    function generate_awb_token(string $awb): string
    {
        $secret = env('AWB_SECRET_KEY');
        return md5($awb . $secret);
    }
}

if (!function_exists('send_push_notification')) {
    function send_push_notification($titles, $messages, $conditions = [], $data = [], $image_url = null, $playerID = null)
    {
        $config = config('omnipush.onesignal');
        (new OneSignalController($config['data']))->sendPush($titles, $messages, $conditions, $data, $image_url, $playerID);
    }
}

if (!function_exists('send_client_otp')) {
    function send_client_otp($phone, $otp)
    {

        $clientId = env('INFINITO_CLIENT_ID');
        $clientPassword = env('INFINITO_CLIENT_PASSWORD');
        $senderId = env('INFINITO_SENDER_ID');
        $message = "This is an OTP code {$otp}";


        $query = http_build_query([
            'clientid' => $clientId,
            'clientpassword' => $clientPassword,
            'from' => $senderId,
            'to' => $phone,
            'text' => $message
        ]);

        $url = "https://api.goinfinito.me/unified/v2/send?" . $query;

        try {

            $response = file_get_contents($url);

            // Log::info("OTP API raw response", ['response' => $response]);


            parse_str($response, $result);

            if (isset($result['statuscode']) && $result['statuscode'] === '0') {
                // Log::info("OTP sent successfully", [
                //     'phone' => $phone,
                //     'otp' => $otp,
                //     'result' => $result
                // ]);
                return true;
            }

            // Log::error("Failed to send OTP", ['phone' => $phone, 'response' => $result]);
            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending OTP", [
                'phone' => $phone,
                'otp' => $otp,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}



if (!function_exists('get_file_info')) {
    function get_file_info($file_id, $disk = 'bucket')
    {
        $cacheKey = "file_{$file_id}";

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($file_id, $disk) {
            $file = DB::table('files')->where('id', $file_id)->first();

            if (!$file) {
                return null;
            }

            return [
                'id' => $file->id,
                'name' => $file->original_name,
                'url' => Storage::disk($disk)->url($file->file_path),
            ];
        });
    }
}


if (!function_exists('get_files_info')) {
    function get_files_info(array $file_ids, $disk = 'bucket')
    {
        $filesInfo = [];

        foreach ($file_ids as $file_id) {
            $cacheKey = "file_{$file_id}";

            $filesInfo[] = Cache::remember($cacheKey, now()->addHours(2), function () use ($file_id, $disk) {
                $file = DB::table('files')->where('id', $file_id)->first();

                if (!$file) {
                    return null;
                }

                return [
                    'id' => $file->id,
                    'name' => $file->original_name,
                    'url' => Storage::disk($disk)->url($file->file_path),
                ];
            });
        }


        return array_filter($filesInfo);
    }
}

if (!function_exists('format_date_time')) {

    function format_date_time($date, $format = 'd-m-Y H:i:s')
    {
        if (!$date) {
            return null;
        }

        if ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }
}
if (!function_exists('convert_status_to_number')) {
    function convert_status_to_number($status)
    {

        $statuses = get_workflow_statuses()->pluck('value_code', 'value')->toArray();

        if (!isset($statuses[$status])) {
            return null;
        }

        return $statuses[$status];
    }
}

if (!function_exists('get_workflow_statuses')) {
    function get_workflow_statuses()
    {
        $attempts = max(1, (int) request()->input('nb_attempts', 1));
        $lang = app()->getLocale();

        return DB::table('shipment_statuses')
            ->whereNull('deleted_at')
            ->whereJsonContains('shipment_status_tags', 'WORKFLOW')
            ->orderBy('id')
            ->get()
            ->filter(function ($status) use ($attempts) {
                if (preg_match('/^SHAT-(\d+)$/', $status->code, $matches)) {
                    return (int) $matches[1] <= $attempts;
                }

                return true;
            })
            ->values()
            ->map(function ($status, $index) use ($lang) {
                $tags = [];
                if (is_string($status->shipment_status_tags) && $status->shipment_status_tags !== '') {
                    $tags = json_decode($status->shipment_status_tags, true) ?: [];
                } elseif (is_array($status->shipment_status_tags)) {
                    $tags = $status->shipment_status_tags;
                }

                $label = $lang === 'ar' ? ($status->label_ar ?? '') : ($status->label_en ?? '');

                return [
                    'value' => $status->code,
                    'value_code' => 100 + $index,
                    'label' => $label . ' (' . $status->code . ')',
                    'icon' => $status->icon,
                    'color_bg' => $status->color_bg,
                    'color_text' => $status->color_text,
                    'description' => $lang === 'ar' ? ($status->description_ar ?? '') : ($status->description_en ?? ''),
                    'category' => $status->shipment_status_category_id ?? null,
                    'tags' => $tags,
                ];
            });
    }
}

if (!function_exists('is_valid_awb')) {
    function is_valid_awb($awb)
    {
        // Must be exactly 12 digits
        if (!preg_match('/^\d{12}$/', $awb)) {
            return false;
        }

        // First 11 digits
        $awbWithoutChecksum = substr($awb, 0, 11);

        // Provided checksum
        $givenChecksum = (int) substr($awb, 11, 1);

        $multipliers = [1, 5, 7];
        $sum = 0;
        $multiplierIndex = 0;

        $digits = str_split($awbWithoutChecksum);

        // Apply multipliers from right to left
        for ($i = count($digits) - 1; $i >= 0; $i--) {
            $digit = (int) $digits[$i];
            $multiplier = $multipliers[$multiplierIndex];

            $sum += $digit * $multiplier;

            $multiplierIndex = ($multiplierIndex + 1) % 3;
        }

        $remainder = $sum % 11;

        if ($remainder === 10 || $remainder === 0) {
            $calculatedChecksum = 0;
        } else {
            $calculatedChecksum = $remainder;
        }

        return $calculatedChecksum === $givenChecksum;
    }
}
if (!function_exists('get_event_handler_label')) {
    /**
     * Get the label from an event handler class by event ID
     *
     * @param string|null $eventId
     * @return string|null
     */
    function get_event_handler_label(?string $eventId): ?string
    {
        if (!$eventId) {
            return null;
        }

        $config = config('event-config');

        if (!isset($config[$eventId])) {
            return null;
        }

        $handlerClass = is_array($config[$eventId])
            ? ($config[$eventId]['class'] ?? null)
            : $config[$eventId];

        if (!$handlerClass || !class_exists($handlerClass)) {
            return null;
        }

        try {
            $handler = app($handlerClass);
            if (method_exists($handler, 'label')) {
                return $handler->label();
            }
        } catch (\Exception $e) {
            Log::warning("Error getting label from handler: {$handlerClass}", [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
function send_awb_to_consignee(string $senderEmail, array $awbs = []): bool
{
    $appName = config('app.name', 'SMSA EXPRESS');
    $trackBaseUrl = config('services.smsa.track_url', 'https://track.smsaexpress.com/?awb=');

    $awbsHtml = '';

    foreach (($awbs ?? []) as $row) {
        $awb = e((string)($row['awb'] ?? ''));

        if (!$awb) continue;

        $trackUrl = e($trackBaseUrl . $awb);

        $awbsHtml .= "
            <div style='border:1px solid #eee; padding:12px; border-radius:10px; margin-bottom:10px;'>
                <div style='font-size:14px; color:#555;'>AWB</div>
                <div style='font-size:20px; font-weight:700; color:#111; margin:6px 0 10px;'>$awb</div>

                <a href='$trackUrl' target='_blank'
                   style='display:inline-block; padding:10px 14px; background:#28a745; color:#fff; text-decoration:none; border-radius:8px; font-size:14px;'>
                    Track Shipment
                </a>
            </div>
        ";
    }

    if ($awbsHtml === '') {
        $awbsHtml = "<div style='font-size:14px; color:#777;'>Your AWB will appear shortly.</div>";
    }

    $htmlContent = "
        <html>
            <body style='font-family: Arial, sans-serif; background:#f8f8f8; padding:20px;'>
                <div style='max-width:560px; margin:auto; background:#fff; border-radius:12px; padding:22px;'>
                    <h2 style='color:#111; margin:0;'>$appName</h2>
                    <p style='font-size:15px; color:#555; margin:8px 0 18px;'>
                        Your shipment is on the way 🚚
                    </p>

                    $awbsHtml
                </div>

                <div style='max-width:560px; margin:12px auto 0; text-align:center; font-size:12px; color:#999;'>
                    © " . date('Y') . " " . e($appName) . "
                </div>
            </body>
        </html>
    ";

    Mail::html($htmlContent, function ($message) use ($senderEmail) {
        $message->to($senderEmail)
            ->subject('Your shipment is on the way');
    });

    return true;
}



function send_shipment_success_to_sender(string $consigneeEmail, array $awbs): bool
{
    $appName = config('app.name', 'SMSA EXPRESS');

    $awbsHtml = '';

    foreach (($awbs ?? []) as $row) {
        $awb = e((string)($row['awb'] ?? ''));
        $url = (string)($row['awb_url'] ?? '');
        $safeUrl = $url ? e($url) : '';

        if (!$awb) continue;

        $awbsHtml .= "
            <div style='border:1px solid #eee; padding:12px; border-radius:10px; margin-bottom:10px;'>
                <div style='font-size:14px; color:#555;'>AWB</div>
                <div style='font-size:20px; font-weight:700; color:#111; margin:6px 0 10px;'>$awb</div>

                " . ($safeUrl ? "
                    <a href='$safeUrl' target='_blank'
                       style='display:inline-block; padding:10px 14px; background:#007bff; color:#fff; text-decoration:none; border-radius:8px; font-size:14px;'>
                        Download AWB
                    </a>
                " : "
                    <div style='font-size:13px; color:#777;'>AWB link not available.</div>
                ") . "
            </div>
        ";
    }

    if ($awbsHtml === '') {
        $awbsHtml = "<div style='font-size:14px; color:#777;'>No AWB available.</div>";
    }

    $htmlContent = "
        <html>
            <body style='font-family: Arial, sans-serif; background:#f8f8f8; padding:20px;'>
                <div style='max-width:560px; margin:auto; background:#fff; border-radius:12px; padding:22px;'>
                    <h2 style='color:#111; margin:0;'>$appName</h2>
                    <p style='font-size:15px; color:#555; margin:8px 0 18px;'>
                        Your Air Waybill (AWB)
                    </p>

                    $awbsHtml
                </div>

                <div style='max-width:560px; margin:12px auto 0; text-align:center; font-size:12px; color:#999;'>
                    © " . date('Y') . " " . e($appName) . "
                </div>
            </body>
        </html>
    ";

    Mail::html($htmlContent, function ($message) use ($consigneeEmail) {
        $message->to($consigneeEmail)
            ->subject('Your AWB');
    });

    return true;
}
if (!function_exists('render_dictionary_template')) {
    function render_dictionary_template($variables)
    {

        $flatten = function ($value, string $prefix = '') use (&$flatten) {
            $result = [];

            if (is_object($value)) {
                $value = (array) $value;
            }

            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $path = $prefix === '' ? (string) $k : $prefix . '.' . $k;

                    if (is_array($v) || is_object($v)) {
                        $result += $flatten($v, $path);
                        continue;
                    }

                    $result['{{' . $path . '}}'] = $v;
                }

                return $result;
            }

            if ($prefix !== '') {
                return ['{{' . $prefix . '}}' => $value];
            }

            return [];
        };

        if (!is_array($variables) && !is_object($variables)) {
            return [];
        }

        return $flatten($variables);
    }
}


function create_access_token($id, $type, $duration_minutes = 525600)
{
    $token = sprintf(
        '%s%s%s',
        '',
        $tokenEntropy = Str::random(40),
        hash('crc32b', $tokenEntropy)
    );
    $access_token = new AccessToken();
    $access_token->token = $id . "|" . $token;
    $access_token->tokenable_id = $id;
    $access_token->tokenable_type = $type;
    $access_token->expires_at = now()->addMinutes($duration_minutes);
    $access_token->save();

    return $access_token;
}


function process_event_status($status, $number_of_attempts = 1)
{
    $refusedStatuses = DB::table('shipment_statuses')
        ->whereNull('deleted_at')
        ->whereJsonContains('shipment_status_tags', 'REFUSED')
        ->pluck('code')
        ->filter()
        ->values()
        ->all();

    // if (empty($refusedStatuses)) {
    //     $refusedStatuses = [
    //         AwbStatusEnum::REFUSED_OPEN_SHIPMENT->value,
    //         AwbStatusEnum::REFUSED_MONEY->value,
    //         AwbStatusEnum::REFUSED_ALREADY_RECEIVED->value,
    //         AwbStatusEnum::REFUSED_NO_LONGER_NEEDED->value,
    //         AwbStatusEnum::REFUSED_DELAYED->value,
    //     ];
    // }

    if (in_array($status, $refusedStatuses, true)) {
        return AwbStatusEnum::REFUSED->value;
    }

    $list_of_exception_trips = DB::table('shipment_statuses')
        ->whereNull('deleted_at')
        ->whereJsonContains('shipment_status_tags', 'WORKFLOW_TRIP_EXCEPTION')
        ->pluck('code')
        ->filter()
        ->values()
        ->all();

    // if (empty($list_of_exception_trips)) {
    //     $list_of_exception_trips = AwbStatusEnum::exception_trips();
    // }

    if (in_array($status, $list_of_exception_trips)) {
        return "SHAT-" . $number_of_attempts;
    }

    return $status;
}
if (!function_exists('filter_date_timezone_to_utc')) {
    function filter_date_timezone_to_utc(array $keys, $timezone = 'UTC')
    {
        $formData = request()->all();
        foreach ($keys as $key) {
            if (!isset($formData[$key])) {
                continue;
            }
            $formData[$key] = now()->parse($formData[$key], $timezone)->utc();
        }

        return $formData;
    }
}
if (!function_exists('current_timezone_to_utc')) {
    function current_timezone_to_utc(array $keys, $timezone = 'UTC')
    {
        $result = [];
        foreach ($keys as $key => $value) {

            $result[$key] = now()->parse($value, $timezone)->utc();
        }

        return $result;
    }
}

if (!function_exists('identify_barcode')) {
    function identify_barcode(string $code): ?string
    {
        if (strlen($code) < 2) {
            return null;
        }

        $prefix = substr($code, 0, 2);

        switch ($prefix) {
            case '50':
                return 'mawb';
            case '30':
                return 'crn';
            case '40':
                return 'hst';

            case '80':
                return 'preprinted';
            default:
                return 'awb';
        }
    }
}
if (!function_exists('generate_reference_number')) {
    function generate_reference_number($id, $suffix)
    {
        return $suffix . str_pad($id, 4, '0', STR_PAD_LEFT);
    }
}
if (!function_exists('get_attributes_for_country')) {
    function get_attributes_for_country(string $attributeFor, string | null $country = null, ?array $data = null): array
    {
        $attributes = AttributeSchema::whereNull('deleted_at')
            ->where('attribute_for', strtoupper($attributeFor))
            ->when($country, function ($query) use ($country) {
                $query->where(function ($subQuery) use ($country) {
                    $subQuery->where('countries', 'like', '%"' . $country . '"%')
                        ->orWhere('countries', 'like', "%'" . $country . "'%")
                        ->orWhere('countries', '=', '[]')
                        ->orWhereNull('countries');
                });
            }, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('countries', '=', '[]')
                        ->orWhereNull('countries');
                });
            })
            ->get()
            ->sortByDesc(function ($attribute) use ($country) {
                if (!$country) {
                    return 0;
                }

                $countries = is_array($attribute->countries) ? $attribute->countries : [];
                return in_array($country, $countries, true) ? 1 : 0;
            })
            ->unique('attribute_key')
            ->map(fn($attribute) => $attribute->formatAttribute($data))
            ->values()
            ->toArray();

        return $attributes;
    }
}

if (!function_exists('get_documents')) {
    function get_documents($condition_slug, $document_for, $product_group, $visible = null, $data = null)
    {

        if (!is_array($condition_slug)) {
            $condition_slug = [$condition_slug];
        }

        $documents = DB::table('document_schemas')
            ->select('document_key',  'document_name')

            ->when(!is_null($visible), function ($query) use ($visible) {
                $query->where('visible_on_creation', $visible ? 1 : 0);
            })
            ->where('document_for', $document_for)
            ->whereIn('required_condition', $condition_slug)

            ->when($product_group, function ($query) use ($product_group) {
                $query->where(function ($q1) use ($product_group) {
                    $q1->where(function ($q) use ($product_group) {
                        $q->where('product_group', 'LIKE', '%"' . $product_group . '"%');
                        $q->orWhere('product_group', 'LIKE', "%'" . $product_group . "'%");
                    });
                    $q1->orWhereNull('product_group');
                    $q1->orWhere('product_group', '[]');
                });
            })
            ->whereNull('deleted_at')
            ->get()->map(function ($document) use ($data) {
                $documentValue = is_array($data) ? ($data[$document->document_key] ?? null) : null;
                $documentFileIds = $documentValue ? [(int) $documentValue] : [];

                return [
                    'document_name' => $document->document_name,
                    'document_key' => $document->document_key,

                    'sample_file_url' => config('sample-files.' . $document->document_key) ?? null,
                    'value' => !empty($documentFileIds) ? get_files_info($documentFileIds) : null
                ];
            });


        return $documents;
    }
    if (!function_exists('buildAddressSnapshot')) {
        function buildAddressSnapshot(?twa\smsautils\Models\Address $address): ?array
        {
            if (!$address) {
                return null;
            }

            return [
                'id' => $address->id ?? null,
                'label' => $address->label ?? null,
                'company' => $address->company ?? null,
                'attention' => $address->attention ?? null,
                'address1' => $address->address1 ?? null,
                'address2' => $address->address2 ?? null,
                'email' => $address->email ?? null,
                'phone' => $address->phone ?? null,
                'secondary_phone' => $address->secondary_phone ?? null,
                'area_code' => $address->area_code ?? null,
                'city' => $address->city ?? null,
                'province' => $address->province ?? null,
                'country' => $address->country ?? null,
                'address_type' => $address->address_type ?? null,
                'latitude' => isset($address->latitude) ? (float) $address->latitude : null,
                'longitude' => isset($address->longitude) ? (float) $address->longitude : null,
                'address_for' => $address->address_for ?? null,
                'target_id' => $address->target_id ?? null,
                'client_id' => $address->client_id ?? null,
                'created_at' => $address->created_at ?? null,
                'updated_at' => $address->updated_at ?? null,
            ];
        }
    }


    if (!function_exists('address_snapshot_json_sql')) {
        /**
         * MySQL expression to read a scalar from address snapshot JSON (keys match buildAddressSnapshot()).
         *
         * @param  string  $qualifiedColumn  e.g. shipments.receiver_address_snapshot
         * @param  string  $key  JSON key (alphanumeric and underscore only)
         */
        function address_snapshot_json_sql(string $qualifiedColumn, string $key): string
        {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                throw new InvalidArgumentException('Invalid address snapshot JSON key.');
            }

            $segments = explode('.', $qualifiedColumn);
            if (count($segments) === 2) {
                $qualified = '`' . $segments[0] . '`.`' . $segments[1] . '`';
            } elseif (count($segments) === 1) {
                $qualified = '`' . $segments[0] . '`';
            } else {
                throw new InvalidArgumentException('address_snapshot_json_sql expects table.column or column.');
            }

            $path = '$.' . $key;
            $pathLiteral = DB::getPdo()->quote($path);

            return "JSON_UNQUOTE(JSON_EXTRACT({$qualified}, {$pathLiteral}))";
        }
    }

    if (!function_exists('address_snapshot_json_select')) {
        /**
         * For query builder ->select(): one JSON field from a snapshot column, optionally aliased.
         *
         * @param  string|null  $as  result alias (e.g. address)
         * @return \Illuminate\Database\Query\Expression
         */
        function address_snapshot_json_select(string $qualifiedColumn, string $key, ?string $as = null)
        {
            $sql = address_snapshot_json_sql($qualifiedColumn, $key);
            if ($as !== null && $as !== '') {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $as)) {
                    throw new InvalidArgumentException('Invalid SQL alias for address snapshot select.');
                }
                $sql .= ' as `' . str_replace('`', '``', $as) . '`';
            }

            return DB::raw($sql);
        }
    }

    if (!function_exists('address_snapshot_json_where')) {
        /**
         * Apply a where condition against a key inside an address snapshot JSON column.
         *
         * Example:
         * address_snapshot_json_where($query, 'shipments.receiver_address_snapshot', 'country', '=', 'LB');
         */
        function address_snapshot_json_where(
            $query,
            string $qualifiedColumn,
            string $key,
            string $operator,
            $value
        ) {
            $allowedOperators = ['=', '!=', '<>', '>', '>=', '<', '<=', 'like', 'not like'];
            $normalizedOperator = strtolower(trim($operator));

            if (!in_array($normalizedOperator, $allowedOperators, true)) {
                throw new InvalidArgumentException('Invalid operator for address_snapshot_json_where.');
            }

            $sql = address_snapshot_json_sql($qualifiedColumn, $key);

            if ($value === null) {
                if (in_array($normalizedOperator, ['!=', '<>', 'not like'], true)) {
                    return $query->whereRaw("{$sql} IS NOT NULL");
                }

                return $query->whereRaw("{$sql} IS NULL");
            }

            return $query->whereRaw("{$sql} {$normalizedOperator} ?", [$value]);
        }
    }
    if (!function_exists('create_token')) {
        function create_token()
        {
            return md5(uniqid() . env('APP_KEY')) . md5(uniqid() . env('APP_KEY'));
        }
    }


}
