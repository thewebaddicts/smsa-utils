<?php

use Illuminate\Support\Facades\DB;
use twa\smsautils\Events\TransactionCreated;
use twa\smsautils\Models\Quote;
use twa\smsautils\Models\Transaction;

if (! function_exists('create_transaction')) {

    function create_transaction(array $data): ?Transaction
    {
        $payload = enrich_transaction_payload($data);

        $transaction = DB::transaction(function () use ($payload) {

            $transaction = Transaction::create([
                'hub_id' => $payload['hub_id'] ?? null,
                'cashier_id' => $payload['cashier_id'] ?? null,
                'cashier_name' => $payload['cashier_name'] ?? null,
                'pos_session_id' => $payload['pos_session_id'] ?? null,
                'awb' => $payload['awb'] ?? null,

                'origin_country' => $payload['origin_country'] ?? null,
                'destination_country' => $payload['destination_country'] ?? null,

                'origin_province' => $payload['origin_province'] ?? null,
                'destination_province' => $payload['destination_province'] ?? null,

                'origin_city' => $payload['origin_city'] ?? null,
                'destination_city' => $payload['destination_city'] ?? null,

                'weight_in_g' => $payload['weight_in_g'] ?? null,
            ]);

            $balances = [];

            foreach (($payload['inventories'] ?? []) as $inventory) {

                $transactionType = $inventory['transaction_type'] ?? 'cash';

                if (! isset($balances[$transactionType])) {
                    $balances[$transactionType] = get_cashier_balance(
                        $payload['cashier_id'] ?? null,
                        $transactionType
                    );
                }

                $amount = (float) ($inventory['amount'] ?? 0);

                if (($inventory['type'] ?? null) === 'refund') {
                    $balances[$transactionType] -= $amount;
                } else {
                    $balances[$transactionType] += $amount;
                }

                $transaction->inventories()->create([
                    'cashier_id' => $payload['cashier_id'] ?? null,
                    'transaction_type' => $transactionType,
                    'type' => $inventory['type'] ?? null,
                    'amount' => $amount,
                    'vat' => $inventory['vat'] ?? null,
                    'discount_amount' => $inventory['discount_amount'] ?? null,
                    'discount_percentage' => $inventory['discount_percentage'] ?? null,
                    'currency' => $inventory['currency'] ?? null,
                    'current_balance' => $balances[$transactionType],
                ]);
            }

            return $transaction->load('inventories');
        });

        event(new TransactionCreated($transaction));

        return $transaction;
    }
}

if (! function_exists('create_transactions_from_quote')) {

    function create_transactions_from_quote(int $quoteId, array $extra = []): ?Transaction
    {
        $quote = Quote::with('paymentLines')->find($quoteId);

        if (! $quote) {
            return null;
        }

        $inventories = [];

        foreach ($quote->paymentLines as $line) {

            if (! in_array($line->payment_source, ['cash', 'card'])) {
                continue;
            }

            $inventories[] = [
                'transaction_type' => $line->payment_source,
                'type' => 'transaction',
                'amount' => (float) $line->payment_amount,
                'vat' => $quote->vat_amount ?? 0,
                'discount_amount' => $quote->total_discount_amount ?? 0,
                'discount_percentage' => 0,
                'currency' => $line->payment_currency ?? $quote->currency,
            ];
        }

        return create_transaction(array_merge([
            'cashier_id' => $quote->cashier_id,
            'inventories' => $inventories,
        ], $extra));
    }
}

if (! function_exists('get_cashier_balance')) {

    function get_cashier_balance(?int $cashierId, ?string $transactionType): float
    {
        if (! $cashierId || ! $transactionType) {
            return 0;
        }

        return (float) DB::table('transaction_inventories')
            ->where('cashier_id', $cashierId)
            ->where('transaction_type', $transactionType)
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN type = 'refund'
                        THEN -amount
                        ELSE amount
                    END
                ), 0) as balance
            ")
            ->value('balance');
    }
}

if (! function_exists('enrich_transaction_payload')) {

    function enrich_transaction_payload(array $payload): array
    {
        if (! empty($payload['cashier_id'])) {

            if (empty($payload['cashier_name'])) {
                $payload['cashier_name'] = DB::table('operators')
                    ->where('id', $payload['cashier_id'])
                    ->value('name');
            }

            if (empty($payload['pos_session_id'])) {
                $payload['pos_session_id'] = DB::table('pos_sessions')
                    ->where('cashier_id', $payload['cashier_id'])
                    ->whereNull('ended_at')
                    ->latest('id')
                    ->value('id');
            }
        }

        if (! empty($payload['hub_id'])) {

            $hub = DB::table('hubs')
                ->where('id', $payload['hub_id'])
                ->first();

            if ($hub) {
                $payload['origin_country'] = $hub->country_code;
                $payload['origin_province'] = $hub->province_code;
                $payload['origin_city'] = $hub->city_code;
            }
        }

        if (! empty($payload['awb'])) {

            $awb = DB::table('awbs')
                ->where('awb', $payload['awb'])
                ->orWhere('id', $payload['awb'])
                ->first();

            if ($awb) {

                $payload['weight_in_g'] =
                    $awb->actual_weight_g
                    ?: $awb->declared_weight_g;

                $payload['destination_province'] =
                    $awb->destination_code;

                $payload['destination_country'] =
                    $awb->destination_country;

                if (! empty($awb->receiver_address_id)) {

                    $address = DB::table('addresses')
                        ->where('id', $awb->receiver_address_id)
                        ->first();

                    if ($address) {
                        $payload['destination_city'] = $address->city;
                    }
                }
            }
        }

        return $payload;
    }
}

if (! function_exists('resolve_province_name_by_code')) {

    function resolve_province_name_by_code(?string $provinceCode): ?string
    {
        if (! $provinceCode) {
            return null;
        }

        return DB::table('provinces')
            ->where('code', $provinceCode)
            ->value('name');
    }
}
