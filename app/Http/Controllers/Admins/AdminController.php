<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Store;
use App\Models\User;
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
        ])->with('user') // 各storeに紐づくuserも一緒に取得
            ->get();

        // role=1 が manager
        $managers = User::where('role', 1)
            ->with('store')
            ->get();

        return view('admins.index', compact('all_stores', 'managers'));
    }


    // AdminController
    public function show($id)
    {
        $store = Store::with('user')->findOrFail($id);

        // チャットを取得または作成
        $chat = Chat::firstOrCreate(
            ['user_id' => $store->user_id, 'chat_type' => 'manager_admin'],
            ['user_id' => $store->user_id, 'chat_type' => 'manager_admin']
        );

        // 👇 ここで未読メッセージを既読にする
        $chat->messages()
            ->where('user_id', '!=', Auth::id()) // 自分以外が送ったもの
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->get();

        // 👇 ここで未読の最初のメッセージを探す
        $firstUnreadId = $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->orderBy('id', 'asc')
            ->value('id'); // 最初の未読メッセージのID

        return view('admins.show', compact('store', 'chat', 'messages', 'firstUnreadId'));
    }

    // Nav バッジ用：全未読件数
    public function unreadTotal()
    {
        $totalUnread = Store::with('chats.messages')->get()
            ->sum(function ($store) {
                return $store->chats->sum(function ($chat) {
                    return $chat->messages
                        ->where('is_read', false)
                        ->where('user_id', '!=', Auth::id())
                        ->count();
                });
            });

        return response()->json(['count' => $totalUnread]);
    }

    // Store カードバッジ用：各 Store 未読
    public function unreadPerStore()
    {
        $stores = Store::with('chats.messages')->get(['id']);

        $stores = $stores->map(function ($store) {
            $unreadCount = $store->chats->sum(function ($chat) {
                return $chat->messages
                    ->where('is_read', false)
                    ->where('user_id', '!=', Auth::id())
                    ->count();
            });

            return [
                'id' => $store->id,
                'count' => $unreadCount,
            ];
        });

        return response()->json($stores);
    }
}
