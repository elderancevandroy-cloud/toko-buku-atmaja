<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// RPC-style API routes for bookstore management
Route::prefix('v1')->group(function () {
    // Books API routes
    Route::get('books', [BookController::class, 'apiIndex']);
    Route::post('books', [BookController::class, 'apiStore']);
    Route::get('books/{id}', [BookController::class, 'apiShow']);
    Route::put('books/{id}', [BookController::class, 'apiUpdate']);
    Route::delete('books/{id}', [BookController::class, 'apiDestroy']);
    
    // Cashiers API routes
    Route::get('cashiers', [CashierController::class, 'apiIndex']);
    Route::post('cashiers', [CashierController::class, 'apiStore']);
    Route::get('cashiers/{id}', [CashierController::class, 'apiShow']);
    Route::put('cashiers/{id}', [CashierController::class, 'apiUpdate']);
    Route::delete('cashiers/{id}', [CashierController::class, 'apiDestroy']);
    
    // Distributors API routes
    Route::get('distributors', [DistributorController::class, 'apiIndex']);
    Route::post('distributors', [DistributorController::class, 'apiStore']);
    Route::get('distributors/{id}', [DistributorController::class, 'apiShow']);
    Route::put('distributors/{id}', [DistributorController::class, 'apiUpdate']);
    Route::delete('distributors/{id}', [DistributorController::class, 'apiDestroy']);
    
    // Purchases API routes
    Route::get('purchases', [PurchaseController::class, 'apiIndex']);
    Route::post('purchases', [PurchaseController::class, 'apiStore']);
    Route::get('purchases/{id}', [PurchaseController::class, 'apiShow']);
    Route::put('purchases/{id}', [PurchaseController::class, 'apiUpdate']);
    Route::delete('purchases/{id}', [PurchaseController::class, 'apiDestroy']);
    
    // Sales API routes
    Route::get('sales', [SaleController::class, 'apiIndex']);
    Route::post('sales', [SaleController::class, 'apiStore']);
    Route::get('sales/{id}', [SaleController::class, 'apiShow']);
    Route::put('sales/{id}', [SaleController::class, 'apiUpdate']);
    Route::delete('sales/{id}', [SaleController::class, 'apiDestroy']);
    
    // GridBuilder data endpoints
    Route::get('books/grid/data', [BookController::class, 'getGridData']);
    Route::get('cashiers/grid/data', [CashierController::class, 'getGridData']);
    Route::get('distributors/grid/data', [DistributorController::class, 'getGridData']);
    Route::get('purchases/grid/data', [PurchaseController::class, 'getGridData']);
    Route::get('sales/grid/data', [SaleController::class, 'getGridData']);
});