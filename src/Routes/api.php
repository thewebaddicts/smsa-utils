<?php

use Illuminate\Support\Facades\Route;



Route::prefix('api/v1')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class])->group(function () {
    Route::post('/files', action: [twa\smsautils\Http\Controllers\FileController::class, 'upload']);

    Route::get('/activity-log', action: [twa\smsautils\Http\Controllers\ActivityLogController::class, 'getActivityLogs']);
    Route::get('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'getImportConfig']);
    Route::post('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'import']);
});
