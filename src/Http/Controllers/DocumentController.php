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
            'document_value' => 'nullable',
            'document_values' => 'nullable|array',
            'document_values.*' => 'integer',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        $barcode = $form_data['barcode'];
        $documentFor = $form_data['document_for'];
        $documentKey = $form_data['document_key'];
        $documentValue = $form_data['document_value'] ?? null;
        $documentValues = $form_data['document_values'] ?? null;

        $awb = \twa\smsautils\Models\Awb::query()
            ->where('awb', $barcode)
            ->whereNull('deleted_at')
            ->first();

        if (!$awb) {
            return $this->response(notification()->error('AWB not found', 'AWB not found'));
        }


        $target = null;
        switch ($documentFor) {
            case 'HAWB':
                $target = $awb;
                break;
            case 'PAWB':
                $target = $awb->shipment ?? Shipment::query()->where('id', $awb->shipment_id)->whereNull('deleted_at')->first();
                if (!$target) {
                    return $this->response(notification()->error('Shipment not found', 'Shipment not found'));
                }
                break;
            case 'CRN':
                $target = DB::table('shipment_crns')
                    ->where('awb', $barcode)
                    ->whereNull('deleted_at')
                    ->first();
                if (!$target) {
                    return $this->response(notification()->error('Shipment Crn not found', 'Shipment Crn not found'));
                }
                break;
            case 'MAWB':
                $target = DB::table('mawbs_trips')
                    ->where('awb', $barcode)
                    ->whereNull('deleted_at')
                    ->first();
                if (!$target) {
                    return $this->response(notification()->error('MAWB not found', 'MAWB not found'));
                }
                break;
            case 'HST':
                $target = DB::table('hsts_trips')
                    ->where('awb', $barcode)
                    ->whereNull('deleted_at')
                    ->first();
                if (!$target) {
                    return $this->response(notification()->error('HST not found', 'HST not found'));
                }
                break;
            default:
                return $this->response(notification()->error('Invalid document_for', 'Invalid document_for'));
        }

        $documents = is_array($target->documents) ? $target->documents : [];

        if (!empty($form_data['documents']) && is_array($form_data['documents'])) {
            $documents = update_documents_batch($documents, $form_data['documents']);
        } else {
            $documents = update_documents_for_key(
                $documents,
                $documentKey,
                $documentValue,
                $documentValues
            );
        }

        $target->documents = $documents;
        $target->save();

        return $this->response(notification()->success('Document saved successfully', 'Document saved successfully'));
    }
}
