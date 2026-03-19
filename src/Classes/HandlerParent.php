<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

class HandlerParent
{
    public function label(): string
    {
        return 'Example Handler';
    }

    public function payload(): array
    {
        return [];
    }
    public function handle(array $variables, string|null $payload): bool
    {

        return true;
    }


    private function renderTemplate(string $template, array $context): string
    {
        return str_replace(array_keys($context), array_values($context), $template);
    }


    public function validatePayload($variables, $payload = null)
    {
        if (!$payload) {

            return false;
        }

        $dictionary = render_dictionary_template($variables);

        $payload = $this->renderTemplate($payload, $dictionary);

        try {
            $payload = json_decode($payload, true);
        } catch (\Throwable $th) {
            Log::error('SendEmail: Failed to decode payload JSON.', [
                'error' => $th->getMessage(),
            ]);
            return false;
        }

        return $payload;
    }
}
