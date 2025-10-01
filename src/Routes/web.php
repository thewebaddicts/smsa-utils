<?php


use Illuminate\Support\Facades\Route;



use twa\smsautils\Http\Controllers\SmsaLabelController;




Route::get('/awb/{awb}/view', [twa\smsautils\Http\Controllers\SmsaLabelController::class, 'viewLabel'])->name('awb.view');
Route::get('/awb/{awb}/pdf', [SmsaLabelController::class, 'generatePdf'])->name('awb.pdf');