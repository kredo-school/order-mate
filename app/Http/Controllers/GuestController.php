<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Store;
use App\Models\Table;
use App\Models\Category;
use App\Models\StaffCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GuestController extends Controller
{
    public function index(Request $request, $storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $all_categories = Category::where('user_id', $store->user_id)->get();
        $initialCategory = $all_categories->first();
    
        $table = Table::where('user_id', $store->user_id)
            ->where('uuid', $tableUuid)
            ->firstOrFail();
    
        // --- latest order / welcome 判定（元のロジックを維持） ---
        $latestOrder = $table->orders()->latest()->first();
        if (!$latestOrder || $latestOrder->status === 'closed') {
            return view('guests.welcome', compact('store', 'table'))
                ->with('isGuestPage', true);
        }
    
        // --- 検索 / カテゴリ / 初期カテゴリ の順で products を決定 ---
        if ($request->filled('search')) {
            $search = $request->input('search');
            $products = Menu::where('user_id', $store->user_id)
                ->with('customGroups.customOptions')
                ->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                })
                ->get();
        } elseif ($request->filled('category')) {
            $categoryId = $request->input('category');
            $products = Menu::where('user_id', $store->user_id)
                ->where('menu_category_id', $categoryId)
                ->with('customGroups.customOptions')
                ->get();
        } else {
            $products = $initialCategory
                ? Menu::where('menu_category_id', $initialCategory->id)
                      ->where('user_id', $store->user_id)
                      ->with('customGroups.customOptions')
                      ->get()
                : collect();
        }
    
        $menus = Menu::where('user_id', $store->user_id)->with('category')->get();
    
        $cart = session()->get("cart_{$table->uuid}", []);
        $cartCount = array_sum(array_column($cart, 'quantity'));
    
        return view('guests.index', compact('store', 'table', 'menus', 'all_categories', 'products', 'cartCount'))
            ->with('isGuestPage', true);
    }
    
    
    public function welcome($storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
                    ->where('uuid', $tableUuid)
                    ->firstOrFail();
        $sessionLocale = session('locale');
            if ($sessionLocale) {
                app()->setLocale($sessionLocale);
            } elseif (isset($store) && !empty($store->language)) {
                // manager が設定した stores.language を優先して session に入れる
                session(['locale' => $store->language]);
                app()->setLocale($store->language);
            }

        return view('guests.welcome', compact('store', 'table'))->with('isGuestPage', true);
    }

    // 許可するロケール一覧（必要なら config に移す）
    protected $allowed = ['ja', 'en'];

    /**
     * POST /guest/set-locale
     * body: locale=ja|en
     */
    public function setLocale(Request $request, $storeName, $tableUuid)
    {
        $data = $request->validate([
            'locale' => ['required', 'string', 'in:' . implode(',', $this->allowed)],
        ]);
    
        session(['guest_locale' => $data['locale']]);
    
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'locale' => $data['locale']]);
        }
    
        return back();
    }

    public function startOrder(Request $request, $storeName, $tableUuid)
    {
        $validated = $request->validate([
            'guest_count' => 'required|integer|min:1|max:20',
            'language'    => 'required|string|in:en,ja',
        ]);
    
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
                    ->where('uuid', $tableUuid)
                    ->firstOrFail();
    
        $existingOrder = $table->orders()->where('status', 'open')->latest()->first();
        if ($existingOrder) {
            return redirect()->route('guest.index', [$storeName, $tableUuid]);
        }
    
        $orderType = ($table->number == 0) ? 'takeout' : 'dine-in';
    
        $table->orders()->create([
            'status'      => 'open',
            'guest_count' => $validated['guest_count'],
            'is_paid'     => false,
            'user_id'     => $table->user_id,
            'total_price' => 0,
            'order_type'  => $orderType,
        ]);
    
        // セッションに保存（guest_locale）
        Session::put('guest_locale', $validated['language']);
        Session::put('guests', $validated['guest_count']);
    
        return redirect()->route('guest.index', [$storeName, $tableUuid]);
    }

    public function show($storeName, $tableUuid, $productId)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $all_categories = Category::where('user_id', $store->user_id)->get();
        $table = Table::where('user_id', $store->user_id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();
    
        $latestOrder = $table->orders()->latest()->first();
        if ($latestOrder && $latestOrder->status === 'closed') {
            return redirect()->route('guest.checkout.complete', [
                'storeName' => $store->store_name,
                'tableUuid' => $table->uuid,
            ])->with('error', 'This table has already checked out.');
        }
    
        $product = Menu::where('id', $productId)
            ->where('user_id', $store->user_id)
            ->with('customGroups.customOptions')
            ->firstOrFail();

        return view('guests.show', compact('store', 'table', 'all_categories', 'product'))
            ->with('isGuestPage', true);
    }

    public function call($storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
            ->where('uuid', $tableUuid)
            ->firstOrFail();

        return view('guests.call', compact('store', 'table'))
            ->with('isGuestPage', true);
    }

    // 🚀 スタッフ呼び出し処理（未読があれば再利用）
    public function storeCall(Request $request, $storeName, $tableUuid)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();
    
        $existing = StaffCall::where('table_id', $table->id)
            ->where('is_read', false)
            ->orderBy('created_at')
            ->first();
    
        if ($existing) {
            $call = $existing;
        } else {
            $call = StaffCall::create([
                'table_id' => $table->id,
                'is_read' => false,
            ]);
        }
    
        return redirect()->route('guest.call.complete', [
            'storeName' => $store->store_name,
            'tableUuid' => $table->uuid,
            'call' => $call->id,
        ]);
    }
    
    public function callComplete($storeName, $tableUuid, StaffCall $call)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('user_id', $store->user_id)
                      ->where('uuid', $tableUuid)
                      ->firstOrFail();
    
        $calls = StaffCall::where('is_read', false)
            ->whereHas('table.user.store', function ($q) use ($store) {
                $q->where('id', $store->id);
            })
            ->orderBy('created_at')
            ->get();
    
        $myIndex = $calls->search(fn($c) => $c->id === $call->id);
        $priority = $myIndex !== false ? $myIndex + 1 : null;
    
        return view('guests.call-complete', [
            'store' => $store,
            'table' => $table,
            'storeName' => $storeName,
            'tableUuid' => $tableUuid,
            'call' => $call,
            'priority' => $priority,
        ])->with('isGuestPage', true);
    }

    // 互換用
    public function store(Request $request, $storeName, $tableUuid)
    {
        return $this->storeCall($request, $storeName, $tableUuid);
    }

    // 🚀 呼び出しの順位を返す（Ajax）
    public function callPriority($storeName, $tableUuid, StaffCall $call)
    {
        $table = $call->table;
    
        $calls = StaffCall::where('is_read', false)
            ->whereHas('table.user.store', function ($q) use ($table) {
                $q->where('id', $table->user->store->id);
            })
            ->orderBy('created_at')
            ->get();
    
        $myIndex = $calls->search(fn($c) => $c->id === $call->id);
    
        $priority = $myIndex !== false ? $myIndex + 1 : null;
    
        return response()->json(['priority' => $priority]);
    }

    public function byCategory($storeName, $tableUuid, $categoryId)
    {
        $store = Store::where('store_name', $storeName)->firstOrFail();
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        $products = Menu::where('menu_category_id', $categoryId)
            ->where('user_id', $store->user_id)
            ->get();

        return view('managers.products.partials.products', compact('products'))
            ->with('isGuestPage', true)
            ->with(compact('store', 'table'));
    }

    public function cartCount($storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)->firstOrFail();
        $cart = session()->get("cart_{$table->uuid}", []);
        $totalItems = array_sum(array_column($cart, 'quantity'));

        return response()->json(['totalItems' => $totalItems]);
    }
}
