<?php

namespace twa\smsautils\Http\Controllers;

use twa\smsautils\Enums\AddressTypeEnum;
use twa\smsautils\Enums\OperationsEnum;
use twa\smsautils\Enums\PaymentMethodEnum;
use twa\smsautils\Enums\PaymentTypeEnum;

use twa\smsautils\Models\AddedService;
use twa\smsautils\Models\City;
use twa\smsautils\Models\Country;
use twa\smsautils\Models\Currency;
use twa\smsautils\Models\Product;
use twa\smsautils\Models\ProductGroup;
use twa\smsautils\Models\Province;
use twa\smsautils\Models\WorkflowEventStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use twa\apiutils\Traits\APITrait;
use twa\smsautils\Enums\AwbStatusEnum;
use twa\smsautils\Models\Awb;

class EventController
{
    use APITrait;


    public function countries()
    {
        $countries = Country::query()
            ->whereNull('deleted_at')
            ->select(['code', 'name'])
            ->orderBy('name')
            ->get()
            ->map(function (Country $country) {
                return [
                    'value' => $country->code,
                    'label' => $country->name,
                ];
            })
            ->values();

        return $this->responseData($countries);
    }



    public function provinces()
    {
        $provinces = Province::query()
            ->whereNull('deleted_at')
            ->select(['code', 'name'])
            ->orderBy('name')
            ->get()
            ->map(function ($province) {
                return [
                    'value' => $province->code,
                    'label' => $province->name,
                ];
            });
        return $this->responseData($provinces);
    }

    public function cities()
    {
        $cities = City::query()
            ->whereNull('deleted_at')
            ->select(['code', 'name'])
            // ->orderBy('name')
            ->paginate(100)
            ->through(function ($city) {
                return [
                    'value' => $city->code,
                    'label' => $city->name,

                ];
            });

        return $this->responseData($cities);
    }


