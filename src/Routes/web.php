<?php


use Illuminate\Support\Facades\Route;



use twa\smsautils\Http\Controllers\SmsaLabelController;



Route::get('transaction-inventories/{transaction_inventory_id}/payment-receipt', [twa\smsautils\Http\Controllers\PaymentReceiptController::class, 'show']);


Route::get('/awb/{awb}/view', [twa\smsautils\Http\Controllers\SmsaLabelController::class, 'viewLabel'])->name('awb.view');
Route::get('/awb/{awb}/pdf', [SmsaLabelController::class, 'generatePdf'])->name('awb.pdf');

Route::get('/run-migration-pack', [twa\smsautils\Http\Controllers\MigrationsController::class, 'run']);
