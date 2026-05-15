<?php

use Illuminate\Support\Facades\DB;
use twa\smsautils\Events\TransactionCreated;
use twa\smsautils\Models\Quote;
use twa\smsautils\Models\Transaction;
use twa\smsautils\Models\TransactionInventory;

if (! function_exists('create_transaction')) {
    function create_transaction(array $data): ?Transaction
    {
        $payload = enrich_transaction_payload($data);
        $hasChanges = false;

        $transaction = DB::transaction(function () use ($payload, &$hasChanges) {
            [$transaction, $transactionChanged] = persist_transaction($payload);
            $changedInventoryTypes = persist_transaction_inventories($transaction, $payload);
            sync_transaction_totals($payload, $transactionChanged, $changedInventoryTypes);
            $hasChanges = transaction_has_changes($transactionChanged, $changedInventoryTypes);

            return $transaction->load('inventories');
        });

        if ($hasChanges) {
            event(new TransactionCreated($transaction));
        }

        return $transaction;
    }
}

if (! function_exists('persist_transaction')) {
    function persist_transaction(array $payload): array
    {
        $transaction = find_existing_transaction($payload);

        if ($transaction) {
            return [$transaction, false];
        }

        $transaction = Transaction::create(build_transaction_attributes($payload));

        return [$transaction, true];
    }
}

if (! function_exists('find_existing_transaction')) {
    function find_existing_transaction(array $payload): ?Transaction
    {
        $transaction = find_existing_transaction_from_inventories($payload['inventories'] ?? []);

        if ($transaction) {
            return $transaction;
        }

        if (empty($payload['awb'])) {
            return null;
        }

        return Transaction::query()
            ->where('awb', $payload['awb'])
            ->when(! empty($payload['pos_session_id']), function ($query) use ($payload) {
                $query->where('created_by_pos_session_id', $payload['pos_session_id']);
            })
            ->when(empty($payload['pos_session_id']) && ! empty($payload['cashier_id']), function ($query) use ($payload) {
                $query->where('created_by_cashier_id', $payload['cashier_id']);
            })
            ->first();
    }
}

if (! function_exists('find_existing_transaction_from_inventories')) {
    function find_existing_transaction_from_inventories(array $inventories): ?Transaction
    {
        $quotePaymentLineIds = collect($inventories)
            ->pluck('quote_payment_line_id')
            ->filter()
            ->unique()
            ->values();

        if ($quotePaymentLineIds->isEmpty()) {
            return null;
        }

        $transactionId = TransactionInventory::withTrashed()
            ->whereIn('quote_payment_line_id', $quotePaymentLineIds)
            ->value('transaction_id');

        if (! $transactionId) {
            return null;
        }

        return Transaction::query()->find($transactionId);
    }
}

if (! function_exists('build_transaction_attributes')) {
    function build_transaction_attributes(array $payload): array
    {
        return [
            'created_by_hub_id' => $payload['hub_id'] ?? null,
            'created_by_cashier_id' => $payload['cashier_id'] ?? null,
            'created_by_cashier_name' => $payload['cashier_name'] ?? null,
            'created_by_pos_session_id' => $payload['pos_session_id'] ?? null,
            'awb' => $payload['awb'] ?? null,
            'origin_country' => $payload['origin_country'] ?? null,
            'destination_country' => $payload['destination_country'] ?? null,
            'origin_province' => $payload['origin_province'] ?? null,
            'destination_province' => $payload['destination_province'] ?? null,
            'origin_city' => $payload['origin_city'] ?? null,
            'destination_city' => $payload['destination_city'] ?? null,
            'weight_in_g' => $payload['weight_in_g'] ?? null,
        ];
    }
}