    public function get()
    {
        $config = config('event-config');
        $handlers = [];

        foreach ($config as $eventId => $handlerClass) {

            $label = null;
            if (is_array($handlerClass)) {
                $label = $handlerClass['label'] ?? null;
                $handlerClass = $handlerClass['class'] ?? null;
            }

            if (!$handlerClass || !class_exists($handlerClass)) {
                Log::warning("Event handler class does not exist: {$handlerClass}");
                continue;
            }

            try {
                $handler = app($handlerClass);


                $payload = method_exists($handler, 'payload') ? $handler->payload() : [];


                if (!$label && method_exists($handler, 'label')) {
                    $label = $handler->label();
                }

                $handlers[] = [
                    'identifier' => $eventId,
                    'label' => $label ?? $eventId,
                    'payload' => $payload,

                ];
            } catch (\Exception $e) {
                Log::error("Error loading event handler: {$handlerClass}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->responseData($handlers);
    }


    public function create(Request $request, $workflow_id)
    {
        $form_data = clean_request([]);
        $validator = Validator::make($form_data, [
            'status' => ['required', 'string'],
            'event' => 'required|string',
            'payload' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }



        $max_order = WorkflowEventStatus::where('workflow_id', $workflow_id)
            ->where('workflow_event', $form_data['event'])
            ->max('orders');
        if ($max_order) {
            $order = $max_order + 0.1;
        } else {
            $order = convert_status_to_number($form_data['status']) + 0.1;
        }

        $event = new WorkflowEventStatus();
        $event->workflow_event = $form_data['event'];
        $event->status = $form_data['status'];
        $event->payload = $form_data['payload'];
        $event->workflow_id = $workflow_id;
        $event->orders = $order;

        // Set configured_at when payload is filled
        if (!empty($form_data['payload'])) {
            $event->configured_at = now();
        }

        $event->save();

        return $this->responseData([
            'event_id' => $event->id,
        ], notification()->success('Success', 'Event created successfully'));
    }

    public function list($workflow_id)
    {
        $events = WorkflowEventStatus::query()
            ->where('workflow_id', $workflow_id)
            ->whereNull('deleted_at')
            ->orderBy('orders', 'asc')
            ->get()
            ->map(function ($event) {
                $label = get_event_handler_label($event->workflow_event);

                return [
                    'id' => $event->id,
                    'workflow_event' => [
                        'id' => $event->workflow_event ?? null,
                        'label' => $label ?? ''
                    ],
                    'payload' => $event->payload,
                    'workflow_id' => $event->workflow_id,
                    'status' => $event->status,
                    'configured_at' => format_date_time($event->configured_at),
                    'created_at' => format_date_time($event->created_at),
                    'updated_at' => format_date_time($event->updated_at),
                ];
            });
        return $this->responseData($events);
    }



    public function update(Request $request, $workflow_id, $event_id)
    {

        $form_data = clean_request([]);
        $validator = Validator::make($form_data, [
            'payload' => 'array',
            'conditions' => 'nullable|array',

        ]);
        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }
        $event = WorkflowEventStatus::where('id', $event_id)
            ->where('workflow_id', $workflow_id)
            ->whereNull('deleted_at')
            ->first();
        if (!$event) {
            return $this->response(notification()->error('Event not found', 'Event not found'));
        }
        $event->payload = $form_data['payload'];
        $event->conditions = $form_data['conditions'];

        // Set configured_at when payload is filled
        if (!empty($form_data['payload'])) {
            $event->configured_at = now();
        }

        $event->updated_at = now();
        $event->save();
        return $this->response(notification()->success('Success', 'Event updated successfully'));
    }

    public function sortEvents(Request $request, $workflow_id)
    {
        $form_data = clean_request([]);
        $validator = Validator::make($form_data, [
            'events' => 'required|array',
            'events.*' => 'required|numeric',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }

        // convert status into number
        $prefix_number = convert_status_to_number($form_data['status']);

        foreach ($form_data["events"] as $index => $event_id) {
            $event = WorkflowEventStatus::where('workflow_id', $workflow_id)->where('id', $event_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$event) {
                return $this->response(notification()->error('Event not found', 'Event not found'));
            }

            $event->orders = $prefix_number + ($index + 1);
            $event->save();
        }


        return $this->response(notification()->success('Success', 'Events sorted successfully'));
    }


    public function deleteEvent(Request $request, $workflow_id, $event_id)
    {
        $event = WorkflowEventStatus::where('id', $event_id)
            ->where('workflow_id', $workflow_id)
            ->whereNull('deleted_at')
            ->first();
        if (!$event) {
            return $this->response(notification()->error('Event not found', 'Event not found'));
        }
        $event->deleted_at = now();
        $event->save();
        return $this->response(notification()->success('Success', 'Event deleted successfully'));
    }


    public function getVariableTemplate()
    {

        $variables = [

            [
                'label' => 'AWB',
                'type' => 'text',
                'name' => 'awb',
                'operations' => OperationsEnum::forTypeText(),

            ],

            [
                'label' => 'Parent AWB',
                'type' => 'text',
                'name' => 'parent_awb',
                'operations' => OperationsEnum::forTypeText(),
            ],

            [
                'label' => 'Shipment Value',
                'type' => 'select',
                'name' => 'shipment_value',
                'operations' => OperationsEnum::forTypeSelect(),
                'options' => [
                    [
                        'label' => 'Low Value',
                        'value' => 'LOW',
                    ],
                    [
                        'label' => 'High Value',
                        'value' => 'HIGH',
                    ],
                    [
                        'label' => 'Any',
                        'value' => 'ANY',
                    ]
                ],

            ],



            [
                'label' => 'Number of Delivery Attempts',
                'type' => 'number',
                'name' => 'nb_delivery_attempts',
                'operations' => OperationsEnum::forTypeNumber(),
            ],

            [
                'label' => 'Dutiable',
                'type' => 'boolean',
                'name' => 'dutiable',
                'operations' => OperationsEnum::forTypeBoolean(),
            ],

            [
                'label' => 'Product Group',
                'type' => 'select',
                'name' => 'product_group',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => ProductGroup::query()->whereNull('deleted_at')->get()->map(function ($product_group) {
                    return [
                        'label' => $product_group->label,
                        'value' => $product_group->reference,
                    ];
                }),
            ],


            [
                'label' => 'Service Code',
                'type' => 'select',
                'name' => 'service_code',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => Product::query()->whereNull('deleted_at')->get()->map(function ($product) {
                    return [
                        'label' => $product->label,
                        'value' => $product->reference,
                    ];
                }),
            ],


            [
                'label' => 'Added Value Services',
                'type' => 'multi_select',
                'name' => 'added_value_services',
                'operations' => OperationsEnum::forTypeMultiSelect(),
                'options_type' => 'static',
                'options' => AddedService::query()->whereNull('deleted_at')->get()->map(function ($service) {
                    return [
                        'label' => $service->label,
                        'value' => $service->code,
                    ];
                }),
            ],




            [
                'label' => 'Declared Amount',
                'type' => 'number',
                'name' => 'declared.amount',
                'operations' => OperationsEnum::forTypeNumber(),
            ],
            [
                'label' => 'Declared Currency',
                'type' => 'select',
                'name' => 'declared.currency',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => Currency::query()->whereNull('deleted_at')->get()->map(function ($currency) {
                    return [
                        'label' => $currency->label,
                        'value' => $currency->iso,
                    ];
                }),
            ],





            [
                'label' => 'COD Amount',
                'type' => 'number',
                'name' => 'cod.amount',
                'operations' => OperationsEnum::forTypeNumber(),
            ],
            [
                'label' => 'COD Currency',
                'type' => 'select',
                'name' => 'cod.currency',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => Currency::query()->whereNull('deleted_at')->get()->map(function ($currency) {
                    return [
                        'label' => $currency->label,
                        'value' => $currency->iso,
                    ];
                }),
            ],




            [
                'label' => 'Shipping Cost Amount',
                'type' => 'number',
                'name' => 'shipping_cost.amount',
                'operations' => OperationsEnum::forTypeNumber(),
            ],
            [
                'label' => 'Shipping Cost Currency',
                'type' => 'select',
                'name' => 'shipping_cost.currency',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => Currency::query()->whereNull('deleted_at')->get()->map(function ($currency) {
                    return [
                        'label' => $currency->label,
                        'value' => $currency->iso,
                    ];
                }),
            ],
            [
                'label' => 'Shipping Cost Payment Type',
                'type' => 'select',
                'name' => 'shipping_cost.payment_type',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(PaymentTypeEnum::cases())->map(function ($payment_type) {
                    return [
                        'label' => $payment_type->name,
                        'value' => $payment_type->value,
                    ];
                })->values(),
            ],
            [
                'label' => 'Shipping Cost Payment Method',
                'type' => 'select',
                'name' => 'shipping_cost.payment_method',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(PaymentMethodEnum::cases())->map(function ($payment_method) {
                    return [
                        'label' => $payment_method->name,
                        'value' => $payment_method->value,
                    ];
                })->values(),
            ],

            [
                'label' => 'Customs Amount',
                'type' => 'number',
                'name' => 'customs.amount',
                'operations' => OperationsEnum::forTypeNumber(),
            ],
            [
                'label' => 'Customs Currency',
                'type' => 'select',
                'name' => 'customs.currency',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => Currency::query()->whereNull('deleted_at')->get()->map(function ($currency) {
                    return [
                        'label' => $currency->label,
                        'value' => $currency->iso,
                    ];
                }),
            ],
            [
                'label' => 'Customs Payment Type',
                'type' => 'select',
                'name' => 'customs.payment_type',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(PaymentTypeEnum::cases())->map(function ($payment_type) {
                    return [
                        'label' => $payment_type->name,
                        'value' => $payment_type->value,
                    ];
                })->values(),
            ],
            [
                'label' => 'Customs Payment Method',
                'type' => 'select',
                'name' => 'customs.payment_method',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(PaymentMethodEnum::cases())->map(function ($payment_method) {
                    return [
                        'label' => $payment_method->name,
                        'value' => $payment_method->value,
                    ];
                })->values(),
            ],






            [
                'label' => 'VAT Amount',
                'type' => 'number',
                'name' => 'vat.amount',
                'operations' => OperationsEnum::forTypeNumber(),
            ],
            [
                'label' => 'VAT Currency',
                'type' => 'select',
                'name' => 'vat.currency',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => Currency::query()->whereNull('deleted_at')->get()->map(function ($currency) {
                    return [
                        'label' => $currency->label,
                        'value' => $currency->iso,
                    ];
                }),
            ],
            [
                'label' => 'VAT Payment Type',
                'type' => 'select',
                'name' => 'vat.payment_type',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(PaymentTypeEnum::cases())->map(function ($payment_type) {
                    return [
                        'label' => $payment_type->name,
                        'value' => $payment_type->value,
                    ];
                })->values(),
            ],
            [
                'label' => 'VAT Payment Method',
                'type' => 'select',
                'name' => 'vat.payment_method',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(PaymentMethodEnum::cases())->map(function ($payment_method) {
                    return [
                        'label' => $payment_method->name,
                        'value' => $payment_method->value,
                    ];
                })->values(),
            ],


            [
                'label' => 'Shipper Name',
                'type' => 'text',
                'name' => 'shipper.name',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Shipper Email',
                'type' => 'text',
                'name' => 'shipper.email',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Shipper Phone',
                'type' => 'text',
                'name' => 'shipper.phone',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Name',
                'type' => 'text',
                'name' => 'consignee.name',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Email',
                'type' => 'text',
                'name' => 'consignee.email',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Phone',
                'type' => 'text',
                'name' => 'consignee.phone',
                'operations' => OperationsEnum::forTypeText(),
            ],



            [
                'label' => 'Shipper Address Label',
                'type' => 'text',
                'name' => 'shipper_address.label',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Shipper Address Attention',
                'type' => 'text',
                'name' => 'shipper_address.attention',
                'operations' => OperationsEnum::forTypeText(),
            ],



            [
                'label' => 'Shipper Address Company',
                'type' => 'text',
                'name' => 'shipper_address.company',
                'operations' => OperationsEnum::forTypeText(),
            ],


            [
                'label' => 'Shipper Address Address1',
                'type' => 'text',
                'name' => 'shipper_address.address1',
                'operations' => OperationsEnum::forTypeText(),
            ],




            [
                'label' => 'Shipper Address Address2',
                'type' => 'text',
                'name' => 'shipper_address.address2',
                'operations' => OperationsEnum::forTypeText(),
            ],



            [
                'label' => 'Shipper Address Address',
                'type' => 'text',
                'name' => 'shipper_address.address',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Shipper Address Phone',
                'type' => 'text',
                'name' => 'shipper_address.phone',
                'operations' => OperationsEnum::forTypeText(),
            ],




            [
                'label' => 'Shipper Address Email',
                'type' => 'text',
                'name' => 'shipper_address.email',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Shipper Address Address Type',
                'type' => 'select',
                'name' => 'shipper_address.address_type',

                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(AddressTypeEnum::cases())->map(function ($address_type) {
                    return [
                        'label' => $address_type->name,
                        'value' => $address_type->value,
                    ];
                })->values(),
            ],

            [
                'label' => 'Shipper Address Truth Level',
                'type' => 'number',
                'name' => 'shipper_address.truth_level',
                'operations' => OperationsEnum::forTypeNumber(),
            ],


            [
                'label' => 'Shipper Address Area Code',
                'type' => 'text',
                'name' => 'shipper_address.area_code',
                'operations' => OperationsEnum::forTypeText(),
            ],




            // 'shipper_address.country_code', //select

            [
                'label' => 'Shipper Address Country Code',
                'type' => 'select',
                'name' => 'shipper_address.country_code',
                'operations' => OperationsEnum::forTypeSelect(),

                'options_type' => 'api',
                'options' => '/workflows/countries',
            ],




            [
                'label' => 'Shipper Address Province Code',
                'type' => 'select',
                'name' => 'shipper_address.province_code',
                'operations' => OperationsEnum::forTypeSelect(),

                'options_type' => 'api',
                'options' => '/workflows/provinces',
            ],



            [
                'label' => 'Shipper Address City Code',
                'type' => 'select',
                'name' => 'shipper_address.city_code',
                'operations' => OperationsEnum::forTypeSelect(),

                'options_type' => 'api',
                'options' => '/workflows/cities',

            ],

            [
                'label' => 'Consignee Address Label',
                'type' => 'text',
                'name' => 'consignee_address.label',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Address Attention',
                'type' => 'text',
                'name' => 'consignee_address.attention',
                'operations' => OperationsEnum::forTypeText(),
            ],



            'consignee_address.company', //text



            [
                'label' => 'Consignee Address Company',
                'type' => 'text',
                'name' => 'consignee_address.company',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Address Address1',
                'type' => 'text',
                'name' => 'consignee_address.address1',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Address Address2',
                'type' => 'text',
                'name' => 'consignee_address.address2',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Address Address',
                'type' => 'text',
                'name' => 'consignee_address.address',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Address Phone',
                'type' => 'text',
                'name' => 'consignee_address.phone',
                'operations' => OperationsEnum::forTypeText(),
            ],




            [
                'label' => 'Consignee Address Email',
                'type' => 'text',
                'name' => 'consignee_address.email',
                'operations' => OperationsEnum::forTypeText(),
            ],
            [
                'label' => 'Consignee Address Address Type',
                'type' => 'select',
                'name' => 'consignee_address.address_type',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'static',
                'options' => collect(AddressTypeEnum::cases())->map(function ($address_type) {
                    return [
                        'label' => $address_type->name,
                        'value' => $address_type->value,
                    ];
                })->values(),
            ],





            [
                'label' => 'Consignee Address Truth Level',
                'type' => 'number',
                'name' => 'consignee_address.truth_level',
                'operations' => OperationsEnum::forTypeNumber(),
            ],




            [
                'label' => 'Consignee Address Area Code',
                'type' => 'text',
                'name' => 'consignee_address.area_code',
                'operations' => OperationsEnum::forTypeText(),
            ],





            [
                'label' => 'Consignee Address Country Code',
                'type' => 'select',
                'name' => 'consignee_address.country_code',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'api',
                'options' => '/workflows/countries',
            ],




            [
                'label' => 'Consignee Address Province Code',
                'type' => 'select',
                'name' => 'consignee_address.province_code',
                'operations' => OperationsEnum::forTypeSelect(),
                'options_type' => 'api',
                'options' => '/workflows/provinces',
            ],


            [
                'label' => 'Concignee City',
                'type' => 'select',
                'name' => 'consignee_address.city_code',
                'operations' => OperationsEnum::forTypeSelect(),

                'options_type' => 'api',
                'options' => '/workflows/cities',

            ],


        ];
        return $this->responseData(['variables' => $variables]);
    }
    public function getVariables($awb, $api = true)
    {
        $awb = Awb::query()
            ->with(['shipment', 'sender', 'receiver'])
            ->where('awb', $awb)
            ->whereNull('deleted_at')
            ->first();
        if (!$awb) {
            return $this->response(notification()->error('AWB not found', 'AWB not found'));
        }
        $shipment = $awb->shipment;
        if (!$shipment) {
            return $this->response(notification()->error('Shipment not found', 'Shipment not found'));
        }

        $sender = $awb->sender;
        if (!$sender) {
            return $this->response(notification()->error('Sender not found', 'Sender not found'));
        }
        $receiver = $awb->receiver;
        if (!$receiver) {
            return $this->response(notification()->error('Receiver not found', 'Receiver not found'));
        }

        $variables = [
            'shipment_id' => $shipment->id,
            'awb' => $awb->awb,
            'parent_awb' => $awb->master_awb,
            'shipment_value' => $awb->shipment_value,
            'nb_delivery_attempts' => $awb->nb_attempts,
            'dutiable' => $shipment->dutiable,
            'product_group' => $shipment->product_group,
            'service' => $shipment->product_code,
            'added_value_services' => $shipment->added_services,

            'declared' => [
                'amount' => $awb->declared_amount,
                'currency' => $awb->declared_amount_currency,
            ],

            'cod' => [
                'amount' => $awb->cod_amount,
                'currency' => $awb->cod_currency,
            ],

            'shipping_cost' => [
                'amount' => $awb->freight_amount,
                'currency' => $awb->freight_amount_currency,
                'payment_type' => $shipment->freight_payment_type,
                'payment_method' => $shipment->freight_payment_method,
            ],

            'customs' => [
                'amount' => $awb->customs_amount,
                'currency' => $awb->customs_amount_currency,
                'payment_type' => $shipment->customs_payment_type,
                'payment_method' => $shipment->customs_payment_method,
            ],

            'vat' => [
                'amount' => $awb->vat_amount,
                'currency' => $awb->vat_amount_currency,
                'payment_type' => $shipment->vat_payment_type,
                'payment_method' => $shipment->vat_payment_method,
            ],

            'shipper' => [
                'id' => $shipment->client->id,
                'name' => $shipment->client->name,
                'email' => $shipment->client->email,
                'phone' => $shipment->client->phone,
                'country' => $shipment->client->account_country,
            ],
            'consignee' => [
                'name' => $shipment->customer->name,
                'email' => $shipment->customer->email,
                'phone' => $shipment->customer->phone,
            ],

            'shipper_address' => [
                'label' => $sender->label,
                'attention' => $sender->attention,
                'company' => $sender->company,
                'address1' => $sender->address1,
                'address2' => $sender->address2,
                'address' => $sender->address,
                'phone' => $sender->phone,
                'email' => $sender->email,
                'address_type' => $sender->address_type,
                'truth_level' => $sender->truth_level,
                'area_code' => $sender->area_code,
                'country_code' => $sender->country,
                'province_code' => $sender->province,
                'city_code' => $sender->city
            ],
            'consignee_address' => [
                'label' => $receiver->label,
                'attention' => $receiver->attention,
                'company' => $receiver->company,
                'address1' => $receiver->address1,
                'address2' => $receiver->address2,
                'address' => $receiver->address,
                'phone' => $receiver->phone,
                'email' => $receiver->email,
                'address_type' => $receiver->address_type,
                'truth_level' => $receiver->truth_level,
                'area_code' => $receiver->area_code,
                'country_code' => $receiver->country,
                'province_code' => $receiver->province,
                'city_code' => $receiver->city
            ]

        ];


        if (!$api) {
            return $variables;
        }
        return $this->responseData($variables);
    }
}
