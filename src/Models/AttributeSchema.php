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
        'values' => 'array',
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
    public function format()
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'attribute_key' => $this->attribute_key,
            'type' => $this->type,
            'is_required' => $this->is_required,
            'countries' => $this->countries,
            'attribute_for' => $this->attribute_for,
            'created_at' => format_date_time($this->created_at),
            'values' => $this->values,
        ];
    }
    public function formatAttribute(?array $storedValues = null): array
    {
        $row = [
            'id' => $this->id,
            'label' => $this->label,
            'attribute_key' => $this->attribute_key,
            'type' => $this->type,
            'is_required' => $this->is_required,
            'options' => $this->values,
        ];

        if ($storedValues === null) {
            return $row;
        }

        $key = $this->attribute_key;
        $value = ($key !== null && array_key_exists($key, $storedValues)) ? $storedValues[$key] : null;

        return array_merge($row, [
            'value' => $value,
            'filled' => ! blank($value),
        ]);
    }
}
