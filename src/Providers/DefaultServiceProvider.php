<?php

namespace twa\smsautils\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DefaultServiceProvider extends ServiceProvider{


    public function boot(){
       
        $this->publishes([
            __DIR__ . '/../Configs/smsa-utils.php' => config_path('smsa-utils.php'),
        ], 'smsa-utils-config');

    
   

    }

    public function register(){

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        include_once(__DIR__.'/../Helpers/default.php');      

    }

}