<?php

use App\Http\Controllers\CardItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
Route::get('/basket', function () {
    return Inertia::render('Basket');
})->name('basket');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('dashboard-navbar', [DashboardController::class, 'indexNavbar'])->middleware(['auth', 'verified'])->name('dashboard.navbar');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/api/products', [ProductController::class, 'getProductsPaginated'])->name('api.products');
Route::get('/products/{id}', [ProductController::class, 'getProductById'])->name('product.getById');

Route::post('/cart/add', [CardItemController::class, 'addCardItem'])->name('cart.add');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
