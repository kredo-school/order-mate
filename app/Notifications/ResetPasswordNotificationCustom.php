<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotificationCustom extends Notification
{
    use Queueable;

    /** @var string */
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * 配信チャネル
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * メール表現（Blade ビューを使う）
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your OrderMate password')
            ->view('emails.password_reset', [
                'url' => $url,
                'notifiable' => $notifiable,
            ]);
    }
}
