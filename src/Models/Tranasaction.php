<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
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
}
