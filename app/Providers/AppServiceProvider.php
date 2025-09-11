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
// app/Providers/AppServiceProvider.php

    public function boot(Request $request): void
    {
        View::composer('*', function ($view) use ($request) {
            $userStore = Auth::check() ? Auth::user()->store : null;
            $view->with('userStore', $userStore);

            $tableUuid = $request->route('tableUuid');
            $table = null;
            $storeName = null;
            $store = null;     // ← 追加
            $totalPrice = 0;   // ← 既に追加済み

            if ($tableUuid) {
                $table = Table::where('uuid', $tableUuid)
                            ->with('user.store')
                            ->first();

                if ($table && $table->user && $table->user->store) {
                    $store = $table->user->store;            // ← ここでモデルそのものを取り出す
                    $storeName = $store->store_name;

                    // open か closed どちらかの order を拾う
                    $order = Order::where('table_id', $table->id)
                                ->whereIn('status', ['open', 'closed'])
                                ->latest()
                                ->first();

                    $isPaid = false;
                    if ($order) {
                        $totalPrice = $order->total_price;
                        $isPaid = (bool) $order->is_paid;
                    }

                    $view->with(compact('table', 'store', 'storeName', 'tableUuid', 'totalPrice', 'isPaid'));
                }
            }
        });
    }

}
