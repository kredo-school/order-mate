<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;

class AdminAnalyticsController extends Controller
{
    // 管理画面（店舗選択 + 初期データを渡す）
    public function index(Request $request)
    {
        // すべての store（admin は全店舗見られる前提）
        $stores = DB::table('stores')->select('id', 'store_name', 'user_id')->orderBy('id')->get();

        // クエリの store_id が無ければ最初の店舗を選択
        $currentStoreId = $request->query('store_id') ?? ($stores->first()->id ?? null);

        // store -> user_id を取り出す（orders は user_id で紐づいている）
        $store = $currentStoreId ? DB::table('stores')->where('id', $currentStoreId)->first() : null;
        $userId = $store->user_id ?? null;

        if (! $userId) {
            // 店舗がなければ空の値を渡す
            $salesToday = $salesWeekly = $salesMonthly = 0;
            $dailyStats = collect();
            $weeklyStats = collect();
            $monthlyStats = collect();
        } else {
            // --- 売上（today/week/month）
            $salesToday = Order::where('user_id', $userId)->whereDate('created_at', today())->sum('total_price');
            $salesWeekly = Order::where('user_id', $userId)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('total_price');
            $salesMonthly = Order::where('user_id', $userId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total_price');

            // --- 日別
            $dailyStats = Order::where('user_id', $userId)
                ->select(
                    DB::raw('DATE(created_at) as raw_date'),
                    DB::raw('DAYNAME(created_at) as day_of_week'),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 THEN ROUND(SUM(total_price) / SUM(guest_count), 2) ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw('DATE(created_at), DAYNAME(created_at)'))
                ->orderBy('raw_date', 'asc')
                ->get()
                ->map(function ($row) {
                    $row->date = date('Y/m/d', strtotime($row->raw_date));
                    $row->sales = (float) $row->sales;
                    $row->guests = (int) $row->guests;
                    return $row;
                });

            // --- 週別
            $weeklyStats = Order::where('user_id', $userId)
                ->select(
                    DB::raw("YEARWEEK(created_at, 1) as week_key"),
                    DB::raw('MIN(DATE(created_at)) as start_date'),
                    DB::raw('MAX(DATE(created_at)) as end_date'),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 THEN ROUND(SUM(total_price) / SUM(guest_count), 2) ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw('YEARWEEK(created_at, 1)'))
                ->orderBy('week_key', 'asc')
                ->get()
                ->map(function ($row) {
                    $row->sales = (float) $row->sales;
                    $row->guests = (int) $row->guests;
                    $row->week_label = sprintf("%s〜%s", date('Y/m/d', strtotime($row->start_date)), date('Y/m/d', strtotime($row->end_date)));
                    return $row;
                });

            // --- 月別
            $monthlyStats = Order::where('user_id', $userId)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as raw_month"),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 THEN ROUND(SUM(total_price) / SUM(guest_count), 2) ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                ->orderBy('raw_month', 'asc')
                ->get()
                ->map(function ($row) {
                    $row->sales = (float) $row->sales;
                    $row->guests = (int) $row->guests;
                    $row->month_label = str_replace('-', '/', $row->raw_month); // YYYY/MM
                    return $row;
                });

            if ($userId) {
                // --- Top 5 products (daily)
                $topProducts = OrderItem::query()
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                    ->where('orders.user_id', $userId)
                    ->whereDate('orders.created_at', today())
                    ->select(
                        'menus.name as product_name',
                        DB::raw('SUM(order_items.quantity) as total_qty'),
                        DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
                    )
                    ->groupBy('menus.name')
                    ->orderByDesc('total_qty')
                    ->limit(5)
                    ->get();
            } else {
                $topProducts = collect();
            }
        }

        return view('admins.analytics', compact(
            'stores',
            'currentStoreId',
            'salesToday',
            'salesWeekly',
            'salesMonthly',
            'dailyStats',
            'weeklyStats',
            'monthlyStats',
            'topProducts'
        ));
    }

    // --- getStats / getTopProducts / getOrderDetails は既存の実装を store_id 対応にする ---
    // （以前の getStats をコピーして、Auth::id を store->user_id に置き換えてください）
    public function getStats(Request $request)
    {
        $storeIds = $request->query('store_ids', []); // 複数対応
        $userIds = DB::table('stores')->whereIn('id', $storeIds)->pluck('user_id')->toArray();
    
        if (empty($userIds)) {
            return response()->json([
                'labels' => collect(),
                'sales' => collect(),
                'guests' => collect(),
                'table_html' => view('partials.analytics_table', ['stats' => collect(), 'range' => 'daily'])->render()
            ]);
        }
    
        $range = $request->query('range', 'daily');
    
        if ($range === 'daily') {
            $stats = Order::whereIn('user_id', $userIds)
                ->select(
                    DB::raw('DATE(created_at) as raw_date'),
                    DB::raw('DAYNAME(created_at) as day_of_week'),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 THEN ROUND(SUM(total_price)/SUM(guest_count),2) ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw('DATE(created_at), DAYNAME(created_at)'))
                ->orderBy('raw_date','asc')
                ->get()
                ->map(function ($row) {
                    $row->date = date('Y/m/d', strtotime($row->raw_date));
                    return $row;
                });
        } elseif ($range === 'weekly') {
            $stats = Order::whereIn('user_id', $userIds)
                ->select(
                    DB::raw("YEARWEEK(created_at, 1) as week_key"),
                    DB::raw('MIN(DATE(created_at)) as start_date'),
                    DB::raw('MAX(DATE(created_at)) as end_date'),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 THEN ROUND(SUM(total_price)/SUM(guest_count),2) ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw("YEARWEEK(created_at, 1)"))
                ->orderBy('week_key','asc')
                ->get()
                ->map(function ($row) {
                    $row->week_label = sprintf("%s〜%s", date('Y/m/d', strtotime($row->start_date)), date('Y/m/d', strtotime($row->end_date)));
                    return $row;
                });
        } else {
            $stats = Order::whereIn('user_id', $userIds)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as raw_month"),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 THEN ROUND(SUM(total_price)/SUM(guest_count),2) ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                ->orderBy('raw_month','asc')
                ->get()
                ->map(function ($row) {
                    $row->month_label = str_replace('-', '/', $row->raw_month);
                    return $row;
                });
        }
    
        return response()->json([
            'labels' => $stats->pluck($range === 'monthly' ? 'month_label' : ($range === 'weekly' ? 'week_label' : 'date')),
            'sales'  => $stats->pluck('sales'),
            'guests' => $stats->pluck('guests'),
            'table_html' => view('partials.analytics_table', ['stats' => $stats, 'range' => $range])->render(),
        ]);
    }
    
    public function getTopProducts(Request $request)
    {
        $storeIds = $request->query('store_ids', []);
        $userIds = DB::table('stores')->whereIn('id', $storeIds)->pluck('user_id')->toArray();
    
        if (empty($userIds)) {
            return response()->json([
                'labels' => collect(),
                'quantities' => collect(),
                'sales' => collect(),
            ]);
        }
    
        $range = $request->query('range', 'daily');
        $start = $request->query('start');
        $end   = $request->query('end');
    
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereIn('orders.user_id', $userIds);
    
        if ($range === 'custom' && $start && $end) {
            $query->whereBetween('orders.created_at', [$start . " 00:00:00", $end . " 23:59:59"]);
        } elseif ($range === 'daily') {
            $query->whereDate('orders.created_at', today());
        } elseif ($range === 'weekly') {
            $query->whereBetween('orders.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'monthly') {
            $query->whereYear('orders.created_at', now()->year)->whereMonth('orders.created_at', now()->month);
        }
    
        $topProducts = $query
            ->select('menus.name as product_name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'))
            ->groupBy('menus.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    
        return response()->json([
            'labels' => $topProducts->pluck('product_name'),
            'quantities' => $topProducts->pluck('total_qty'),
            'sales'      => $topProducts->pluck('total_sales'),
        ]);
    }
    

    public function getOrderDetails(Request $request)
    {
        $storeId = $request->query('store_id');
        $store = $storeId ? DB::table('stores')->where('id', $storeId)->first() : null;
        $userId = $store->user_id ?? null;

        $date = $request->query('date'); // フロントは "YYYY/MM/DD"
        $queryDate = $date ? str_replace('/', '-', $date) : null; // -> "YYYY-MM-DD"

        if (! $userId || ! $queryDate) {
            return '<div>No orders</div>';
        }

        $orders = Order::where('user_id', $userId)
            ->whereDate('created_at', $queryDate)
            ->get()
            ->map(function ($order) {
                $durationSeconds = strtotime($order->updated_at) - strtotime($order->created_at);
                $order->duration = $durationSeconds > 0 ? gmdate("H:i", $durationSeconds) : '-';
                $order->avg_spend = $order->guest_count > 0 ? round($order->total_price / $order->guest_count, 2) : 0;
                return $order;
            });

        return view('partials.analytics_order_details', compact('orders'))->render();
    }
}
