<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middlewares' => [twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]], function () {
    Route::post('/files /upload', action: [twa\smsautils\Http\Middleware\Http\Controllers\FileController::class, 'upload']);
   
});