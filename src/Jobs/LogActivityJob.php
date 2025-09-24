<?php

namespace twa\smsautils\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $table;
    protected  $target;
    protected  $target_id;
    protected  $status_code;
    protected  $activity_by_id;
    protected  $activity_by_type;
    protected  $comment;
    protected  $files;

    public function __construct(
        $table,
        $target,
        $target_id,
        $status_code,
        $activity_by_id = null,
        $activity_by_type = null,
        $comment = null,
        $files = []
    ) {
        $this->table = $table;
        $this->target = $target;
        $this->target_id = $target_id;
        $this->status_code = $status_code;
        $this->activity_by_id = $activity_by_id;
        $this->activity_by_type = $activity_by_type;
        $this->comment = $comment;
        $this->files = $files;
    }

    public function handle(): void
    {

        // Directly insert into the table, no helper call
        $data =   DB::table($this->table)->insert([
            'target' => $this->target,
            'target_id' => $this->target_id,
            'status_code' => $this->status_code,
            'activity_by_id' => $this->activity_by_id,
            'activity_by_type' => $this->activity_by_type,
            'comment' => $this->comment,
            'files' => $this->files ? json_encode($this->files) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // dd($data);
    }
}
