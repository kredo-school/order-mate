<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $storeId = Auth::id();
        $store = null;
    
        if ($storeId) {
            $store = User::withCount([
                'chats as unread_messages_count' => function ($query) {
                    $query->join('messages', 'chats.id', '=', 'messages.chat_id')
                        ->where('messages.is_read', false)
                        ->where('messages.user_id', '!=', Auth::id()); // 自分の送信は除外
                }
            ])->find($storeId);

            // 店舗が無い場合はダッシュボード表示用のダミーオブジェクトを作成
            if (!$store) {
                $store = (object)[
                    'unread_messages_count' => 0,
                ];
            }
        }
    
        return view('managers.home', compact('store'));
    }
    
}