if (! function_exists('create_transactions_from_quote')) {
    function create_transactions_from_quote(int $quoteId, array $extra = []): ?Transaction
    {
        $quote = Quote::with('paymentLines')->find($quoteId);

        if (! $quote) {
            return null;
        }

        $inventories = $quote->paymentLines
            ->whereIn('payment_source', ['cash', 'card'])
            ->map(fn ($line) => [
                'quote_payment_line_id' => $line->id,
                'payed_by' => $line->source ?: null,
                'transaction_type' => $line->payment_source,
                'type' => 'transaction',
                'amount' => (float) $line->payment_amount,
                'vat' => $quote->vat_amount ?? 0,
                'discount_amount' => $quote->total_discount_amount ?? 0,
                'discount_percentage' => 0,
                'currency' => $line->payment_currency ?? $quote->currency,
            ])
            ->values()
            ->all();

        return create_transaction(array_merge([
            'cashier_id' => $quote->cashier_id,
            'inventories' => $inventories,
        ], $extra));
    }
}

if (! function_exists('persist_transaction_inventories')) {
    function persist_transaction_inventories(Transaction $transaction, array $payload): array
    {
        $changedInventoryTypes = [];

        foreach ($payload['inventories'] ?? [] as $inventory) {
            $changed = persist_transaction_inventory($transaction, $payload, $inventory);

            if (! $changed) {
                continue;
            }

            $changedInventoryTypes[] = $inventory['transaction_type'] ?? 'cash';
        }

        return array_values(array_unique($changedInventoryTypes));
    }
}

if (! function_exists('persist_transaction_inventory')) {
    function persist_transaction_inventory(Transaction $transaction, array $payload, array $inventory): bool
    {
        $transactionInventory = find_existing_transaction_inventory($inventory) ?? new TransactionInventory();
        $wasTrashed = $transactionInventory->exists && $transactionInventory->trashed();

        if ($wasTrashed) {
            $transactionInventory->restore();
        }

        $transactionInventory->fill(build_transaction_inventory_attributes($transaction, $payload, $inventory));

        if (! $transactionInventory->exists) {
            $transactionInventory->current_balance ??= 0;
        }

        $changed = ! $transactionInventory->exists || $transactionInventory->isDirty() || $wasTrashed;

        if ($changed) {
            $transactionInventory->save();
        }

        return $changed;
    }
}

if (! function_exists('build_transaction_inventory_attributes')) {
    function build_transaction_inventory_attributes(Transaction $transaction, array $payload, array $inventory): array
    {
        return [
            'transaction_id' => $transaction->id,
            'quote_payment_line_id' => $inventory['quote_payment_line_id'] ?? null,
            'hub_id' => $payload['hub_id'] ?? null,
            'cashier_id' => $payload['cashier_id'] ?? null,
            'cashier_name' => $payload['cashier_name'] ?? null,
            'pos_session_id' => $payload['pos_session_id'] ?? null,
            'payed_by' => $inventory['payed_by'] ?? $payload['payed_by'] ?? null,
            'transaction_type' => $inventory['transaction_type'] ?? 'cash',
            'type' => $inventory['type'] ?? null,
            'amount' => (float) ($inventory['amount'] ?? 0),
            'vat' => $inventory['vat'] ?? null,
            'discount_amount' => $inventory['discount_amount'] ?? null,
            'discount_percentage' => $inventory['discount_percentage'] ?? null,
            'currency' => $inventory['currency'] ?? null,
        ];
    }
}

if (! function_exists('find_existing_transaction_inventory')) {
    function find_existing_transaction_inventory(array $inventory): ?TransactionInventory
    {
        if (empty($inventory['quote_payment_line_id'])) {
            return null;
        }

        return TransactionInventory::withTrashed()
            ->where('quote_payment_line_id', $inventory['quote_payment_line_id'])
            ->first();
    }
}

