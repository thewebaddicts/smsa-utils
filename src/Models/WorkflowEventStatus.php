<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowEventStatus extends Model
{
    protected $table = 'workflow_event_status';

    protected $fillable = [
        'workflow_event',
        'status',
        'payload',
        'configured_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'conditions' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_event', 'id');
    }
}
