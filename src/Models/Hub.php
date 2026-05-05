<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Hub extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'label',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'product_ids' => 'array',
            'added_service_ids' => 'array',
        ];
    }

    public function Products(): Collection
    {
        $productIds = array_values(array_filter($this->product_ids ?? []));
        if (empty($productIds)) {
            return collect();
        }

        return DB::table('products')
            ->whereNull('deleted_at')
            ->whereIn('id', $productIds)
            ->get();
    }

    public function AddedServices(): Collection
    {
        $addedServiceIds = array_values(array_filter($this->added_service_ids ?? []));
        if (empty($addedServiceIds)) {
            return collect();
        }

        return DB::table('added_services')
            ->whereNull('deleted_at')
            ->whereIn('id', $addedServiceIds)
            ->get();
    }
    public function ShipmentTypes(): Collection
    {
        $shipmentTypeIds = array_values(array_filter($this->shipment_type_ids ?? []));
        if (empty($shipmentTypeIds)) {
            return collect();
        }

        return DB::table('shipment_types')
            ->whereNull('deleted_at')
            ->whereIn('id', $shipmentTypeIds)
            ->get();
    }
} 