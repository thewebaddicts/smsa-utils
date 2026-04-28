<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExceptionTriggerReason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label',
        'code',
        'description',
        'sort_order',
    ];

    public function exceptionCategory()
    {
        return $this->belongsTo(ExceptionCategory::class, 'exception_category_id', 'id')->whereNull('deleted_at');
    }
}
