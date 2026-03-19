<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use twa\smsautils\Models\AccessToken;

class RTS extends HandlerParent
{
    public function label(): string
    {
        return 'RTS';
    }

    public function payload(): array
    {

        return [
            [
                'column' => 'awb',
                'label' => 'AWB',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter AWB',
            ],
        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {


        $access_token = AccessToken::where('tokenable_type', 'workflow')
            ->where('tokenable_id', 2)
            ->where('expires_at', '>', now())
            ->whereNull('deleted_at')
            ->first();
        if (!$access_token) {

            $access_token = new AccessToken;
            $access_token->tokenable_type = 'workflow';
            $access_token->tokenable_id = 2;
            $access_token->token = Str::random(100);
            $access_token->expires_at = now()->addDays(30);
            $access_token->save();
        }

        $url = env('AWB_APP_URL')."/api/v1/return-module/submit/external/{$payload['awb']}/0";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $access_token->token,
        ])->post($url);

        if ($response->failed()) {
            return false;
        }

        return true;
    }
}
