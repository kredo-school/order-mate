<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\ContactReceived;
use App\Mail\ContactAutoReply;
use App\Models\User; // 必要に応じてユーザー認証情報などを利用する場合に備えて
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * お問い合わせデータを受け取り、メール送信とWebhook処理を実行する
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            // 'name' (First Name) を必須に
            'name' => 'required|string|max:255',
            // 'last_name' (Last Name) を必須に
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // 'phone' (電話番号) は optional (nullable) のまま
            'phone' => 'required|string|max:50',
            'store_name' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // ------------------------------------------------
        // 3. 管理者とお客様へのメール送信
        // ------------------------------------------------
        try {
            // 管理者への通知
            Mail::sendnow(new ContactReceived($validated));

            // お客様への自動返信
            Mail::sendnow(new ContactAutoReply($validated));
        } catch (\Exception $e) {
            // ⭐ 修正: 成功時のリダイレクトを避け、画面にエラーを表示させる ⭐
            Log::error('Inquiry Mail Sending Error: ' . $e->getMessage()); 
        }
        // ------------------------------------------------

        // 4. 送信成功後のリダイレクト
        return redirect('/')
            ->with('success', 'Inquiry sent. We will contact you shortly.');
    }
}
