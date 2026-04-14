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
        'countries' => 'array',
    ];

    public function setCountriesAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['countries'] = null;
            return;
        }

        if (is_array($value)) {
            $this->attributes['countries'] = json_encode(array_values($value));
            return;
        }

        $this->attributes['countries'] = $value;
    }
    public function formatAttribute()
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'attribute_key' => $this->attribute_key,
            'field_type' => $this->type,
            'is_required' => $this->is_required,
        ];
    }
}
