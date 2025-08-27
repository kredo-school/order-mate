<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * チャット表示
     */
    // Manager側のStoreController
    public function show()
    {
        $store = Auth::user()->store;
    
        $chat = Chat::firstOrCreate(
            ['store_id' => $store->id, 'chat_type' => 'manager_admin'],
            ['store_id' => $store->id, 'chat_type' => 'manager_admin']
        );
    
        $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->get();
    
        // 👇 ここで未読の最初のメッセージを探す
        $firstUnreadId = $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->orderBy('id', 'asc')
            ->value('id'); // 最初の未読メッセージのID
    
        return view('managers.show', compact('store', 'chat', 'messages', 'firstUnreadId'));
    }

    /**
     * メッセージ送信
     */
    public function send(Request $request, $chatId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $chat = Chat::findOrFail($chatId);

        // メッセージを保存
        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

            // イベントを発火
    broadcast(new MessageSent($message))->toOthers();

    return response()->json($message); // Ajax対応
    }

    public function markAsRead(Chat $chat)
    {
        $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'ok']);
    }

    public function unreadCount()
    {
        $count = Message::where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->count();

        return response()->json(['count' => $count]);
    }

    public function unreadPerStore()
    {
        $stores = Auth::user()->isAdmin()
            ? Store::withCount(['messages as unread_messages_count' => function ($q) {
                $q->where('is_read', false)->where('user_id', '!=', Auth::id());
            }])->get(['id'])
            : collect();

        return response()->json($stores->map(fn($s) => [
            'id' => $s->id,
            'count' => $s->unread_messages_count,
        ]));
    }


}
