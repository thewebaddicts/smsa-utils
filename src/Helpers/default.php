<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

if (!function_exists('log_activity')) {

    function log_activity(
        string $table,
        string $status_code,
        ?string $target = null,
        ?int $target_id = null,
        ?int $activity_by_id = null,
        ?string $activity_by_type = null,
        ?string $comment = null,
        ?array $files = null
    ) {
        try {

            \twa\smsautils\Jobs\LogActivityJob::dispatch(
                $table,
                $target ?? '',
                $target_id ?? 0,
                $status_code,
                $activity_by_id,
                $activity_by_type,
                $comment,
                $files ?? []
            );
        } catch (\Exception $e) {
// dd($e);
            Log::error('Failed to dispatch LogActivityJob: ' . $e->getMessage());
        }
    }
}


if (!function_exists('query_options_response')) {
    function query_options_response($table, $columnValue, $columnLabel, $params = [])
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
            $baseQuery->where($field, $value);
        }

        // Apply search filter if provided
        if (request()->input('search')) {
            $baseQuery->where($columnLabel, 'like', '%' . request()->input('search') . '%');
        }


        // If we have specific values, prioritize them at the top
        if (count($values) > 0) {
            $baseQuery->orderByRaw("CASE WHEN " . $columnValue . " IN (" . implode(',', array_map('intval', $values)) . ") THEN 0 ELSE 1 END")
                ->orderBy($columnValue, 'asc');
        } else {
            $baseQuery->orderBy($columnValue, 'asc');
        }

        return $baseQuery->paginate(20)->through(function ($item) use ($columnValue, $columnLabel) {
            return [
                'value' => $item->$columnValue,
                'label' => $item->$columnLabel,
            ];
        });
    }
}