<?php

use Illuminate\Support\Facades\Route;



Route::prefix('api/v1')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class])->group(function () {
    Route::post('/files', action: [twa\smsautils\Http\Controllers\FileController::class, 'upload']);

    Route::post('/activity-log', action: [twa\smsautils\Http\Controllers\ActivityLogController::class, 'getActivityLogs']);
    Route::get('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'getImportConfig']);
    Route::post('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'import']);


    Route::get('document-schemas/{document_for}/documents/options', [twa\smsautils\Http\Controllers\DocumentSchemaController::class, 'uploadOptions'])->where('document_for', 'crn|mawb|hst|hawb')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::resource('document-schemas', \twa\smsautils\Http\Controllers\DocumentSchemaController::class)->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
});
