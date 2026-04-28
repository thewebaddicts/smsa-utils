<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExceptionCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label',
        'code',
        'description',
        'redirection_url',
        'color',
        'sort_order',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function exceptionCases(): HasMany
    {
        return $this->hasMany(ExceptionCase::class, 'exception_category_id', 'id')
            ->whereNull('deleted_at');
    }
}
