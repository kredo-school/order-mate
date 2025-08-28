<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $all_stores = Store::withCount([
            'chats as unread_messages_count' => function ($query) {
                $query->join('messages', 'chats.id', '=', 'messages.chat_id')
                ->where('messages.is_read', false)
                ->where('messages.user_id', '!=', Auth::id());
            }
        ])->get();

        return view('admins.index', compact('all_stores'));
    }

    // AdminController
    public function show($id){
        $store = Store::with('user')->findOrFail($id);

        // チャットを取得または作成
        $chat = Chat::firstOrCreate(
            ['user_id' => $store->user_id, 'chat_type' => 'manager_admin'],
            ['user_id' => $store->user_id, 'chat_type' => 'manager_admin']
        );

        $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // 👇 ここで未読の最初のメッセージを探す
        $firstUnreadId = $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->orderBy('id', 'asc')
            ->value('id'); // 最初の未読メッセージのID

        return view('admins.show', compact('store', 'chat', 'messages', 'firstUnreadId'));
    }
}
