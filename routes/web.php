<?php

use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\Admins\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderListController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffCallController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TakeoutController;
use App\Models\Table;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 認証ルート
Auth::routes();

// 認証が必要なルート
Route::group(['middleware' => 'auth'], function () {

    // manager top page
    Route::get('/manager', [HomeController::class, 'index'])->name('manager.home')->middleware('managerLocale');

    // Manager
    Route::group(['prefix' => 'manager', 'as' => 'manager.', 'middleware' => ['managerLocale'],], function () {
        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
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
    
        // Analytics routes
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/data', [AnalyticsController::class, 'getCustomData'])->name('analytics.data');
        Route::get('/analytics/top-products', [AnalyticsController::class, 'getTopProducts'])->name('analytics.topProducts');
        Route::get('/analytics/stats', [AnalyticsController::class, 'getStats'])
        ->name('analytics.stats');
        Route::get('/analytics/order_details', [AnalyticsController::class, 'getOrderDetails'])->name('analytics.orderDetails');
    
        // Tables routes
        // Route::get('/tables', [TableController::class, 'index'])->name('tables');
        Route::get('/tables', [StoreController::class, 'tablesIndex'])->name('tables');
        Route::get('/tables/{tableId}', [OrderController::class, 'historyByTable'])
        ->name('tables.show');
        Route::post('/tables/{table}/checkout', [CheckoutController::class, 'checkoutByManager'])->name('tables.checkout');
        Route::post('/tables/{table}/pay', [CheckoutController::class, 'payByManager'])->name('tables.pay');
    
    
        // Orders routes
        Route::get('/order-list', [OrderListController::class, 'index'])->name('order-list');
    
        // Call Staff
        Route::get('/staff-calls', [StaffCallController::class, 'index'])->name('staffCalls.index');
        Route::post('/staff-calls/{staffCall}/read', [StaffCallController::class, 'markAsRead'])->name('staffCalls.read');
    });
});


// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

// Admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    // 管理者用のルートをここに追加
    Route::get('/dashboard', [AdminController::class, 'index'])->name('index');
    Route::get('/stores/{id}', [AdminController::class, 'show'])->name('show');
    Route::post('/chat/broadcast', [ChatController::class, 'broadcastToManagers'])->name('chat.broadcast');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
    
    // Ajax 用
    Route::get('/analytics/stats', [AdminAnalyticsController::class, 'getStats'])->name('analytics.stats');
    Route::get('/analytics/custom', [AdminAnalyticsController::class, 'getCustomData'])->name('analytics.data');
    Route::get('/analytics/top-products', [AdminAnalyticsController::class, 'getTopProducts'])->name('analytics.topProducts');
    Route::get('/analytics/orders', [AdminAnalyticsController::class, 'getOrderDetails'])->name('analytics.orderDetails');
});


Route::post('/order-items/{orderItem}/toggle-status', [OrderController::class, 'toggleStatus'])
    ->name('orderItems.toggleStatus');


// Guests
Route::group(['prefix' => 'guest/{storeName}/{tableUuid}', 'as' => 'guest.', 'middleware' => ['guestLocale'],], function () {
    Route::get('/', [GuestController::class, 'index'])->name('index');
    Route::get('/call', [GuestController::class, 'call'])->name('call');
    Route::get('/show/{id}', [GuestController::class, 'show'])->name('show');
    Route::get('/products/{categoryId}', [GuestController::class, 'byCategory'])
        ->name('products.byCategory');
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('/welcome', [GuestController::class, 'welcome'])->name('welcome');
    Route::match(['get', 'post'], '/start-order', [GuestController::class, 'startOrder'])->name('startOrder');

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

    // ゲスト側のスタッフ呼び出し（POST）
    Route::post('/call', [GuestController::class, 'storeCall'])->name('call.store');
    // 呼び出し完了画面 (GET)
    Route::get('/call-complete/{call}', [GuestController::class, 'callComplete'])->name('call.complete');
    // 呼び出しの順位を返す API（GET）
    Route::get('/staff-calls/{call}/priority', [GuestController::class, 'callPriority'])->name('call.priority');

    // Payment (Stripe)
    Route::post('/payment', [CheckoutController::class, 'payment'])->name('payment');
    Route::get('/payment/success', [CheckoutController::class, 'success'])->name('payment.success');

    // checkout
    Route::post('/checkout', function ($storeName, $tableUuid) {
        $table = Table::where('uuid', $tableUuid)
                      ->with('user.store')
                      ->firstOrFail();
        $store = $table->user->store;

        return view('guests.checkout', compact('store', 'table'));
    })->name('checkout');

    Route::match(['get', 'post'], '/checkout/complete', [CheckoutController::class, 'checkout'])->name('checkout.complete');
});


// チャットルート
Route::get('/chats/{userId}', [ChatController::class, 'show'])->name('chat.show');
Route::post('/chats/{chatId}/send', [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/{chat}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unreadCount');
Route::get('/chat/unread-per-store', [ChatController::class, 'unreadPerStore']);
Route::get('/chat/unread-per-store', [ChatController::class, 'unreadPerStore']);
