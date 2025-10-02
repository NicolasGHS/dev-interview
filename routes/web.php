<?php

use App\Http\Controllers\CardItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
Route::get('/basket', function () {
    return Inertia::render('Basket');
})->name('basket');
Route::get('/order-confirmation', function () {
    return Inertia::render('OrderConfirmation');
})->name('order.confirmation');
Route::get('/likes', [LikeController::class, 'index'])->name('likes');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('dashboard-navbar', [DashboardController::class, 'indexNavbar'])->middleware(['auth', 'verified'])->name('dashboard.navbar');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
// Route::get('/api/products', [ProductController::class, 'getProductsPaginated'])->name('api.products');
Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');
Route::get('/products/{id}', [ProductController::class, 'getProductById'])->name('product.getById');

Route::post('/cart/add', [CardItemController::class, 'addCardItem'])->name('cart.add');
Route::get('/cart', [CardItemController::class, 'getCartItems'])->name('cart.items');
Route::delete('/cart/clear', [CardItemController::class, 'clearCart'])->name('cart.clear');
Route::patch('/cart/{id}/increment', [CardItemController::class, 'incrementQuantity'])->name('cart.increment');
Route::patch('/cart/{id}/decrement', [CardItemController::class, 'decrementQuantity'])->name('cart.decrement');
Route::patch('/cart/{id}/update', [CardItemController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/{id}', [CardItemController::class, 'removeCartItem'])->name('cart.remove');

Route::post('/likes/{product}/toggle', [LikeController::class, 'toggleLike'])->name('likes.toggle');
Route::get('/api/likes', [LikeController::class, 'getLikedProducts'])->name('api.likes');
Route::post('/api/likes/check', [LikeController::class, 'checkLikes'])->name('api.likes.check');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
