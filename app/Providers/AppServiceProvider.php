<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        // 全ビュー共通の変数を共有
        View::composer('*', function ($view) use ($request) {
            $userStore = Auth::check() ? Auth::user()->store : null;
            $view->with('userStore', $userStore);

            $tableUuid = $request->route('tableUuid');
            $table = null;
            $storeName = null;
            $cartCount = 0;

            if ($tableUuid) {
                $table = Table::where('uuid', $tableUuid)
                    ->with('user.store')
                    ->first();

                if ($table && $table->user && $table->user->store) {
                    $storeName = $table->user->store->store_name;
                    // カートの合計数を計算
                    $order = Order::where('table_id', $table->id)
                        ->where('status', 'pending')
                        ->first();
                    $cartCount = $order ? $order->orderItems->sum('quantity') : 0;
                }
            }

            $view->with(compact('table', 'storeName', 'tableUuid', 'cartCount'));
        });
    }
}
