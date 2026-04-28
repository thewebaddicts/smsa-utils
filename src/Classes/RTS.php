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
        return [];
    }
    
    public function handle(array $variables, string|null $payload): bool
    {
        $access_token = AccessToken::where('tokenable_type', 'workflow')
            ->where('tokenable_id', 2)
            ->where('expires_at', '>', now())
            ->whereNull('deleted_at')
            ->first();

        if (!$access_token) {
            $access_token = create_access_token(2, 'workflow');
        }

        $url = env('AWB_APP_URL') . "/api/v1/return-module/submit/external/{$variables['awb']}/0";


        $response = Http::withHeaders([
            'Access-Token' => $access_token->token,
        ])->post($url);

        if ($response->failed()) {
            return false;
        }

        return true;
    }
}
