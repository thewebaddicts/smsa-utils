<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionResolveMethod extends Model
{
    protected $table = 'exceptions_resolve_methods';

    protected $fillable = [
        'exception_category_id',
        'label',
        'code',
        'description',
        'sort_order',
    ];

    public function exceptionCategory()
    {
        return $this->belongsTo(\twa\smsautils\Models\ExceptionCategory::class, 'exception_category_id')->whereNull('deleted_at');
    }
}
