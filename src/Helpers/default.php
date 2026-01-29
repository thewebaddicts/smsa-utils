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

use Illuminate\Support\Facades\Storage;


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
        return Rule::unique($table, $column)
            ->where(function ($query) use ($ignore_id) {
                $query->whereNull('deleted_at');
                if ($ignore_id) {
                    $query->where('id', '!=', $ignore_id);
                }
            });
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


        $data = DB::table($table)->insert([
            'target' => $target,
            'target_id' => $target_id,
            'status_code' => $status_code,
            'activity_by_id' => $activity_by_id,
            'activity_by_type' => $activity_by_type,
            'comment' => $comment,
            'files' => $files ? json_encode($files) : null,
            'created_at' => $created_at ? $created_at : now(),
            'updated_at' => $created_at ? $created_at : now(),
            'source' => $source ?? null
        ]);

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

        log_activity(
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

        $assignment = DB::table('route_assignments')
            ->where('route_id', $route->id)
            ->whereNull('deleted_at')
            ->orderByDesc('assigned_at')
            ->first();

        if ($assignment) {
            $courier_id = $assignment->courier_id;
        }

        return $courier_id;
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

        // Log::info("Starting OTP send", [
        //     'phone' => $phone,
        //     'otp' => $otp,
        //     'clientId' => $clientId,
        //     'senderId' => $senderId
        // ]);


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