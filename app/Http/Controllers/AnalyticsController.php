<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 売上集計（今日・週・月）
        $salesToday = Order::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->sum('total_price');

        $salesWeekly = Order::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_price');

        $salesMonthly = Order::where('user_id', $userId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_price');

        // 日別集計
        $dailyStats = Order::where('user_id', $userId)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('DAYNAME(created_at) as day_of_week'),
                DB::raw('SUM(total_price) as sales'),
                DB::raw('SUM(guest_count) as guests'),
                DB::raw('CASE WHEN SUM(guest_count) > 0 
                              THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                              ELSE 0 END as avg_spend'),
                DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
            )
            ->groupBy(DB::raw('DATE(created_at), DAYNAME(created_at)'))
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($row) {
                $row->sales  = (float) $row->sales;
                $row->guests = (int) $row->guests;
                return $row;
            });

        // 週別集計
        $weeklyStats = Order::where('user_id', $userId)
            ->select(
                DB::raw("YEARWEEK(created_at, 1) as week_key"),
                DB::raw('MIN(DATE(created_at)) as start_date'),
                DB::raw('MAX(DATE(created_at)) as end_date'),
                DB::raw('SUM(total_price) as sales'),
                DB::raw('SUM(guest_count) as guests'),
                DB::raw('CASE WHEN SUM(guest_count) > 0 
                            THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                            ELSE 0 END as avg_spend')
            )
            ->groupBy(DB::raw('YEARWEEK(created_at, 1)'))
            ->orderBy('week_key', 'asc')
            ->get()
            ->map(function ($row) {
                $row->sales  = (float) $row->sales;
                $row->guests = (int) $row->guests;
                $row->week_label = sprintf(
                    "%s〜%s",
                    date('m/d', strtotime($row->start_date)),
                    date('m/d', strtotime($row->end_date))
                );
                return $row;
            });

        // 月別集計
        $monthlyStats = Order::where('user_id', $userId)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_label"),
                DB::raw('SUM(total_price) as sales'),
                DB::raw('SUM(guest_count) as guests'),
                DB::raw('CASE WHEN SUM(guest_count) > 0 
                              THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                              ELSE 0 END as avg_spend')
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month_label', 'asc')
            ->get()
            ->map(function ($row) {
                $row->sales  = (float) $row->sales;
                $row->guests = (int) $row->guests;
                return $row;
            });

        return view('managers.stores.analytics', compact(
            'salesToday',
            'salesWeekly',
            'salesMonthly',
            'dailyStats',
            'weeklyStats',
            'monthlyStats'
        ));
    }

    /**
     * 日別カスタム範囲のデータ
     */
    public function getCustomData(Request $request)
    {
        $userId = Auth::id();
        $start = $request->query('start', now()->subMonth()->toDateString());
        $end   = $request->query('end', now()->toDateString());

        $stats = Order::where('user_id', $userId)
            ->whereBetween('created_at', [
                $start . " 00:00:00",
                $end . " 23:59:59"
            ])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('DAYNAME(created_at) as day_of_week'),
                DB::raw('SUM(total_price) as sales'),
                DB::raw('SUM(guest_count) as guests'),
                DB::raw('CASE WHEN SUM(guest_count) > 0 
                              THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                              ELSE 0 END as avg_spend'),
                DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
            )
            ->groupBy(DB::raw('DATE(created_at), DAYNAME(created_at)'))
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($row) {
                $row->sales  = (float) $row->sales;
                $row->guests = (int) $row->guests;
                return $row;
            });

        return response()->json([
            'labels' => $stats->pluck('date'),
            'sales'  => $stats->pluck('sales'),
            'guests' => $stats->pluck('guests'),
            'table_html' => view('partials.analytics_table', ['stats' => $stats])->render(),
        ]);
    }

    /**
     * 商品別売上 Top5
     */
    public function getTopProducts(Request $request)
    {
        $userId = Auth::id();
        $range = $request->query('range', 'daily');
        $start = $request->query('start');
        $end   = $request->query('end');
    
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('orders.user_id', $userId);
    
        if ($range === 'custom' && $start && $end) {
            $query->whereBetween('orders.created_at', [
                $start . " 00:00:00",
                $end . " 23:59:59"
            ]);
        } elseif ($range === 'daily') {
            $query->whereDate('orders.created_at', today());
        } elseif ($range === 'weekly') {
            $query->whereBetween('orders.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'monthly') {
            $query->whereYear('orders.created_at', now()->year)
                  ->whereMonth('orders.created_at', now()->month);
        }
    
        $topProducts = $query
            ->select(
                'menus.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->groupBy('menus.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $row->total_qty   = (int) $row->total_qty;
                $row->total_sales = (float) $row->total_sales;
                return $row;
            });
    
        return response()->json([
            'labels' => $topProducts->pluck('product_name'),
            'quantities' => $topProducts->pluck('total_qty'),
            'sales'      => $topProducts->pluck('total_sales'),
        ]);
    }

    public function getStats(Request $request)
    {
        $userId = Auth::id();
        $range = $request->query('range', 'daily');
    
        if ($range === 'daily') {
            $stats = Order::where('user_id', $userId)
                ->select(
                    DB::raw('DATE(created_at) as raw_date'),
                    DB::raw('DAYNAME(created_at) as day_of_week'),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 
                                  THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                                  ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw('DATE(created_at), DAYNAME(created_at)'))
                ->orderBy('raw_date', 'asc')
                ->get()
                ->map(function ($row) {
                    // YYYY/MM/DD に整形
                    $row->date = date('Y/m/d', strtotime($row->raw_date));
                    return $row;
                });
    
        } elseif ($range === 'weekly') {
            $stats = Order::where('user_id', $userId)
                ->select(
                    DB::raw("YEARWEEK(created_at, 1) as week_key"),
                    DB::raw('MIN(DATE(created_at)) as start_date'),
                    DB::raw('MAX(DATE(created_at)) as end_date'),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 
                                  THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                                  ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw("YEARWEEK(created_at, 1)"))
                ->orderBy('week_key', 'asc')
                ->get()
                ->map(function ($row) {
                    // YYYY/MM/DD〜YYYY/MM/DD に整形
                    $row->week_label = sprintf(
                        "%s〜%s",
                        date('Y/m/d', strtotime($row->start_date)),
                        date('Y/m/d', strtotime($row->end_date))
                    );
                    return $row;
                });
    
        } else { // monthly
            $stats = Order::where('user_id', $userId)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as raw_month"),
                    DB::raw('SUM(total_price) as sales'),
                    DB::raw('SUM(guest_count) as guests'),
                    DB::raw('CASE WHEN SUM(guest_count) > 0 
                                  THEN ROUND(SUM(total_price) / SUM(guest_count), 2) 
                                  ELSE 0 END as avg_spend'),
                    DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
                )
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                ->orderBy('raw_month', 'asc')
                ->get()
                ->map(function ($row) {
                    // YYYY/MM に整形
                    $row->month_label = str_replace('-', '/', $row->raw_month);
                    return $row;
                });
        }
    
        return response()->json([
            'labels' => $stats->pluck(
                $range === 'monthly' ? 'month_label' :
                ($range === 'weekly' ? 'week_label' : 'date')
            ),
            'sales'  => $stats->pluck('sales'),
            'guests' => $stats->pluck('guests'),
            'table_html' => view('partials.analytics_table', [
                'stats' => $stats,
                'range' => $range,
            ])->render(),
        ]);
    }

    public function getOrderDetails(Request $request)
    {
        $userId = Auth::id();
        $date = $request->query('date');
    
        $orders = Order::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get()
            ->map(function ($order) {
                // created_at と updated_at の差を秒数で計算
                $durationSeconds = strtotime($order->updated_at) - strtotime($order->created_at);
    
                // 負数や null の場合は '-'
                $order->duration = $durationSeconds > 0
                    ? gmdate("H:i", $durationSeconds)
                    : '-';
    
                $order->avg_spend = $order->guest_count > 0
                    ? round($order->total_price / $order->guest_count, 2)
                    : 0;
    
                return $order;
            });
    
        return view('partials.analytics_order_details', compact('orders'))->render();
    }
}
