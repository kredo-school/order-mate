<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSent as EventsMessageSent;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mailer\Event\MessageEvent;

class ChatController extends Controller
{
    /**
     * チャット表示
     */
    // Manager側のStoreController
    public function show()
    {
        $store = Auth::user()->store;

        // 最新チャットを取得（なければ作成）
        $chat = Chat::where('user_id', $store->user_id)
            ->where('chat_type', 'manager_admin')
            ->latest('id') // 最新のものを取得
            ->first();

        if (!$chat) {
            // なければ作成
            $chat = Chat::create([
                'user_id' => $store->user_id,
                'chat_type' => 'manager_admin',
            ]);
        }

        $messages = $chat->messages()->with('user')->orderBy('created_at', 'asc')->get();

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

        // メッセージ送信イベント（リアルタイムチャット用）
        broadcast(new MessageSent($message))->toOthers();

        // 🔔 通知イベントも発火（フロントで通知音 or バッジ表示）
        broadcast(new \App\Events\NewNotificationEvent(
            $request->content,   // $message
            $chat->user_id       // $receiverId（通知を受け取る人）
        ));

        return response()->json($message);
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
        $chatIds = Chat::where('user_id', Auth::id())->pluck('id');

        $count = Message::whereIn('chat_id', $chatIds)
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->count();
        return response()->json(['count' => $count]);
    }


    public function unreadPerStore()
    {
        $stores = Store::withCount(['chats as unread_messages_count' => function ($q) {
            $q->whereHas('messages', function ($mq) {
                $mq->where('is_read', false)
                    ->where('user_id', '!=', Auth::id());
            });
        }])->get(['id']);

        return response()->json($stores->map(fn($s) => [
            'id' => $s->id,
            'count' => $s->unread_messages_count,
        ]));
    }

    public function broadcastToManagers(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'manager_ids' => 'required|array',   // 複数選択対応
            'manager_ids.*' => 'integer|exists:users,id',
        ]);

        $admin = Auth::user();
        /** @var \App\Models\User $admin */
        if (!$admin->isAdmin()) {
            abort(403, 'Non-admin access denied.');
        }

        // === 送信対象のマネージャーを決定 ===
        if (in_array('all', $request->manager_ids)) {
            // 全員
            $managers = User::where('role', 1)->get();
        } else {
            // 選択されたマネージャーのみ
            $managers = User::whereIn('id', $request->manager_ids)
                ->where('role', 1)
                ->get();
        }

        foreach ($managers as $manager) {
            $store = $manager->store;
            if (!$store) continue; // store を持たない manager はスキップ

            // 既存チャットを取得 or 作成
            $chat = Chat::firstOrCreate(
                ['user_id' => $store->user_id, 'chat_type' => 'manager_admin'],
                ['user_id' => $store->user_id, 'chat_type' => 'manager_admin']
            );

            // メッセージ保存
            $message = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $admin->id,
                'content' => $request->content,
            ]);

            // リアルタイム配信
            broadcast(new MessageSent($message))->toOthers();
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
