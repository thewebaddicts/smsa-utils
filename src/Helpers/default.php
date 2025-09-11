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
        $data = [
            'target' => $target,
            'target_id' => $target_id,
            'status_code' => $status_code,
            'activity_by_id' => $activity_by_id,
            'activity_by_type' => $activity_by_type,
            'comment' => $comment,
            'files' => $files ? json_encode($files) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table($table)->insert($data);

        return $data;
    }
}


if (!function_exists('query_options_response')) {
    function query_options_response($table, $columnValue, $columnLabel)
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