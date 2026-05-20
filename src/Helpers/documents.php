<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('document_file_ids')) {
    /**
     * Return file IDs from a document upload value.
     *
     * Single: 31446 → [31446]
     * Multiple: [31446, 31447] → [31446, 31447]
     * String: "31446,31447" → [31446, 31447]
     */
    function document_file_ids(mixed $value): array
    {
        if ($value === null || $value === '' || $value === 'null' || $value === 'NULL') {
            return [];
        }

        // New format: each document key holds multiple file IDs.
        if (is_array($value)) {
            return array_values(array_unique(array_filter(array_map('intval', $value))));
        }

        // Convenience: allow "101,102" from query/form strings.
        if (is_string($value) && str_contains($value, ',')) {
            return array_values(array_unique(array_filter(array_map('intval', explode(',', $value)))));
        }

        // Legacy format: a single file ID per key.
        return [(int) $value];
    }
}

if (!function_exists('update_documents_for_key')) {
    /**
     * Update one document key on awb/shipment documents JSON.
     *
     * Request options (used by shipmentUploadDocuments):
     * - document_values: [101, 102]  → replace the full list for this key
     * - document_value: 103           → append file ID(s) to the existing list
     * - document_value=null or document_values=[] → delete the key
     */
    function update_documents_for_key(
        array $documents,
        string $documentKey,
        mixed $documentValue = null,
        mixed $documentValues = null
    ): array {
        $isEmpty = static function ($value): bool {
            return $value === null
                || $value === ''
                || $value === 'null'
                || $value === 'NULL'
                || (is_array($value) && count(array_filter($value, fn($item) => $item !== null && $item !== '')) === 0);
        };


        if ($documentValues !== null) {
            $nextValues = document_file_ids($documentValues);

            if (empty($nextValues)) {
                unset($documents[$documentKey]);
            } else {
                $documents[$documentKey] = $nextValues;
            }

            return $documents;
        }


        if ($isEmpty($documentValue)) {
            unset($documents[$documentKey]);
            return $documents;
        }

        $currentValues = isset($documents[$documentKey])
            ? document_file_ids($documents[$documentKey])
            : [];

        $incomingValues = document_file_ids($documentValue);

        // Single upload appends to the list; send document_values to replace the full list.
        $nextValues = array_values(array_unique(array_merge($currentValues, $incomingValues)));

        if (empty($nextValues)) {
            unset($documents[$documentKey]);
        } else {
            $documents[$documentKey] = $nextValues;
        }

        return $documents;
    }
}

if (!function_exists('update_documents_batch')) {
    /**
     * Update multiple document keys in one request.
     *
     * Example:
     * [
     *   "commercial_invoice" => [31446, 31447],
     *   "trading_invoice" => [31448],
     * ]
    
     */
    function update_documents_batch(array $documents, array $documentsInput): array
    {
        foreach ($documentsInput as $documentKey => $documentValues) {
            if (!is_string($documentKey) || $documentKey === '') {
                continue;
            }

            if (is_array($documentValues)) {
                $documents = update_documents_for_key($documents, $documentKey, null, $documentValues);
                continue;
            }

            $documents = update_documents_for_key($documents, $documentKey, $documentValues, null);
        }

        return $documents;
    }
}



if (!function_exists('get_documents_info')) {
    /**
     * Build document schema rows with uploaded file IDs and file metadata.
     *
     * Stored documents example:
     * {
     *   "commercial_invoice": [31446, 31447],
     *   "trading_invoice": [31448]
     * }
     */
    function get_documents_info($condition_slug, $document_for, $destination_code, $product_group, $visible = null, $data = [])
    {
        if (!is_array($condition_slug)) {
            $condition_slug = [$condition_slug];
        }

        if (is_string($data) && $data !== '') {
            $data = json_decode($data, true);
        }
        if (!is_array($data)) {
            $data = [];
        }

        $documents = DB::table('document_schemas')
            ->select('document_key',  'document_name', 'required_condition')

            ->when(!is_null($visible), function ($query) use ($visible) {
                $query->where('visible_on_creation', $visible ? 1 : 0);
            })
            ->where('document_for', $document_for)
            ->whereIn('required_condition', $condition_slug)
            ->when($destination_code, function ($query) use ($destination_code) {
                $query->where(function ($q1) use ($destination_code) {

                    $q1->where(function ($q) use ($destination_code) {
                        $q->where('ports', 'LIKE', '%"' . $destination_code . '"%');
                        $q->orWhere('ports', 'LIKE', "%'" . $destination_code . "'%");
                    });
                    $q1->orWhereNull('ports');
                    $q1->orWhere('ports', '[]');
                });
            })
            ->when($product_group, function ($query) use ($product_group) {
                $query->where(function ($q1) use ($product_group) {
                    $q1->where(function ($q) use ($product_group) {
                        $q->where('product_group', 'LIKE', '%"' . $product_group . '"%');
                        $q->orWhere('product_group', 'LIKE', "%'" . $product_group . "'%");
                    });
                    $q1->orWhereNull('product_group');
                    $q1->orWhere('product_group', '[]');
                });
            })
            ->whereNull('deleted_at')
            ->get()->map(function ($doc) use ($data) {

                $documentValue = collect($data[$doc->document_key] ?? [])->unique()->filter()->values()->toArray();
                $filePaths = get_files_info($documentValue);

                return [
                    'document_name' => $doc->document_name,
                    'document_key' => $doc->document_key,
                    'required' => in_array($doc->required_condition, ["REQUIRED_ON_CREATION", "REQUIRED_ON_COMPLETE"]),
                    'sample_file_url' => config('sample-files.' . $doc->document_key) ?? null,
                    'value' => $documentValue,
                    'file_paths' => $filePaths,
                ];
            });


        return $documents;
    }
}
