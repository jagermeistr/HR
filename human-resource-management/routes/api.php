<?php

use App\Http\Controllers\MpesaB2CController;
use Illuminate\Support\Facades\Route;

Route::post('/mpesa/b2c/result', [MpesaB2CController::class, 'handleB2CResult']);
Route::post('/mpesa/b2c/timeout', [MpesaB2CController::class, 'handleB2CTimeout']);
Route::post('/mpesa/b2c/queue-timeout', [MpesaB2CController::class, 'handleQueueTimeout']);