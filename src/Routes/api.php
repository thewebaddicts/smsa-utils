<?php

use Illuminate\Support\Facades\Route;

use twa\smsautils\Http\Controllers\AttributesController;

Route::prefix('api/v1')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class])->group(function () {
    Route::post('/files', action: [twa\smsautils\Http\Controllers\FileController::class, 'upload']);

    Route::post('/activity-log', action: [twa\smsautils\Http\Controllers\ActivityLogController::class, 'getActivityLogs']);
    Route::get('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'getImportConfig']);
    Route::post('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'import']);


    Route::get('document-schemas/{document_for}/documents/options', [twa\smsautils\Http\Controllers\DocumentSchemaController::class, 'uploadOptions'])->where('document_for', 'crn|mawb|hst|hawb')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::resource('document-schemas', \twa\smsautils\Http\Controllers\DocumentSchemaController::class)->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('attribute-for/options', [AttributesController::class, 'attributesForOptions'])->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('attribute-types/options', [AttributesController::class, 'attributeTypes'])->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('attributes/{attribute_for}/options', [AttributesController::class, 'options'])->where('attribute_for', 'ADDRESS|MAWB_MANIFEST')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::resource('attributes', AttributesController::class)->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
});
