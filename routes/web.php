<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Bookstore management routes
Route::resource('books', BookController::class);
Route::resource('cashiers', CashierController::class);
Route::resource('distributors', DistributorController::class);
Route::resource('purchases', PurchaseController::class);
Route::resource('sales', SaleController::class);
