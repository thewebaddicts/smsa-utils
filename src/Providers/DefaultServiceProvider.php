<?php

namespace twa\smsautils\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\Relation;
class DefaultServiceProvider extends ServiceProvider
{


    public function boot()
    {
        
        Relation::enforceMorphMap([
            'operator' => 'twa\smsautils\Models\Operator',
            'courier' => 'twa\smsautils\Models\Courier',
        ]);
        $this->publishes([
            __DIR__ . '/../Configs/smsa-utils.php' => config_path('smsa-utils.php'),
        ], 'smsa-utils-config');
    }

    public function register()
    {

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        include_once(__DIR__ . '/../Helpers/default.php');
        include_once(__DIR__ . '/../Helpers/awbLocationChange.php');
    }
}
