<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use twa\smsautils\Models\TransactionInventory;
class Transaction extends Model
{
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $guarded = [];

    public function inventories(): HasMany
    {
        return $this->hasMany(TransactionInventory::class, 'transaction_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class, 'cashier_id');
    }

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country', 'code')
            ->whereNull('countries.deleted_at');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country', 'code')
            ->whereNull('countries.deleted_at');
    }

    public function getOriginProvinceNameAttribute(): ?string
    {
        return resolve_province_name_by_code($this->origin_province, $this->origin_country);
    }

    public function getDestinationProvinceNameAttribute(): ?string
    {
        return resolve_province_name_by_code($this->destination_province, $this->destination_country);
    }
 
    public function awbRow()
    {
        return $this->hasOne(Awb::class, 'awb', 'awb');
    }
    
    public function masterAwbRow()
    {
        return $this->hasOne(Awb::class, 'master_awb', 'awb');
    }
    
  
}