if (! function_exists('sync_cashier_balances')) {
    function sync_cashier_balances(int $cashierId, array $transactionTypes, ?int $posSessionId = null): void
    {
        $transactionTypes = array_values(array_filter(array_unique($transactionTypes)));

        if (empty($transactionTypes)) {
            return;
        }

        $inventories = apply_pos_session_scope(
            DB::table('transaction_inventories')
            ->where('cashier_id', $cashierId)
            ->whereIn('transaction_type', $transactionTypes)
            ->whereNull('deleted_at'),
            $posSessionId
        )
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'transaction_type', 'type', 'amount']);

        $balances = [];

        foreach ($inventories as $inventory) {
            $transactionType = $inventory->transaction_type ?: 'cash';
            $amount = (float) $inventory->amount;
            $balanceChange = calculate_balance_change($inventory->type, $amount);

            $balances[$transactionType] = ($balances[$transactionType] ?? 0) + $balanceChange;

            DB::table('transaction_inventories')
                ->where('id', $inventory->id)
                ->update(['current_balance' => $balances[$transactionType]]);
        }
    }
}

if (! function_exists('apply_pos_session_scope')) {
    function apply_pos_session_scope($query, ?int $posSessionId)
    {
        return $posSessionId === null
            ? $query->whereNull('pos_session_id')
            : $query->where('pos_session_id', $posSessionId);
    }
}

if (! function_exists('calculate_balance_change')) {
    function calculate_balance_change(?string $inventoryType, float $amount): float
    {
        return $inventoryType === 'refund' ? -$amount : $amount;
    }
}

if (! function_exists('sync_pos_session_totals')) {
    function sync_pos_session_totals(int $posSessionId): void
    {
        $posSession = DB::table('pos_sessions')
            ->where('id', $posSessionId)
            ->whereNull('deleted_at')
            ->first();

        if (! $posSession) {
            return;
        }

        $inventories = get_pos_session_inventories($posSessionId);
        $totals = calculate_pos_session_totals($inventories);

        $nbTransactions = DB::table('transactions')
            ->where('created_by_pos_session_id', $posSessionId)
            ->whereNull('deleted_at')
            ->count();

        DB::table('pos_sessions')
            ->where('id', $posSessionId)
            ->update([
                'cash_collected_amount' => $totals['cash_collected_amount'],
                'cash_collected_currency' => $totals['cash_collected_currency'] ?: $posSession->cash_collected_currency,
                'card_collected_amount' => $totals['card_collected_amount'],
                'card_collected_currency' => $totals['card_collected_currency'] ?: $posSession->card_collected_currency,
                'refund_amount' => $totals['refund_amount'],
                'refund_currency' => $totals['refund_currency'] ?: $posSession->refund_currency,
                'nb_transactions' => $nbTransactions,
                'last_activity_at' => $totals['last_activity_at'] ?: $posSession->last_activity_at,
            ]);
    }
}

if (! function_exists('get_pos_session_inventories')) {
    function get_pos_session_inventories(int $posSessionId)
    {
        return DB::table('transaction_inventories')
            ->where('pos_session_id', $posSessionId)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get(['transaction_type', 'type', 'amount', 'currency', 'created_at', 'updated_at']);
    }
}

if (! function_exists('calculate_pos_session_totals')) {
    function calculate_pos_session_totals($inventories): array
    {
        $cashInventories = $inventories->filter(fn ($inventory) => is_collected_payment($inventory, 'cash'));
        $cardInventories = $inventories->filter(fn ($inventory) => is_collected_payment($inventory, 'card'));
        $refundInventories = $inventories->filter(fn ($inventory) => is_refund($inventory));
        $cashTotals = build_inventory_totals($cashInventories);
        $cardTotals = build_inventory_totals($cardInventories);
        $refundTotals = build_inventory_totals($refundInventories);

        return [
            'cash_collected_amount' => $cashTotals['amount'],
            'cash_collected_currency' => $cashTotals['currency'],
            'card_collected_amount' => $cardTotals['amount'],
            'card_collected_currency' => $cardTotals['currency'],
            'refund_amount' => $refundTotals['amount'],
            'refund_currency' => $refundTotals['currency'],
            'last_activity_at' => get_last_activity_at($inventories),
        ];
    }
}

if (! function_exists('build_inventory_totals')) {
    function build_inventory_totals($inventories): array
    {
        return [
            'amount' => (float) $inventories->sum('amount'),
            'currency' => optional($inventories->last())->currency,
        ];
    }
}

