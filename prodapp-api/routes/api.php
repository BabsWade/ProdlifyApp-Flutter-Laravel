<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('products', [ProductController::class, 'index']);
/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {
    // Produits (utilisateur + admin)
 
    Route::get('products/{id}', [ProductController::class, 'show']);
   
    Route::put('products/{id}', [ProductController::class, 'update']);

    // Catégories (utilisateur + admin)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:is-admin')->group(function () {
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::post('products', [ProductController::class, 'store']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    });
});


