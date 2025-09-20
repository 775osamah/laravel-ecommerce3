<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{code}', [OrderController::class, 'show']);
    
    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('admin/categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('admin/products', ProductController::class)->except(['index', 'show']);
        Route::patch('admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::get('admin/users', [AuthController::class, 'index']);
        Route::patch('admin/users/{id}/toggle', [AuthController::class, 'toggleStatus']);
    });
});