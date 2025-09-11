<?php

use Illuminate\Support\Facades\Route;



 Route::prefix('api/v1')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class])->group(function () {
// Route::group(['prefix' => 'api/v1', 'middlewares' => [twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]], function () {
    Route::post('/files', action: [twa\smsautils\Http\Controllers\FileController::class, 'upload']);
});