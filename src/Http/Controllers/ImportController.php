<?php

namespace twa\smsautils\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use twa\apiutils\Traits\APITrait;

class ImportController
{

    use APITrait;

    public function getImportConfig($identifier)
    {
        $config = config('import-config.' . $identifier);

        return $this->responseData($config);
    }


    public function import($identifier)
    {

        $form_data = clean_request([]);

        $validations = $this->getValidationRules($identifier);

        $validator = Validator::make($form_data, [
            'data' => 'required|array',
            ...$validations
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }




        $config = config('import-config.' . $identifier);

        $function = $config['target'];

        $function = str_replace('$data', '$form_data["data"]', $function) . ";";


        $callback =  eval($function);

        return $callback;
    }

    public function getValidationRules($identifier)
    {

        $fields = collect(config('import-config.' . $identifier . '.columns'))->where('required', true)->pluck('column')->toArray();

        $validations = [];
        foreach ($fields as $field) {
            $validations['data.*.' . $field] = 'required';
        };

        return $validations;
    }
}
