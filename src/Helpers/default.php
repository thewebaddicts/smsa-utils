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