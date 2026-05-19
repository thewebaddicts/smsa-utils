<?php

namespace twa\smsautils\Http\Controllers;



use twa\smsautils\Models\DocumentSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use twa\apiutils\Traits\APITrait;
use twa\smsautils\Http\Controllers\Controller;
use twa\smsautils\Models\Shipment;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    use APITrait;


    public function saveDocuments(Request $request)
    {

        $form_data = clean_request([]);
        $validator = Validator::make($form_data, [
            'barcode' => 'required',
            'document_for' => 'required|in:HAWB,PAWB,CRN,MAWB,HST',
            'document_key' => 'required',

            'document_values' => 'nullable|array',
            'document_values.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        $barcode = $form_data['barcode'];
        $documentFor = $form_data['document_for'];
        $documentKey = $form_data['document_key'];
        $documentValue = $form_data['document_value'] ?? null;
        $documentValues = $form_data['document_values'] ?? null;


        switch ($documentFor) {
            case 'HAWB':


                $table = 'awbs';
                $field = 'documents';
                $barcodeField = 'awb';

                break;
            case 'PAWB':


                $table = 'shipments';
                $field = 'documents';
                $barcodeField = 'parent_awb';

                break;
            case 'CRN':

                $table = 'shipment_crns';
                $field = 'documents';
                $barcodeField = 'awb';

                break;
            case 'MAWB':

                $table = 'mawbs_trips';
                $field = 'documents';
                $barcodeField = 'awb';

                break;
            case 'HST':

                $table = 'hsts_trips';
                $field = 'documents';
                $barcodeField = 'awb';

                break;
            default:
                return $this->response(notification()->error('Invalid document_for', 'Invalid document_for'));
        }


        $target = DB::table($table)
            ->where($barcodeField, $barcode)
            ->whereNull('deleted_at')
            ->first();

        if (!$target) {
            return $this->response(notification()->error('Target not found', 'Target not found'));
        }


        $documents = json_decode($target->documents, true) ?? [];
        $documents[$documentKey] = $documentValues ?? [];

        $affected = DB::table($table)
            ->where($barcodeField, $barcode)
            ->whereNull('deleted_at')
            ->update([
                $field => $documents
            ]);


        if ($affected > 0) {
            return $this->response(notification()->success('Document saved successfully', 'Document saved successfully'));
        } else {
            return $this->response(notification()->error('Failed to save document', 'Failed to save document'));
        }
    }
}
