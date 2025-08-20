<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StoreController extends Controller
{
    public function index(){
        $store = Store::where('user_id', Auth::id())->first();
        return view('managers.stores.index', compact('store'));
    }

    public function edit(){
        $store = Store::where('user_id', Auth::id())->first(); // 既存のストアを取得（なければnull）
        return view('managers.stores.save', compact('store'));
    }

    public function save(Request $request){
        $validated = $request->validate([
            'store_name'   => 'nullable|string|max:255',
            'store_url'    => 'nullable|string|max:255',
            'address'      => 'nullable|string',
            'phone'        => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'store_photo'  => 'nullable|image|max:255',
            'open_hours'   => 'nullable|string',
            'password'     => 'nullable|string|min:8|confirmed', 
            // password_confirmation フィールドもフォームに必要
        ]);

        // 画像がアップロードされた場合
        if ($request->hasFile('store_photo')) {
            $validated['store_photo'] = $request->file('store_photo')->store('store_photos', 'public');
        }

        // Store 更新または作成
        Store::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        // パスワード更新がある場合
        if (!empty($validated['password'])) {
            $user = User::find(Auth::id());
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

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
                ['store_id' => $store->id, 'number' => $number],
                ['uuid' => \Illuminate\Support\Str::uuid()]
            );

            return $table;
        });
        return view('managers.stores.qr', compact('store', 'tables'));
    }

}
