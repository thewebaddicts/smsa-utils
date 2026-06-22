<?php

use Illuminate\Support\Facades\Route;

use twa\smsautils\Http\Controllers\AttributesController;


Route::prefix('api/v1')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class])->group(function () {

    Route::get('/version/check', [\twa\smsautils\Http\Controllers\ForceUpdateController::class, 'check']);

    Route::middleware(twa\smsautils\Http\Middleware\BasicAuthMiddleware::class)->group(function () {
        Route::post('version/release', [\twa\smsautils\Http\Controllers\ForceUpdateController::class, 'release']);
    });

    // https://smsa-awb.twalab.live/api/v1/save-documents
 
    Route::post('/save-documents', action: [twa\smsautils\Http\Controllers\DocumentController::class, 'saveDocuments']);
    Route::post('/files', action: [twa\smsautils\Http\Controllers\FileController::class, 'upload']);

    Route::post('/activity-log', action: [twa\smsautils\Http\Controllers\ActivityLogController::class, 'getActivityLogs']);
    Route::get('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'getImportConfig']);
    Route::post('/super-import/{identifier}', action: [twa\smsautils\Http\Controllers\ImportController::class, 'import']);


    Route::get('document-schemas/{document_for}/documents/options', [twa\smsautils\Http\Controllers\DocumentSchemaController::class, 'uploadOptions'])->where('document_for', 'crn|mawb|hst|hawb|pawb')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('document-schemas/{document_for}/documents', [twa\smsautils\Http\Controllers\DocumentSchemaController::class, 'getDocuments'])->where('document_for', 'crn|mawb|hst|hawb|pawb')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    
    Route::resource('document-schemas', \twa\smsautils\Http\Controllers\DocumentSchemaController::class)->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
   
    
    Route::get('attribute-for/options', [AttributesController::class, 'attributesForOptions'])->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('attribute-types/options', [AttributesController::class, 'attributeTypesOptions'])->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('attributes/{attribute_for}/fields', [AttributesController::class, 'fields'])->where('attribute_for', 'ADDRESS|MAWB_MANIFEST|SHIPMENT|SHIPPER')->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::resource('attributes', AttributesController::class)->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
    Route::get('attributes/{id}/details', [AttributesController::class, 'details'])->middleware([twa\smsautils\Http\Middleware\AuthMandatoryMiddleware::class]);
});
