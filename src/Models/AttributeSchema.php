<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeSchema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attributes';

    protected $casts = [
        'is_required' => 'boolean',
    ];
}
