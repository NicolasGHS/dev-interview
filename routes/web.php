<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// API route for products
Route::get('/api/products', [ProductController::class, 'getProductsPaginated'])->name('api.products');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
