<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

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

        

    

    }
}
