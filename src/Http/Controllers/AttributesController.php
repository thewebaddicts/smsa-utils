<?php

namespace twa\smsautils\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use twa\apiutils\Traits\APITrait;
use twa\smsautils\Models\AttributeSchema;
use twa\smsautils\Enums\AttributeForEnum;
use twa\smsautils\Enums\AttributeTypeEnum;

class AttributesController extends Controller
{
    use APITrait;

    public function store(Request $request)
    {
        $data = clean_request([]);

        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        $attribute = new AttributeSchema();
        $attribute->attribute_for = $data['attribute_for'];
        $attribute->label = $data['label'];
        $attribute->attribute_key = str()->slug($data['attribute_key'] ?? $data['label'], '_');
        $attribute->type = $data['type'];
        $attribute->is_required = $data['is_required'] ?? false;
        $attribute->countries = $data['countries'] ?? null;
        $attribute->values = $data['values'] ?? null;
        $attribute->save();

        return $this->response(notification()->success('Attribute created successfully', 'Attribute created successfully'));
    }

    public function update(Request $request, $attributeId)
    {
        $data = clean_request([]);

        $attribute = AttributeSchema::find($attributeId);
        if (!$attribute || $attribute->deleted_at) {
            return $this->response(notification()->error('Attribute not found', 'Attribute not found'));
        }

        $validator = Validator::make($data, $this->rules($attribute->id, false));

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        if (isset($data['attribute_for'])) {
            $attribute->attribute_for = $data['attribute_for'];
        }
        if (isset($data['label'])) {
            $attribute->label = $data['label'];
        }
        if (isset($data['attribute_key']) || isset($data['label'])) {
            $attribute->attribute_key = str()->slug($data['attribute_key'] ?? $attribute->label, '_');
        }
        if (isset($data['type'])) {
            $attribute->type = $data['type'];
        }
        if (isset($data['is_required'])) {
            $attribute->is_required = $data['is_required'];
        }
        if (array_key_exists('countries', $data)) {
            $attribute->countries = $data['countries'];
        }
        if (array_key_exists('values', $data)) {
            $attribute->values = $data['values'];
        }
        $attribute->save();

        return $this->response(notification()->success('Attribute updated successfully', 'Attribute updated successfully'));
    }

    public function destroy($attributeId)
    {
        $attribute = AttributeSchema::find($attributeId);
        if (!$attribute || $attribute->deleted_at) {
            return $this->response(notification()->error('Attribute not found', 'Attribute not found'));
        }

        $attribute->deleted_at = now();
        $attribute->save();

        return $this->response(notification()->success('Attribute deleted successfully', 'Attribute deleted successfully'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $attributes = AttributeSchema::whereNull('deleted_at')
            ->when($search, function ($query) use ($search) {
                $query->where('label', 'like', '%' . $search . '%')
                    ->orWhere('attribute_key', 'like', '%' . $search . '%');
            })
            ->get()
            ->map(fn($attribute) => $attribute->format());

        return $this->responseData($attributes);
    }

    public function show($attributeId)
    {
        $attribute = AttributeSchema::find($attributeId);
        if (!$attribute || $attribute->deleted_at) {
            return $this->response(notification()->error('Attribute not found', 'Attribute not found'));
        }

        return $this->responseData($attribute);
    }

    public function details($attributeId)
    {

        $attribute = AttributeSchema::find($attributeId);
        if (!$attribute || $attribute->deleted_at) {
            return $this->response(notification()->error('Attribute not found', 'Attribute not found'));
        }

        return $this->responseData($attribute->format());
    }
    public function fields($attributeFor)
    {
        $country = request()->input('country');
        //helper function
        $attributes = get_attributes_for_country($attributeFor, $country);

        return $this->responseData($attributes);
    }

    protected function rules($attributeId = null, $isStore = true)
    {
        $required = $isStore ? 'required|' : '';

        return [
            'attribute_for' => $required . 'string|in:' . implode(',', array_column(AttributeForEnum::cases(), 'value')),
            'label' => $required . 'string|max:255',
            'attribute_key' => [
                $isStore ? 'nullable' : 'sometimes',
                'string',
                'max:255',
                unique_rule('attributes', 'attribute_key', $attributeId),
            ],
            'type' => $required . 'string|in:' . implode(',', array_column(AttributeTypeEnum::cases(), 'value')),
            'is_required' => 'boolean',
            'countries' => 'nullable|array',
            'countries.*' => 'nullable|string',
            'values' => 'required_if:type,dropdown|array',
            'values.*' => 'nullable|array',
            'values.*.label' => 'nullable|string',
            'values.*.value' => 'nullable|string',
        ];
    }


    public function attributesForOptions()
    {
        $attributesFor = collect(AttributeForEnum::cases())->map(fn($attribute) => AttributeForEnum::info($attribute));
        return $this->responseData($attributesFor);
    }
    public function attributeTypesOptions()
    {
        $attributeTypes = collect(AttributeTypeEnum::cases())->map(fn($attribute) => AttributeTypeEnum::info($attribute));
        return $this->responseData($attributeTypes);
    }
}
