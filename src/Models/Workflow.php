<?php

namespace twa\smsautils\Models;


use twa\smsautils\Models\Product;
use Illuminate\Database\Eloquent\Model;
use twa\smsautils\Models\Shipper;

class Workflow extends Model
{
    protected $fillable = [
        'country',
        'service_id',
        'delivery_attempts',
        'shipper_id',
    ];

    public function service()
    {
        return $this->belongsTo(Product::class, 'service_id', 'id')->whereNull('deleted_at');
    }

    public function shipper()
    {
        return $this->belongsTo(Shipper::class, 'shipper_id', 'id')->whereNull('deleted_at');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'code')->whereNull('deleted_at');
    }
    public function events()
    {
        return $this->hasMany(WorkflowEventStatus::class, 'workflow_id', 'id')->whereNull('deleted_at')->orderBy('orders', 'asc');
    }
    public function format()
    {
        $country = $this->country;
        $service = $this->service;
        $shipper = $this->shipper;
        return [
            'id' => $this->id,
            'country' => [
                'code' => $country,
                'name' => $country?->name ?? 'All Countries',
            ],
            'service' => [
                'id' => $this->service_id,
                'name' => $service?->label ?? null,
            ],
            'delivery_attempts' => $this->delivery_attempts,
            'shipper' => [
                'id' => $this->shipper_id,
                'name' => $shipper?->name ?? 'All Shippers',
            ],
            'created_at' => format_date_time($this->created_at),
            'updated_at' => format_date_time($this->updated_at),
            'deleted_at' => format_date_time($this->deleted_at),
        ];
    }
}
