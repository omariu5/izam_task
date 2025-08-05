<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Inventory listing with filters
    Route::get('/inventory', [InventoryController::class, 'index']);

    // Stock transfers
    Route::post('/stock-transfers', [StockTransferController::class, 'store']);

    // Warehouse specific inventory
    Route::get('/warehouses/{warehouse}/inventory', [WarehouseController::class, 'inventory']);
});
