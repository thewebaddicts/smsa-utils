<?php

namespace twa\smsautils\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use twa\smsautils\Models\Transaction;

class TransactionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Transaction $transaction) {}
}
