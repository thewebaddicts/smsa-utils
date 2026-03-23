<?php

namespace twa\smsautils\Listeners;

use twa\smsautils\Events\OnAWBActivityLog;
use Illuminate\Support\Facades\DB;
use twa\smsautils\Models\Route;
use Illuminate\Contracts\Queue\ShouldQueue;
use twa\smsautils\Models\AwbActivity;
use twa\smsautils\Models\WorkflowActivityEventStatus;
use twa\smsautils\Models\Workflow;
use twa\smsautils\Http\Controllers\EventController;
use twa\smsautils\Services\WorkflowEventConditionEvaluator;
use Illuminate\Support\Facades\Log;

class HandleWorkflowActivityLog implements ShouldQueue
{
    public function handle(OnAWBActivityLog $event)
    {
        $awb_activity_log_id = $event->awb_activity_id;


        $awb_activity =  AwbActivity::where('id', $awb_activity_log_id)->first();
        $awb_activity->workflow_started_at = now();
        $awb_activity->save();



        $awb_number = $awb_activity->target;
        $variables = (new EventController())->getVariables($awb_number, false);
        $workflow_country = $variables['shipper']['country'];
        $workflow_shipper_id = $variables['shipper']['id'];
        $workflow_service_code = $variables['service'];
        $workflow_delivery_attempts = $variables['nb_delivery_attempts'];
        $workflow_product_group = $variables['product_group'];



        $workflows = Workflow::query()
            ->with(['events'])
            ->where(function ($query) use ($workflow_country) {
                $query->where('country', $workflow_country);
                $query->orWhereNull('country');
            })
            ->where(function ($query) use ($workflow_shipper_id) {
                $query->where('shipper_id', $workflow_shipper_id);
                $query->orWhereNull('shipper_id');
            })
            // ->where(function ($query) use ($workflow_delivery_attempts) {
            //     $query->where('delivery_attempts', $workflow_delivery_attempts);
            //     $query->orWhereNull('delivery_attempts');
            // })
            ->where('service_code', $workflow_service_code) //cause this will not be null it's mandatory
            ->where('product_group', $workflow_product_group) //it's from the service code always filled
            ->get();

        foreach ($workflows as $workflow) {

            $events = $workflow->events;
            foreach ($events as $event) {


                $awb_status_code = process_event_status($awb_activity->status_code, $variables['nb_delivery_attempts'] ?? 1);


                if ($event->status != $awb_status_code) {
                    $event_status = new WorkflowActivityEventStatus();
                    $event_status->awb_activity_id = $awb_activity->id;
                    $event_status->event_identifier = $event->workflow_event;
                    $event_status->status = 'MISSMATCH STATUS';
                    $event_status->payload = $event->payload;
                    $event_status->variables = $variables;
                    $event_status->save();
                    continue 1;
                }

                if (!$this->matchConditions($event->conditions, $variables)) {
                    $event_status = new WorkflowActivityEventStatus();
                    $event_status->awb_activity_id = $awb_activity->id;
                    $event_status->event_identifier = $event->workflow_event;
                    $event_status->status = 'MISSMATCH CONDITIONS';
                    $event_status->payload = $event->payload;
                    $event_status->variables = $variables;
                    $event_status->save();
                    continue 1;
                }


                $class = config('event-config.' . $event->workflow_event);
                if (!$class) {
                    $eventKey = (string) $event->workflow_event;
                    $eventConfig = config('event-config', []);
                    Log::error('Workflow event handler not resolved from config', [
                        'event_identifier_raw' => $event->workflow_event,
                        'event_identifier_trimmed' => trim($eventKey),
                        'event_identifier_hex' => bin2hex($eventKey), // helps detect hidden chars/spaces/newlines
                        'event_identifier_length' => strlen($eventKey),
                        'config_loaded' => is_array($eventConfig),
                        'config_keys_count' => is_array($eventConfig) ? count($eventConfig) : null,
                        'has_exact_key' => is_array($eventConfig) ? array_key_exists($eventKey, $eventConfig) : false,
                        'has_trimmed_key' => is_array($eventConfig) ? array_key_exists(trim($eventKey), $eventConfig) : false,
                    ]);
                }
                $class = new $class();
                $result = $class->handle($variables, json_encode($event->payload, true));


                $event_status = new WorkflowActivityEventStatus();
                $event_status->awb_activity_id = $awb_activity->id;
                $event_status->event_identifier = $event->workflow_event;
                $event_status->status = $result ? 'SUCCESS' : 'FAILED';
                $event_status->payload = $event->payload;
                $event_status->variables = $variables;
                $event_status->save();
            }
        }
        $awb_activity->workflow_ended_at = now();
        $awb_activity->save();
    }

    private function matchConditions(array $conditions, array $variables): bool
    {
        return (new WorkflowEventConditionEvaluator())->evaluate($variables, $conditions);
    }
}
