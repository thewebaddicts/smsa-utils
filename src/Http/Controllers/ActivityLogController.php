<?php

namespace twa\smsautils\Http\Controllers;

use twa\smsautils\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use twa\apiutils\Traits\APITrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ActivityLogController extends Controller
{

    use APITrait;

    public function getActivityLogs()
    {
        $form_data = clean_request([]);
        $validator = Validator::make($form_data, [
            'record_type' => 'required|string',
            'record_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->responseValidation($validator);
        }
        $logs = ActivityLog::where('record_type', $form_data['record_type'])->where('record_id', $form_data['record_id'])->orderBy('created_at', 'desc')->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'mode' => $log->mode,
                'record_id' => $log->record_id,
                'record_type' => $log->record_type,
                'payload' => $log->payload,
                'created_at' => $log->created_at,
                'operator_id' => $log->operator_id,
                'operator_email' => $log->operator_email,
            ];
        })->values();
        return $this->responseData($logs);
    }
}