<?php

use Illuminate\Support\Facades\Validator;

function get_validation_rules($identifier)
{

    $fields = collect(config('import-config.' . $identifier . '.columns'))->where('required', true)->pluck('column')->toArray();

    $validations = [];
    foreach ($fields as $field) {
        $validations[$field] = 'required';
    };

    return $validations;
}

function validate_import_row($identifier, $row, $index, &$report)
{
    
    $row = json_decode(json_encode($row), true);


    $validations = get_validation_rules($identifier);

    $validator = Validator::make($row, [
        ...$validations
    ]);

    if ($validator->fails()) {

        $errors = [];
        foreach (collect($validator->errors()) as $key => $err) {
            if ($err[0] ?? null) {
                $errors[] = $err[0];
            }
        }

        error_import_row($index, $report, implode(', ', $errors));

        return false;
    }

    return true;
}


function error_import_row($index, &$report, $message)
{

    $report[] = [
        'status' => 'error',
        'index' => $index,
        'message' => $message,
    ];
}
function success_import_row($index, &$report)
{

    $report[] = [
        'status' => 'success',
        'index' => $index,
        'message' => "Uploaded successfully",
    ];
}
