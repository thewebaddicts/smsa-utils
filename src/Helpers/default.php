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
        $data = [
            'awb' => $awb->awb,
            'awb_sequence' => $awb->awb_sequence,
            'id' => $awb->id,
            'nb_packages' => $awb->nb_packages,
            'master_awb' => $awb->master_awb,
            'shipment_id' => $awb->shipment_id,
            'status' => AwbStatusEnum::from($awb->last_status)->info(),
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



if (!function_exists('format_date_time_with_timezone')) {
    function format_date_time_with_timezone($datetime, $timezone)
    {
        return now()->parse($datetime)->setTimezone($timezone)->format('d M Y h:i A');
    }
}

if (!function_exists('format_time_with_timezone')) {
    function format_time_with_timezone($time, $timezone)
    {
        return now()->parse($time)->setTimezone($timezone)->format('h:i A');
    }
}

if (!function_exists('format_date_with_timezone')) {
    function format_date_with_timezone($date, $timezone)
    {
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
            ->where('status', PickupStatusEnum::PENDING->value)
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

        $existingPendingPickup = find_overlapping_pending_pickup(
            clientId: $has_client ? $shipment->client_id : null,
            addressId: $firstAwb->sender_address_id,
            pickupDate: $pickupDate,
            pickupTimeFrom: $pickupTimeFrom,
            pickupTimeTo: $pickupTimeTo
        );
        if ($existingPendingPickup) {
            // $existingPendingPickup->already_exists = true;
            throw new \Exception('A pending pickup already exists for the same shipper address.');
        }


        // $courier_id = courier_assignment_on_route($route_id);
        // if (!$courier_id) {
        //     $courier_id = null;
        // }

        $pickupRequest = new \twa\smsautils\Models\PickupRequest();

        $pickupRequest->operator_id = $operator ? $operator->id : null;
        $pickupRequest->hub_id = $hub_id;
        $pickupRequest->address_id = $firstAwb->sender_address_id;
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

        $pickupRequest->status = 'pending';
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
    function money_object($value, $currency): array
    {
        $numeric = (float) $value;

        return [
            'value' => $numeric,
            'formatted' =>   trim(number_format((float) $numeric, 2, '.', '') . ' ' . $currency),
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

        // LogActivityJob::dispatch(
        //     $table,
        //     $target ?? '',
        //     $target_id ?? 0,
        //     $status_code,
        //     $activity_by_id,
        //     $activity_by_type,
        //     $comment,
        //     $files
        // );
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
            $created_at,
            $source

        );

        \twa\smsautils\Events\OnAWBActivityLog::dispatch($awb_activity_log_id);
        // (new TreatWorkflowActivity($awb_activity_log_id))->handle();
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
            ->where('status', \twa\smsautils\Enums\PickupStatusEnum::PENDING->value)
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

        // ABCD

        //A = 1.
        //B = 2.
        // C = 3.
        // D = 4.

        // the value will be (1*4) + (2*3) + (3*2) + (4*1) = 4 + 6 + 6 + 4 = 20

        // Please implement this function to convert the status to number
        // The status is a string like "ABCD"
        // The function should return the number
        // The function should return the number    


        $statuses = get_workflow_statuses()->pluck('value_code', 'value')->toArray();

        if (!isset($statuses[$status])) {
            return null;
        }

        return $statuses[$status];

        // $status_array = str_split($status);

        // $number = 0;
        // foreach ($status_array as $index => $letter) {

        //     $letter_value = ord($letter) - ord('A') + 1;

        //     $number += ($index + 1) * $letter_value;
        // }
        // return $number;
    }
}

if (!function_exists('get_workflow_statuses')) {
    function get_workflow_statuses()
    {

        $attempts = request()->input('nb_attempts', 1);

        $status_codes = [
            AwbStatusEnum::CREATED,
            AwbStatusEnum::PICKED_UP,
            AwbStatusEnum::ORIGIN_RECEIVED,
            AwbStatusEnum::RECEIVED_OPERATION,
            AwbStatusEnum::GATEWAY_RECEIVED,
            AwbStatusEnum::STATION_RECEIVED,
            AwbStatusEnum::HUB_RECEIVED,
            AwbStatusEnum::RETAIL_RECEIVED,
            AwbStatusEnum::DESTINATION_RECEIVED,


            AwbStatusEnum::GATEWAY_NOT_RECEIVED,
            AwbStatusEnum::STATION_NOT_RECEIVED,
            AwbStatusEnum::HUB_NOT_RECEIVED,
            AwbStatusEnum::RETAIL_NOT_RECEIVED,

            AwbStatusEnum::OFFLOADED,


            AwbStatusEnum::RTS_INITIATED,
            AwbStatusEnum::REVOKED,

            AwbStatusEnum::ADDRESS_CHANGED,
            AwbStatusEnum::ADDRESS_VALIDATED,
            AwbStatusEnum::UPDATED_DIMENSIONS,
            AwbStatusEnum::UPDATED_WEIGHT,
            AwbStatusEnum::CHANGE_ROUTE,
            AwbStatusEnum::HOLD_FOR_PICKUP,
            AwbStatusEnum::HOLD,
            AwbStatusEnum::HOLD_CUSTOMS,
            AwbStatusEnum::RELEASE_CUSTOMS,



            AwbStatusEnum::OUT_FOR_DELIVERY,


        ];

        foreach (range(1, $attempts) as $attempt) {
            $status_codes[] = AwbStatusEnum::tryFrom('SHAT-' . $attempt);
        }


        $status_codes[] = AwbStatusEnum::DELIVERED;
        $status_codes[] = AwbStatusEnum::CANCELLED;
        $status_codes[] = AwbStatusEnum::CIR;

        // case  RTS_INITIATED = 'SHRT';
        // case FINAL_RTS = 'SHFR';
        // case RTS_INBOUND = 'SHRTIN';
        // case RTS_SHELF_IN = 'SHRTSI';
        // case RTS_SHELF_OUT = 'SHRTSO';
        // case RTS_DELIVERED = 'SHRTSD';
        // case CIR = 'SHCIR';
        // case REVOKED = 'SHRE';

        $statuses = collect($status_codes)

            ->map(function ($case, $index) {

                return array_merge(['value' => $case->value, "value_code" => 100 + $index], $case->info());
            });

        return $statuses;
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

    $list_of_exception_trips = AwbStatusEnum::exception_trips();
    // $list_of_exception_trips = [];
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
    // if (!function_exists('get_attributes_for_country')) {
    //     function get_attributes_for_country(string $attributeFor, string | null $country = null) : array
    //     {
    //         $attributes = AttributeSchema::whereNull('deleted_at')
    //             ->where('attribute_for', strtoupper($attributeFor))
    //             ->when($country, function ($query) use ($country) {
    //                 $query->where(function ($subQuery) use ($country) {
    //                     $subQuery->where('countries', 'like', '%"' . $country . '"%')
    //                         ->orWhere('countries', 'like', "%'" . $country . "'%")
    //                         ->orWhere('countries', '[]')
    //                         ->orWhereNull('countries');
    //                 });
    //             })
    //             ->get()
    //             ->map(fn($attribute) => $attribute->formatAttribute())
    //             ->toArray();
    //         return $attributes;
    //     }
    // }
    if (!function_exists('get_attributes_for_country')) {
        function get_attributes_for_country(string $attributeFor, string | null $country = null) : array
        {
            $for = strtoupper($attributeFor);
            $countryKey = ($country !== null && $country !== '')
                ? strtoupper($country)
                : '_any';

            $ttl = max(60, (int) config('cache.attribute_schema_ttl', 3600));

            return Cache::remember(
                'smsautils:attribute_schema:' . $for . ':' . $countryKey,
                now()->addSeconds($ttl),
                static function () use ($attributeFor, $country): array {
                    return AttributeSchema::query()
                        ->select(['id', 'label', 'attribute_key', 'field_type', 'is_required'])
                        ->whereNull('deleted_at')
                        ->where('attribute_for', strtoupper($attributeFor))
                        ->when($country, function ($query) use ($country) {
                            $encoded = json_encode((string) $country, JSON_UNESCAPED_UNICODE);
                            $query->where(function ($subQuery) use ($encoded) {
                                $subQuery->whereRaw(
                                    '(countries IS NOT NULL AND JSON_VALID(countries) AND JSON_CONTAINS(CAST(countries AS JSON), CAST(? AS JSON)))',
                                    [$encoded]
                                )
                                    ->orWhereNull('countries')
                                    ->orWhere('countries', '[]');
                            });
                        })
                        ->get()
                        ->map(fn ($attribute) => $attribute->formatAttribute())
                        ->toArray();
                }
            );
        }
    }
}
