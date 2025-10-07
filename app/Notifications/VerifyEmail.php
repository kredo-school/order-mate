<?php

namespace App\Notifications;

// ⭐ すべての USE 文をここに集約・整理する ⭐
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail; 
use Illuminate\Support\Facades\Log;     // Log::info() のため
use Illuminate\Support\Facades\View;    // View::make()->render() のため
use App\Mail\VerifyEmailMailable; // ← 自分で作るメールクラスを読み込む



class VerifyEmail extends BaseVerifyEmail
{
    use Queueable;


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array // ⭐ 修正: object を削除 ⭐
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        Log::info('Custom VerifyEmail Notification triggered.');

        // 認証URLを作る
        $verificationUrl = $this->verificationUrl($notifiable);

        // 👇 Mailableを呼び出す！（MailMessageはもう使わない）
        return (new VerifyEmailMailable($notifiable, $verificationUrl))
            ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
