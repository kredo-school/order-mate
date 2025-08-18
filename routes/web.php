<?php

use App\Http\Controllers\Admins\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 認証ルート
Auth::routes();

// 認証が必要なルート
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    // 他にも保護したいルートをここへ
});

// Admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function (){
    // 管理者用のルートをここに追加
    Route::get('/dashboard', [AdminController::class, 'index'])->name('index');
    Route::get('/stores/{id}', [AdminController::class, 'show'])->name('show');
});

// Manager
Route::group(['prefix' => 'manager', 'as' => 'manager.'], function () {
    Route::get('/products', [ProductController::class, 'index'])->name('index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('create');
    Route::get('/products/by-category/{id}', [ProductController::class, 'byCategory'])
     ->name('products.byCategory');

    
    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Custom routes
    Route::get('/custom', [CustomController::class, 'index'])->name('custom.index');
    Route::post('/custom/store', [CustomController::class, 'store'])->name('custom.store');
    Route::patch('/custom/update/{id}', [CustomController::class, 'update'])->name('custom.update');
    Route::delete('/custom/delete/{id}', [CustomController::class, 'destroy'])->name('custom.destroy');

    // Stores routes
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::post('/stores/save', [StoreController::class, 'save'])->name('stores.save');

});