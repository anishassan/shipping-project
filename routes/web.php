<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Customer\ShippingController as CustomerShippingController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/shipping', [AdminShippingController::class, 'index'])->name('shipping.index');
    Route::get('/shipping/create', [AdminShippingController::class, 'create'])->name('shipping.create');
    Route::post('/shipping', [AdminShippingController::class, 'store'])->name('shipping.store');
    Route::get('/shipping/{shipment}', [AdminShippingController::class, 'show'])->name('shipping.show');
    Route::post('/shipping/{shipment}/label', [AdminShippingController::class, 'createLabel'])->name('shipping.create-label');
    Route::post('/shipping/{shipment}/status', [AdminShippingController::class, 'updateStatus'])->name('shipping.update-status');
    Route::post('/shipping/{shipment}/cancel', [AdminShippingController::class, 'cancel'])->name('shipping.cancel');
});

// Customer Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/shipping/track', [CustomerShippingController::class, 'track'])->name('shipping.track');
    Route::post('/shipping/track', [CustomerShippingController::class, 'trackShipment'])->name('shipping.track-shipment');
    Route::get('/shipping/history', [CustomerShippingController::class, 'history'])->name('shipping.history');
    Route::get('/shipping/{shipment}', [CustomerShippingController::class, 'show'])->name('shipping.show');
    Route::get('/shipping/{shipment}/label', [CustomerShippingController::class, 'downloadLabel'])->name('shipping.download-label');
});

// Order Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/process', [OrderController::class, 'process'])->name('orders.process');
    
    // Shipping Calculation Routes
    Route::get('/orders/shipping-services', [OrderController::class, 'getShippingServices'])->name('orders.shipping-services');
    Route::get('/orders/calculate-shipping', [OrderController::class, 'calculateShippingCost'])->name('orders.calculate-shipping');
});
