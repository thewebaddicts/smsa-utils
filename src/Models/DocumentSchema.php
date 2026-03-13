<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSchema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_schemas';

    protected $casts = [
        'ports' => 'array',
        'product_group' => 'array',
    ];
}
