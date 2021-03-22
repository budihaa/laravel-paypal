<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('/', [OrderController::class, 'index']);
Route::post('/paypal-capture-payment', [OrderController::class, 'capturePayment']);
