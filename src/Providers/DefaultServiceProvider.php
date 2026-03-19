<?php

namespace twa\smsautils\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;


class DefaultServiceProvider extends EventServiceProvider
{


    protected $listen = [
        \twa\smsautils\Events\OnAWBActivityLog::class => [
            \twa\smsautils\Listeners\HandleWorkflowActivityLog::class,
        ]
    ];

    public function boot()
    {

        Relation::enforceMorphMap([
            'operator' => 'twa\smsautils\Models\Operator',
            'courier' => 'twa\smsautils\Models\Courier',
        ]);
        $this->publishes([
            __DIR__ . '/../Configs/smsa-utils.php' => config_path('smsa-utils.php'),
        ], 'smsa-utils-config');
        $this->publishes([
            __DIR__ . '/../Configs/event-config.php' => config_path('event-config.php'),
        ], 'laravel-assets');
    }

    public function register()
    {

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        include_once(__DIR__ . '/../Helpers/default.php');
        include_once(__DIR__ . '/../Helpers/awbLocationChange.php');
        include_once(__DIR__ . '/../Helpers/courier-notifications.php');
        include_once(__DIR__ . '/../Helpers/importHelper.php');
        include_once(__DIR__ . '/../Helpers/awb-received-status.php');
        include_once(__DIR__ . '/../Helpers/awbLogsActivities.php');
    }
}
