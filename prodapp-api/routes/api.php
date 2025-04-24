<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

// Route d'inscription
Route::post('register', [AuthController::class, 'register']);

// Route de connexion
Route::post('login', [AuthController::class, 'login']);

// Route pour la vérification de l'email
Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

// Routes pour les produits, protégées par JWT
Route::middleware('auth:api')->group(function () {
    // Produits (Admin et Utilisateur)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    // Catégories (Admin et Utilisateur)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
});

// Routes protégées pour les administrateurs
Route::middleware('auth:api')->group(function () {
    // Suppression d'un produit - Uniquement pour les administrateurs
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
    // Ajout ou mise à jour des catégories - Uniquement pour les administrateurs
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
});


