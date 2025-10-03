<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * ⭐ 修正点 1: フォームデータを受け取るためのプロパティを追加 ⭐
     */
    public $formData;

    public function __construct(array $formData)
    {
        // ⭐ 修正点 2: フォームデータを受け取る ⭐
        $this->formData = $formData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // ⭐ 修正点 3: 宛先を管理者2名に設定 ⭐
            to: [
                'ordermate.official@gmail.com', // 1人目の管理者アドレス
            ],
            subject: '【Ordermate LP】URGENT: New Inquiry Received!', // 件名
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // ⭐ 修正点 4: 管理者向けのビューを指定 ⭐
            view: 'emails.contact',
            // ビューにデータを渡す
            with: [
                'data' => $this->formData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}