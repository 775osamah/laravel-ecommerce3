<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 Prefix
Route::prefix('v1')->group(function () {

    // Public Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Public Resource Routes (No Auth Needed)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    // Protected Routes (require Sanctum token)
    Route::middleware('auth:sanctum')->group(function () {

        // Protected Auth Routes
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // Customer Order Routes
        Route::prefix('orders')->group(function () {
            Route::post('/', [OrderController::class, 'store']); // Create order
            Route::get('/', [OrderController::class, 'index']); // List user's orders
            Route::get('/{code}', [OrderController::class, 'show']); // Show order details
        });

        // Admin Routes
        Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
            // Admin Categories Management
            Route::prefix('categories')->group(function () {
                Route::post('/', [CategoryController::class, 'store']);
                Route::patch('/{id}', [CategoryController::class, 'update']);
                Route::delete('/{id}', [CategoryController::class, 'destroy']);
            });

            // Admin Products Management
            Route::prefix('products')->group(function () {
                Route::post('/', [ProductController::class, 'store']);
                Route::patch('/{id}', [ProductController::class, 'update']);
                Route::delete('/{id}', [ProductController::class, 'destroy']);
                Route::post('/{id}/images', [ProductController::class, 'uploadImages']);
            });

            // Admin Orders Management
            Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus']);

            // Admin Users Management
            Route::get('/users', [UserController::class, 'index']);
            Route::patch('/users/{id}/toggle', [UserController::class, 'toggleStatus']);
        });

    });

});


// Group all v1 API routes under the 'v1' prefix
Route::prefix('v1')->group(function () {
    // Categories routes
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    // Add other v1 routes here
});




Route::prefix('v1')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
});