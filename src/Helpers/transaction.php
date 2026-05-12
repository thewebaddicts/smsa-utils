<?php

use Illuminate\Support\Facades\DB;
use twa\smsautils\Events\TransactionCreated;
use twa\smsautils\Models\Quote;
use twa\smsautils\Models\Transaction;

if (! function_exists('create_transaction')) {
    /**
     * Create a transaction row plus its inventory lines and fire TransactionCreated.
     * POS-only side effects (pos_session counters, quote settlement, audit) should
     * subscribe to that event in the host application rather than living here.
     *
     * @param  array<string, mixed>  $data
     */
    function create_transaction(array $data): ?Transaction
    {
        $payload = enrich_transaction_payload(
            normalize_transaction_payload($data)
        );

        $transaction = DB::transaction(function () use ($payload): ?Transaction {
            $transaction = Transaction::create(transaction_data($payload));

            $inventories = resolve_transaction_inventories_with_current_balance($payload);

            create_transaction_inventories($transaction, $payload, $inventories);

            return load_transaction_response($transaction->id);
        });

        if ($transaction !== null) {
            event(new TransactionCreated($transaction));
        }

        return $transaction;
    }
}

if (! function_exists('create_transactions_from_quote')) {
    /**
     * Create one transaction from a quote, with one inventory line per payment_source
     * group on the quote's payment lines. Inventory type is always 'transaction'.
     * VAT and discount from the quote are prorated by each group's share of total_amount.
     *
     * Pass anything not derivable from the quote (hub_id, awb, pos_session_id,
     * cashier_name, cashier_id override) via $extra. Keys in $extra win over quote defaults.
     *
     * @param  array<string, mixed>  $extra
     */
    function create_transactions_from_quote(int $quoteId, array $extra = []): ?Transaction
    {
        try {
        DB::beginTransaction();
        $quote = Quote::query()
            ->with(['paymentLines' => function ($query): void {
                $query->whereNull('deleted_at');
            }])
            ->whereNull('deleted_at')
            ->find($quoteId);

        if (! $quote) {
            DB::rollBack();

            return null;
        }

        $paymentLines = $quote->paymentLines
            ->filter(static fn ($line): bool => is_numeric($line->payment_amount))
            ->filter(static fn ($line): bool => in_array(
                strtolower((string) $line->payment_source),
                ['cash', 'card'],
                true
            ));

        if ($paymentLines->isEmpty()) {
            DB::rollBack();

            return null;
        }

        $quoteTotal = (float) ($quote->total_amount ?? 0);
        $quoteVat = (float) ($quote->vat_amount ?? 0);
        $quoteDiscountAmount = (float) ($quote->total_discount_amount ?? 0);
        $quoteCurrency = (string) ($quote->currency ?? '');
        $quoteDiscountPercentage = $quoteTotal > 0
            ? round(($quoteDiscountAmount / $quoteTotal) * 100, 2)
            : null;

        $inventories = $paymentLines
            ->groupBy(static fn ($line): string => strtolower((string) $line->payment_source))
            ->map(function ($lines, string $paymentSource) use (
                $quoteTotal,
                $quoteVat,
                $quoteDiscountAmount,
                $quoteDiscountPercentage,
                $quoteCurrency
            ): array {
                $groupAmount = (float) $lines->sum(
                    static fn ($line): float => (float) $line->payment_amount
                );
                $shareOfTotal = $quoteTotal > 0 ? $groupAmount / $quoteTotal : 0.0;
                $currency = (string) ($lines->first()->payment_currency ?? $quoteCurrency);

                return [
                    'transaction_type' => $paymentSource,
                    'type' => 'transaction',
                    'amount' => round($groupAmount, 2),
                    'vat' => round($quoteVat * $shareOfTotal, 2),
                    'discount_amount' => round($quoteDiscountAmount * $shareOfTotal, 2),
                    'discount_percentage' => $quoteDiscountPercentage,
                    'currency' => $currency,
                ];
            })
            ->values()
            ->all();

        $payload = array_merge(
            ['cashier_id' => $quote->cashier_id],
            $extra,
            ['inventories' => $inventories]
        );

        $transaction = create_transaction($payload);
        DB::commit();
        return $transaction;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

if (! function_exists('transaction_data')) {
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    function transaction_data(array $payload): array
    {
        return [
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
        ];
    }
}

if (! function_exists('create_transaction_inventories')) {
    /**
     * @param  array<int, array<string, mixed>>  $inventories
     */
    function create_transaction_inventories(Transaction $transaction, array $payload, array $inventories): void
    {
        $transaction->inventories()->createMany(
            collect($inventories)
                ->map(fn (array $inventory): array => [
                    'cashier_id' => $payload['cashier_id'] ?? null,
                    'transaction_type' => $inventory['transaction_type'] ?? null,
                    'type' => $inventory['type'] ?? null,
                    'amount' => $inventory['amount'] ?? null,
                    'vat' => $inventory['vat'] ?? null,
                    'discount_amount' => $inventory['discount_amount'] ?? null,
                    'discount_percentage' => $inventory['discount_percentage'] ?? null,
                    'current_balance' => $inventory['current_balance'] ?? null,
                    'currency' => $inventory['currency'] ?? null,
                ])
                ->all()
        );
    }
}

if (! function_exists('sum_inventory_amount_by_type')) {
    /**
     * @param  array<int, array<string, mixed>>  $inventories
     */
    function sum_inventory_amount_by_type(array $inventories, string $type): float
    {
        return collect($inventories)
            ->where('type', $type)
            ->sum(
                fn (array $inventory): float => is_numeric($inventory['amount'] ?? null)
                    ? (float) $inventory['amount']
                    : 0.0
            );
    }
}

if (! function_exists('load_transaction_response')) {
    function load_transaction_response(int $transactionId): ?Transaction
    {
        $transaction = Transaction::query()
            ->with('inventories')
            ->where('id', $transactionId)
            ->whereNull('deleted_at')
            ->first();

        if (! $transaction) {
            return null;
        }

        $transaction->setAttribute(
            'destination_province_name',
            resolve_province_name_by_code(
                $transaction->destination_province,
                $transaction->destination_country
            )
        );

        return $transaction;
    }
}

if (! function_exists('resolve_transaction_inventories_with_current_balance')) {
    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    function resolve_transaction_inventories_with_current_balance(array $payload): array
    {
        $cashierId = is_numeric($payload['cashier_id'] ?? null) ? (int) $payload['cashier_id'] : null;
        $balancesByTransactionType = [];

        return collect($payload['inventories'] ?? [])
            ->map(function (array $inventory) use ($cashierId, &$balancesByTransactionType): array {
                $transactionType = isset($inventory['transaction_type'])
                    ? (string) $inventory['transaction_type']
                    : '';

                if (! array_key_exists($transactionType, $balancesByTransactionType)) {
                    $balancesByTransactionType[$transactionType] = get_cashier_transaction_type_balance(
                        $cashierId,
                        $transactionType !== '' ? $transactionType : null
                    );
                }

                $amount = is_numeric($inventory['amount'] ?? null)
                    ? (float) $inventory['amount']
                    : 0.0;

                $balancesByTransactionType[$transactionType] += ($inventory['type'] ?? null) === 'refund'
                    ? -$amount
                    : $amount;

                $inventory['current_balance'] = $balancesByTransactionType[$transactionType];

                return $inventory;
            })
            ->values()
            ->all();
    }
}

if (! function_exists('get_cashier_transaction_type_balance')) {
    function get_cashier_transaction_type_balance(?int $cashierId, ?string $transactionType): float
    {
        if (! $cashierId || ! $transactionType) {
            return 0.0;
        }

        return (float) DB::table('transaction_inventories as ti')
            ->whereNull('ti.deleted_at')
            ->where('ti.cashier_id', $cashierId)
            ->where('ti.transaction_type', $transactionType)
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN ti.type = 'refund' THEN -COALESCE(ti.amount, 0) ELSE COALESCE(ti.amount, 0) END), 0) as balance"
            )
            ->value('balance');
    }
}

