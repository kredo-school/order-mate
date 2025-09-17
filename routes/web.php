<?php

use App\Http\Controllers\Admins\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Models\Table;
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
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    // 管理者用のルートをここに追加
    Route::get('/dashboard', [AdminController::class, 'index'])->name('index');
    Route::get('/stores/{id}', [AdminController::class, 'show'])->name('show');
    Route::post('/chat/broadcast', [ChatController::class, 'broadcastToManagers'])->name('chat.broadcast');
});

// Manager
Route::group(['prefix' => 'manager', 'as' => 'manager.'], function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');  // ← ここを修正
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // ← ここも揃える
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/by-category/{id}', [ProductController::class, 'byCategory'])
        ->name('products.byCategory');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');




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

    // Custom group の options を取得する Ajax 用ルート
    Route::get('/custom/{id}/options', [CustomController::class, 'options'])
        ->name('custom.options');

    // Stores routes
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::post('/stores/save', [StoreController::class, 'save'])->name('stores.save');
    Route::get('/stores/qr-code', [StoreController::class, 'qrCode'])->name('stores.qrCode');
    Route::post('/stores/generate-qr', [StoreController::class, 'generateQr'])->name('stores.generateQr');
});

// Guests
Route::group(['prefix' => 'guest/{storeName}/{tableUuid}', 'as' => 'guest.'], function () {
    Route::get('/', [GuestController::class, 'index'])->name('index');
    Route::get('/call', [GuestController::class, 'call'])->name('call');
    Route::get('/show/{id}', [GuestController::class, 'show'])->name('show');
    Route::get('/products/{categoryId}', [GuestController::class, 'byCategory'])
        ->name('products.byCategory');
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');


    // カート関連
    Route::post('/cart/add/{menu}', [OrderController::class, 'add'])->name('cart.add');
    Route::get('/cart/add-complete', function ($storeName, $tableUuid) {
        $table = Table::where('uuid', $tableUuid)
            ->with('user.store')
            ->firstOrFail();
        $store = $table->user->store;

        return view('guests.add-complete', compact('store', 'table', 'storeName', 'tableUuid'));
    })->name('cart.addComplete');
    Route::get('/cart', [OrderController::class, 'show'])->name('cart.show');
    Route::delete('/cart/{orderItem}', [OrderController::class, 'destroy'])->name('cart.destroy');
    Route::get('/cart/{orderItem}/edit', [OrderController::class, 'edit'])->name('cart.edit');
    Route::patch('/cart/{orderItem}', [OrderController::class, 'update'])->name('cart.update');

    Route::get('/cart-count', [GuestController::class, 'cartCount'])
        ->name('guest.cart.count');

    // 完了処理
    Route::post('/cart/complete', [OrderController::class, 'complete'])->name('cart.complete');
    Route::get('/order-complete', function ($storeName, $tableUuid) {
        $table = Table::where('uuid', $tableUuid)->with('user.store')->firstOrFail();
        $store = $table->user->store;

        return view('guests.order-complete', compact('store', 'table'));
    })->name('order.complete');
    Route::get('/order-history', [OrderController::class, 'history'])->name('orderHistory');
});


// チャットルート
Route::get('/chats/{userId}', [ChatController::class, 'show'])->name('chat.show');
Route::post('/chats/{chatId}/send', [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/{chat}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unreadCount');
Route::get('/chat/unread-per-store', [ChatController::class, 'unreadPerStore']);
Route::get('/chat/unread-per-store', [ChatController::class, 'unreadPerStore']);
