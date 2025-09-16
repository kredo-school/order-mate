<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Store;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StoreController extends Controller{
    public function index()
    {
        $store = Auth::user()->store;

        if ($store) {
            $chat = Chat::firstOrCreate(
                ['user_id' => $store->user_id, 'chat_type' => 'manager_admin'],
                ['user_id' => $store->user_id, 'chat_type' => 'manager_admin']
            );

            $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->get();

            $firstUnreadId = $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->orderBy('id', 'asc')
            ->value('id');
        } else {
            $chat = null;
            $messages = collect();
            $firstUnreadId = null;
        }

        return view('managers.stores.index', compact('store', 'chat', 'messages', 'firstUnreadId'));
    }

    public function edit(){
        $store = Store::where('user_id', Auth::id())->first(); // 既存のストアを取得（なければnull）
        return view('managers.stores.save', compact('store'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'store_name'   => 'nullable|string|max:255',
            'store_url'    => 'nullable|string|max:255',
            'address'      => 'nullable|string',
            'phone'        => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'store_photo'  => 'nullable|image|max:2048',
            'open_hours'   => 'nullable|string',
            'password'     => 'nullable|string|min:8|confirmed',
            'email'        => 'nullable|email|max:255', // users.email の更新用
        ]);

        // store_photo
        if ($request->hasFile('store_photo')) {
            $validated['store_photo'] = $request->file('store_photo')->store('store_photos', 'public');
        }

        // store 用のデータだけ抜き出す（email / password は user 用なので除外）
        $storeData = collect($validated)->except(['email', 'password'])->toArray();

        // Store 更新（既存 or 新規）
        Store::updateOrCreate(
            ['user_id' => Auth::id()],
            $storeData
        );

        // User 更新
        $user = User::find(Auth::id());
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('manager.stores.index');
    }

    public function qrCode(){
        return view('managers.stores.qr');
    }

    public function generateQr(Request $request){
        $store = Auth::user()->store;
    
        $start = (int)$request->input('table_start');
        $end   = (int)$request->input('table_end');
    
        if ($start > $end) {
            return back()->withErrors('開始番号は終了番号以下にしてください');
        }
    
        // まず全テーブルを inactive にする（論理削除）
        Table::where('user_id', $store->user_id)->update(['is_active' => false]);
    
        // 今回の範囲を active に復活 or 作成
        $tables = collect(range($start, $end))->map(function ($number) use ($store) {
            $table = Table::firstOrNew(
                ['user_id' => $store->user_id, 'number' => $number]
            );
    
            if (!$table->exists) {
                $table->uuid = \Illuminate\Support\Str::uuid();
            }
    
            $table->is_active = true; // アクティブ化
            $table->save();
    
            return $table;
        });
    
        return view('managers.stores.qr', compact('store', 'tables'));
    }

    public function tablesIndex()
    {
        $store = Auth::user()->store;

        if (! $store) {
            // 店舗がない場合は空のコレクション渡す or リダイレクト
            $tables = collect();
        } else {
            // 同じストア(user_id)の is_active = true のやつだけ取得
            $tables = Table::forStore($store->user_id)
                        ->active()
                        ->orderBy('number')
                        ->get();
        }

        return view('managers.tables.tables', compact('tables'));
    }
}