if (! function_exists('is_collected_payment')) {
    function is_collected_payment($inventory, string $transactionType): bool
    {
        return $inventory->transaction_type === $transactionType && ! is_refund($inventory);
    }
}

if (! function_exists('is_refund')) {
    function is_refund($inventory): bool
    {
        return $inventory->type === 'refund';
    }
}

if (! function_exists('get_last_activity_at')) {
    function get_last_activity_at($inventories): ?string
    {
        return $inventories
            ->map(fn ($inventory) => $inventory->updated_at ?: $inventory->created_at)
            ->filter()
            ->max();
    }
}

if (! function_exists('sync_transaction_totals')) {
    function sync_transaction_totals(array $payload, bool $transactionChanged, array $changedInventoryTypes): void
    {
        if (! empty($changedInventoryTypes) && ! empty($payload['cashier_id'])) {
            sync_cashier_balances(
                (int) $payload['cashier_id'],
                $changedInventoryTypes,
                isset($payload['pos_session_id']) ? (int) $payload['pos_session_id'] : null
            );
        }

        if (! empty($payload['pos_session_id']) && transaction_has_changes($transactionChanged, $changedInventoryTypes)) {
            sync_pos_session_totals((int) $payload['pos_session_id']);
        }
    }
}

if (! function_exists('transaction_has_changes')) {
    function transaction_has_changes(bool $transactionChanged, array $changedInventoryTypes): bool
    {
        return $transactionChanged || ! empty($changedInventoryTypes);
    }
}

if (! function_exists('enrich_transaction_payload')) {
    function enrich_transaction_payload(array $payload): array
    {
        if (! empty($payload['cashier_id'])) {
            $payload = enrich_payload_from_cashier($payload);
        }

        if (! empty($payload['hub_id'])) {
            $payload = enrich_payload_from_hub($payload);
        }

        if (! empty($payload['awb'])) {
            $payload = enrich_payload_from_awb($payload);
        }

        return $payload;
    }
}

if (! function_exists('enrich_payload_from_cashier')) {
    function enrich_payload_from_cashier(array $payload): array
    {
        $cashierId = $payload['cashier_id'];

        if (empty($payload['cashier_name'])) {
            $payload['cashier_name'] = DB::table('operators')
                ->where('id', $cashierId)
                ->value('name');
        }

        if (empty($payload['pos_session_id'])) {
            $payload['pos_session_id'] = DB::table('pos_sessions')
                ->where('operator_id', $cashierId)
                ->whereNull('ended_at')
                ->latest('id')
                ->value('id');
        }

        return $payload;
    }
}

if (! function_exists('enrich_payload_from_hub')) {
    function enrich_payload_from_hub(array $payload): array
    {
        $hub = DB::table('hubs')
            ->where('id', $payload['hub_id'])
            ->first(['country_code', 'province_code', 'city_code']);

        if (! $hub) {
            return $payload;
        }

        $payload['origin_country'] = $hub->country_code;
        $payload['origin_province'] = $hub->province_code;
        $payload['origin_city'] = $hub->city_code;

        return $payload;
    }
}

if (! function_exists('enrich_payload_from_awb')) {
    function enrich_payload_from_awb(array $payload): array
    {
        $awb = DB::table('awbs')
            ->leftJoin('addresses', 'addresses.id', '=', 'awbs.receiver_address_id')
            ->where(function ($query) use ($payload) {
                $query->where('awbs.awb', $payload['awb'])
                    ->orWhere('awbs.id', $payload['awb']);
            })
            ->first([
                'awbs.actual_weight_g',
                'awbs.declared_weight_g',
                'awbs.destination_code',
                'awbs.destination_country',
                'addresses.city as destination_city',
            ]);

        if (! $awb) {
            return $payload;
        }

        $payload['weight_in_g'] = $awb->actual_weight_g ?: $awb->declared_weight_g;
        $payload['destination_province'] = $awb->destination_code;
        $payload['destination_country'] = $awb->destination_country;
        $payload['destination_city'] = $awb->destination_city;

        return $payload;
    }
}
