<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\OrderController;

Route::get('/', [FrontController::class, 'index'])->name('front.index');

Route::get('/browse/{category:slug}', [FrontController::class, 'category'])->name('front.category');

Route::get('/details/{package:slug}', [FrontController::class, 'details'])->name('front.details');

Route::post('/order/begin{category:slug}', [OrderController::class, 'saveOrder'])->name('front.save_order');

Route::get('/order/book/', [OrderController::class, 'book'])->name('front.book');


Route::get('/order/customer-data', [OrderController::class, 'customerData'])->name('front.customer_data');
Route::post('/order/customer-data/save', [OrderController::class, 'SaveCustomerData'])->name('front.save_customer_data');

Route::get('/order/payment', [OrderController::class, 'payment'])->name('front.payment');
Route::post('/order/payment/confirm', [OrderController::class, 'paymentConfirm'])->name('front.payment_confirm');

Route::get('/order/finished/{productTransaction:id}', [OrderController::class, 'orderFinished'])->name('front.order_finished');
