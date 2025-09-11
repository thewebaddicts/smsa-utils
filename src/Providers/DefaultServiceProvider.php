<?php

namespace twa\smsautils\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DefaultServiceProvider extends ServiceProvider{


    public function boot(){
       
        $this->publishes([
            __DIR__ . '/../Configs/smsa-utils.php' => config_path('smsa-utils.php'),
        ], 'smsa-utils-config');

      
            Route::prefix('api')       // optional: give it /api/ prefix
                ->middleware('api')    // same middleware as Laravel’s api.php
                ->group(__DIR__ . '/../routes/api.php');
   

    }

    public function register(){
        include_once(__DIR__.'/../Helpers/default.php');      

    }

}