<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Root route - menampilkan daftar produk
Route::get('/', [ProductController::class, 'index'])->name('home');

// Resource routes untuk products
Route::resource('products', ProductController::class);
