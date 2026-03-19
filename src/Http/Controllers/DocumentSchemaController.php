<?php

namespace twa\smsautils\Http\Controllers;



use twa\smsautils\Models\DocumentSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use twa\apiutils\Traits\APITrait;
use twa\smsautils\Http\Controllers\Controller;

class DocumentSchemaController extends Controller
{
    use APITrait;

    public function store(Request $request)
    {
        $data = clean_request([]);

        $validator = Validator::make($data, [
            'document_name' => ['required', 'string', 'max:255', unique_rule('document_schemas', 'document_name')],
            'document_for' => 'required|string|in:CRN,MAWB,HST,HAWB',
            'ports' => 'array',
            'ports.*' => 'required|string|min:3|max:3',
            'required_condition' => 'required|in:REQUIRED_ON_CREATION,REQUIRED_ON_COMPLETE,OPTIONAL_ANYTIME,ON_INITIAL_RTS,ON_CIR_REQUEST',
            'visible_on_creation' => 'boolean',
            'product_group' => 'array',
            'product_group.*' => 'required|string|in:DOM,EXP',

        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        $documentSchema = new DocumentSchema;
        $documentSchema->document_name = $data['document_name'];
        $documentSchema->document_key = str()->slug($data['document_name'], '_');
        $documentSchema->document_for = $data['document_for'];
        $documentSchema->ports = $data['ports'] ?? null;
        $documentSchema->required_condition = $data['required_condition'];
        $documentSchema->visible_on_creation = $data['visible_on_creation'] ?? false;
        $documentSchema->product_group = $data['product_group'];
        $documentSchema->save();

        return $this->response(notification()->success('Document schema created successfully', 'Document schema created successfully'));
    }

    public function update(Request $request, $documentSchemaId)
    {
        $data = clean_request([]);


        $documentSchema = DocumentSchema::find($documentSchemaId);


        $validator = Validator::make($data, [
            'document_name' => ["string", "max:255", unique_rule('document_schemas', 'document_name', $documentSchema->id)],
            'document_for' => 'string|in:CRN,MAWB,HST,HAWB',
            'ports' => 'array',
            'ports.*' => 'required|string|size:3',
            'required_condition' => 'in:REQUIRED_ON_CREATION,REQUIRED_ON_COMPLETE,OPTIONAL_ANYTIME,ON_INITIAL_RTS,ON_CIR_REQUEST',
            'visible_on_creation' => 'boolean',
            'product_group' => 'array',
            'product_group.*' => 'required|string|in:DOM,EXP',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        if (isset($data['document_name'])) {

            $documentSchema->document_name = $data['document_name'];
            $documentSchema->document_key = str()->slug($data['document_name'], '_');
        }
        if (isset($data['document_for'])) {
            $documentSchema->document_for = $data['document_for'];
        }
        if (isset($data['ports'])) {
            $documentSchema->ports = $data['ports'];
        }
        if (isset($data['required_condition'])) {
            $documentSchema->required_condition = $data['required_condition'];
        }
        if (isset($data['visible_on_creation'])) {
            $documentSchema->visible_on_creation = $data['visible_on_creation'];
        }
        if (isset($data['product_group'])) {
            $documentSchema->product_group = $data['product_group'];
        }
        $documentSchema->save();

        return $this->response(notification()->success('Document schema updated successfully', 'Document schema updated successfully'));
    }

    public function destroy(DocumentSchema $documentSchema)
    {
        $documentSchema->delete();

        return $this->response(notification()->success('Document schema deleted successfully', 'Document schema deleted successfully'));
    }

    public function index(Request $request)
    {
        $documentSchemas = DocumentSchema::whereNull('deleted_at')->get()->map(function ($documentSchema) {
            return [
                'id' => $documentSchema->id,
                'document_name' => $documentSchema->document_name,
                'document_for' => $documentSchema->document_for,
                'ports' => $documentSchema->ports,
                'required_condition' => $documentSchema->required_condition,
                'visible_on_creation' => $documentSchema->visible_on_creation,
                'created_at' => format_date_time($documentSchema->created_at),
                'updated_at' => format_date_time($documentSchema->updated_at),
                'document_key' => $documentSchema->document_key,
                'product_group' => $documentSchema->product_group,
            ];
        });

        return $this->responseData($documentSchemas);
    }

    public function show($id)
    {


        $documentSchema = DocumentSchema::find($id);
        if (!$documentSchema) {
            return $this->response(notification()->error('Document schema not found', 'Document schema not found'));
        }

        $documentSchema = [
            'id' => $documentSchema->id,
            'document_name' => $documentSchema->document_name,
            'document_for' => $documentSchema->document_for,
            'ports' => $documentSchema->ports,
            'required_condition' => $documentSchema->required_condition,
            'visible_on_creation' => $documentSchema->visible_on_creation,
            'created_at' => format_date_time($documentSchema->created_at),
            'updated_at' => format_date_time($documentSchema->updated_at),
            'document_key' => $documentSchema->document_key,
            'product_group' => $documentSchema->product_group,

        ];
        return $this->responseData($documentSchema);
    }


    public function uploadOptions($document_for)
    {
        // Convert to uppercase to match database values (CRN, MAWB, HST, HAWB)
        $document_for = strtoupper($document_for);

        $destination_port = request()->input('destination_port');
        $is_creation_form = request()->input('is_creation_form');

        $documentSchemas = $this->getDocumentSchema($document_for, $destination_port, $is_creation_form);

        return $this->responseData($documentSchemas);
    }


    public function getDocumentSchema($document_for, $destination_port, $is_creation_form = null, $required_condition = null, $product_group = null)
    {

        return DocumentSchema::select('document_key', 'document_name', 'required_condition', 'product_group')
            ->where('document_for', strtoupper($document_for))
            ->where(function ($query) use ($destination_port) {
                $query->where(function ($q) use ($destination_port) {

                    $q->where('ports', 'like', '%"' . $destination_port . '"%');
                })

                    ->orWhereNull('ports')
                    ->orWhere('ports', '[]');
            })
            ->when($is_creation_form !== null, function ($query) use ($is_creation_form) {
                $query->where('visible_on_creation', $is_creation_form ? true : false);
            })
            ->when($product_group !== null, function ($query) use ($product_group) {
                $query->whereJsonContains('product_group', $product_group);
            })
            ->when($required_condition !== null, function ($query) use ($required_condition) {
                $query->where('required_condition', $required_condition);
            })
            ->get();
    }
}