if (! function_exists('resolve_awb_weight_grams_from_row')) {
    /**
     * Prefer `actual_weight_g` when set; otherwise `declared_weight_g` from an `awbs` row.
     *
     * @param  object|array<string, mixed>  $row
     */
    function resolve_awb_weight_grams_from_row(object|array $row): ?float
    {
        $read = static function (object|array $row, string $key): mixed {
            $value = is_array($row) ? ($row[$key] ?? null) : ($row->{$key} ?? null);
            if (is_string($value)) {
                $trimmed = trim($value);

                return $trimmed === '' ? null : $trimmed;
            }

            return $value;
        };

        $actual = $read($row, 'actual_weight_g');
        if ($actual !== null && $actual !== '' && is_numeric($actual)) {
            return (float) $actual;
        }

        $declared = $read($row, 'declared_weight_g');
        if ($declared !== null && $declared !== '' && is_numeric($declared)) {
            return (float) $declared;
        }

        return null;
    }
}

if (! function_exists('enrich_transaction_payload')) {
    /**
     * Fill origin from hub and destination from AWB receiver address.
     * Weight (grams) is taken only from the matched `awbs` row: `actual_weight_g` if set, else `declared_weight_g`.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    function enrich_transaction_payload(array $payload): array
    {
        unset($payload['weight'], $payload['weight_in_g']);

        if (empty($payload['hub_id']) || ! is_numeric($payload['hub_id'])) {
            return $payload;
        }

        $hub = DB::table('hubs')
            ->where('id', (int) $payload['hub_id'])
            ->whereNull('deleted_at')
            ->first(['country_code', 'province_code', 'city_code']);

        if ($hub) {
            $payload['origin_country'] = $hub->country_code;
            $payload['origin_province'] = $hub->province_code;
            $payload['origin_city'] = $hub->city_code;
        }

        if (empty($payload['awb'])) {
            return $payload;
        }

        $awb = DB::table('awbs')
            ->whereNull('deleted_at')
            ->where(function ($query) use ($payload): void {
                $query->where('awb', $payload['awb']);
                if (is_numeric($payload['awb'])) {
                    $query->orWhere('id', (int) $payload['awb']);
                }
            })
            ->first(['receiver_address_id', 'destination_code', 'destination_country', 'actual_weight_g', 'declared_weight_g']);

        $payload['weight_in_g'] = $awb !== null ? resolve_awb_weight_grams_from_row($awb) : null;

        if (! $awb || empty($awb->receiver_address_id)) {
            if (! empty($awb?->destination_code)) {
                $payload['destination_province'] = $awb->destination_code;
                $payload['destination_province_name'] = resolve_province_name_by_code(
                    $awb->destination_code,
                    $awb->destination_country ?? null
                );
            }

            return $payload;
        }

        $receiverAddress = DB::table('addresses')
            ->where('id', $awb->receiver_address_id)
            ->whereNull('deleted_at')
            ->first(['country', 'province', 'city']);

        if ($receiverAddress) {
            $payload['destination_country'] = $awb->destination_country ?? $receiverAddress->country;
            $payload['destination_province'] = $awb->destination_code ?? $receiverAddress->province;
            $payload['destination_city'] = $receiverAddress->city;
            $payload['destination_province_name'] = resolve_province_name_by_code(
                $payload['destination_province'] ?? null,
                $payload['destination_country'] ?? null
            );
        }

        return $payload;
    }
}

if (! function_exists('resolve_province_name_by_code')) {
    function resolve_province_name_by_code(?string $provinceCode, ?string $countryCode = null): ?string
    {
        if (! $provinceCode) {
            return null;
        }

        static $cache = [];
        $cacheKey = ($countryCode ?? '').'|'.$provinceCode;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $name = DB::table('provinces')
            ->whereNull('deleted_at')
            ->when($countryCode, function ($query) use ($countryCode): void {
                $query->where('country', $countryCode);
            })
            ->where('code', $provinceCode)
            ->value('name');

        return $cache[$cacheKey] = $name !== null ? (string) $name : null;
    }
}

if (! function_exists('resolve_country_name_by_code')) {
    function resolve_country_name_by_code(?string $countryCode): ?string
    {
        if (! $countryCode) {
            return null;
        }

        static $cache = [];
        if (array_key_exists($countryCode, $cache)) {
            return $cache[$countryCode];
        }

        $name = DB::table('countries')
            ->whereNull('deleted_at')
            ->where('code', $countryCode)
            ->value('name');

        return $cache[$countryCode] = $name !== null ? (string) $name : null;
    }
}

if (! function_exists('normalize_transaction_payload')) {
    /**
     * Accepts both body-style payload and query-style payload.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    function normalize_transaction_payload(array $data): array
    {
        $payload = $data;

        if (isset($payload['inventories']) && is_string($payload['inventories'])) {
            $decoded = json_decode($payload['inventories'], true);
            if (is_array($decoded)) {
                $payload['inventories'] = $decoded;
            }
        }

        if (! isset($payload['inventories']) && isset($payload['inventory']) && is_array($payload['inventory'])) {
            $payload['inventories'] = [$payload['inventory']];
        }

        if (! isset($payload['inventories']) || ! is_array($payload['inventories'])) {
            $inventoryFromFlatParams = [];
            foreach (['type', 'transaction_type', 'amount', 'vat', 'discount_amount', 'discount_percentage', 'current_balance', 'currency'] as $inventoryKey) {
                if (array_key_exists($inventoryKey, $payload)) {
                    $inventoryFromFlatParams[$inventoryKey] = $payload[$inventoryKey];
                }
            }

            $payload['inventories'] = ! empty($inventoryFromFlatParams) ? [$inventoryFromFlatParams] : [];
        }

        if (isset($payload['transaction_type']) && is_array($payload['inventories'])) {
            $payload['inventories'] = array_map(
                static function ($inventory) use ($payload) {
                    if (! is_array($inventory)) {
                        return $inventory;
                    }

                    if (! array_key_exists('transaction_type', $inventory) || $inventory['transaction_type'] === null || $inventory['transaction_type'] === '') {
                        $inventory['transaction_type'] = $payload['transaction_type'];
                    }

                    return $inventory;
                },
                $payload['inventories']
            );
        }

        return $payload;
    }
}
