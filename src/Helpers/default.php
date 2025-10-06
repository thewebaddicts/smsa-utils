<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

if (!function_exists('unique_rule')) {
    function unique_rule($table, $column)
    {
        return  Rule::unique($table, $column)->whereNull('deleted_at');
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
        return $base_url . '/awb/' . $awb . '/pdf';
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
        $files = []
    ) {


        $data =   DB::table($table)->insert([
            'target' => $target,
            'target_id' => $target_id,
            'status_code' => $status_code,
            'activity_by_id' => $activity_by_id,
            'activity_by_type' => $activity_by_type,
            'comment' => $comment,
            'files' => $files ? json_encode($files) : null,
            'created_at' => now(),
            'updated_at' => now(),
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
        $files = []
    ) {

        log_activity(
            'awb_activities',
            $status_code,
            $target,
            $target_id,
            $activity_by_id,
            $activity_by_type,
            $comment,
            $files
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
    function query_options_response($table, $columnValue, $columnLabel, $params = [], $extraFields = [], $separator = " ")
    {

        $values = request()->input('values');

        if (is_numeric($values)) {
            $values = [$values];
        } elseif (is_array($values)) {
            $values = $values;
        } else {
            $values = [];
        }


        // Build base query
        $baseQuery = DB::table($table)->whereNull('deleted_at');


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
                        $baseQuery->where($field, $value['operand'], $value['value']);
                }
            } else {
                $baseQuery->where($field, $value);
            }
        }

        // Apply search filter if provided
        if (request()->input('search')) {
            $baseQuery->where($columnLabel, 'like', '%' . request()->input('search') . '%');

            foreach (collect($extraFields)->flatten()->values()->toArray() as $extraField) {
                $baseQuery->orWhere($extraField, 'like', '%' . request()->input('search') . '%');
            }
        }


        // If we have specific values, prioritize them at the top
        if (count($values) > 0) {
            $baseQuery->orderByRaw("CASE WHEN " . $columnValue . " IN (" . implode(',', array_map('intval', $values)) . ") THEN 0 ELSE 1 END")
                ->orderBy($columnValue, 'asc');
        } else {
            $baseQuery->orderBy($columnValue, 'asc');
        }

        return $baseQuery->paginate(20)->through(function ($item) use ($columnValue, $columnLabel, $extraFields, $separator) {

            $extraFields = collect($extraFields)->map(function ($fieldName) use ($item, $separator) {

                if (!is_array($fieldName)) {
                    return $item->{$fieldName} ?? null;
                }


                return collect($fieldName)->map(function ($itteration) use ($item, $separator) {
                    return $item->{$itteration} ?? null;
                })->filter()->values()->implode($separator);
            })->filter()->values()->toArray();

            return [
                'value' => $item->$columnValue,
                'label' => $item->$columnLabel,
                'extra_descriptions' => $extraFields
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
            ->where('name', $address['city'])
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
        $routes = DB::table('route_by_postal_codes')
            ->whereNull('deleted_at')
            ->where('country', $address['country'])
            ->where('province', $address['province'])
            ->where('city', $address['city'])
            ->get();

        if (count($routes) > 0) {
            return $routes;
        }

        // Level 2: Country and province only
        $routes = DB::table('route_by_postal_codes')
            ->whereNull('deleted_at')
            ->where('country', $address['country'])
            ->where('province', $address['province'])
            ->whereNull('city')
            ->get();

        if (count($routes) > 0) {
            return $routes;
        }

        // Level 3: Country only
        $routes = DB::table('route_by_postal_codes')
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