<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use twa\smsautils\Models\Address;
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
            'service_type_ids' => 'array',
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
    public function ServiceTypes(): Collection
    {
        $serviceTypeIds = array_values(array_filter($this->service_type_ids ?? []));
        if (empty($serviceTypeIds)) {
            return collect();
        }

        return DB::table('service_types')
            ->whereNull('deleted_at')
            ->whereIn('id', $serviceTypeIds)
            ->get();
    }
    public function Address()
    {
        return $this->belongsTo(\twa\smsautils\Models\Address::class, 'address_id');  
    }
}  