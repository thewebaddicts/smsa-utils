<?php


use Illuminate\Support\Facades\Route;



use twa\smsautils\Http\Controllers\SmsaLabelController;



Route::get('transaction-inventories/{transaction_inventory_id}/payment-receipt', [twa\smsautils\Http\Controllers\PaymentReceiptController::class, 'show'])->name('payment-receipt');

Route::get('/awb/{awb}/view', [SmsaLabelController::class, 'viewLabel'])->name('awb.view');
Route::get('/awb/{awb}/pdf/{token}', [SmsaLabelController::class, 'generatePdf'])->name('awb.pdf');
Route::get('/awb/{awb}/invoice/{token}', [SmsaLabelController::class, 'generateInvoice'])->name('awb.invoice');
Route::get('awb/{awb}/invoice/view', [SmsaLabelController::class, 'viewInvoice'])->name('awb.invoice.view');

// Route::get('/awb/{awb}/pdf', [SmsaLabelController::class, 'generatePdf'])->name('awb.pdf');

Route::get('/run-migration-pack', [twa\smsautils\Http\Controllers\MigrationsController::class, 'run']);
