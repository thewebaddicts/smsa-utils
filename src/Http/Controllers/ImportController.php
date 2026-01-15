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

        $config = config('import-config.' . $identifier);

        $function = $config['target'];

        $function = str_replace('$data', '$form_data["data"], $identifier', $function) . ";";

        $callback = eval("return $function");

        return $this->responseData($callback);
    }
}
