<?php
namespace twa\apiutils\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $table;
    protected string $target;
    protected int $target_id;
    protected string $status_code;
    protected ?int $activity_by_id;
    protected ?string $activity_by_type;
    protected ?string $comment;
    protected array $files;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $table,
        string $target,
        int $target_id,
        string $status_code,
        ?int $activity_by_id = null,
        ?string $activity_by_type = null,
        ?string $comment = null,
        array $files = []
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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        log_activity(
            $this->table,
            target: $this->target,
            target_id: $this->target_id,
            status_code: $this->status_code,
            activity_by_id: $this->activity_by_id,
            activity_by_type: $this->activity_by_type,
            comment: $this->comment,
            files: $this->files,
        );
    }
}