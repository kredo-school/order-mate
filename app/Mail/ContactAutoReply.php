<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoReply extends Mailable
{
    use Queueable, SerializesModels;

    // ⭐ フォームデータを受け取るためのプロパティ ⭐
    public $formData;

    public function __construct(array $formData)
    {
        // ⭐ コントローラーから渡されたデータを保存 ⭐
        $this->formData = $formData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // ⭐ 宛先をフォームに入力されたメールアドレスに設定 ⭐
            to: $this->formData['email'], 
            subject: '【Ordermate】Inquiry Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            // ⭐ お客様向けテンプレートを指定 ⭐
            view: 'emails.autoreply', 
            // データをビューに渡す
            with: [
                'name' => $this->formData['name'],
                'data' => $this->formData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}