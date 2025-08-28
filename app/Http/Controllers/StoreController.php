<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Store;
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

        $tables = collect(range($start, $end))->map(function ($number) use ($store) {
            // 既に同じ番号のテーブルがある場合はそれを返す
            $table = \App\Models\Table::firstOrCreate(
                ['user_id' => $store->id, 'number' => $number],
                ['uuid' => \Illuminate\Support\Str::uuid()]
            );

            return $table;
        });
        return view('managers.stores.qr', compact('store', 'tables'));
    }

}

