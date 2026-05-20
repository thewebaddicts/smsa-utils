<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosReceipt extends Model
{
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $guarded = [];
}
