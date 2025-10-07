{{-- resources/views/emails/verify.blade.php --}}
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Email Verification Required</title>
</head>

<body
    style="margin:0;padding:0;background:#f6f6f6;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#3b3b3b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="min-width:320px;">
        <tr>
            <td class="align-center" style="padding:32px 16px;">
                <table role="presentation" width="680" cellpadding="0" cellspacing="0"
                    style="max-width:680px;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
                    
                    {{-- Header --}}
                    <tr>
                        <td
                            style="padding:24px 28px 0;text-align:center;background:linear-gradient(180deg,#fff6ea,#fff);">
                            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="OrderMate"
                                style="display:block; margin:0 auto 12px;">
                            <h1 style="margin:0;font-size:22px;color:#5C3D2E;">Email Verification Required</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:24px 28px 8px;">
                            <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">
                                Hi {{ $user->name ?? 'there' }},<br>
                            </p>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#4b4b4b;">
                                Thank you for registering with OrderMate. Before you can log in, please confirm your email address by clicking the button below. This ensures the security of your account.
                            </p>

                            {{-- ⭐ CTA: 認証リンクを使用し、デザインカラーを適用 ⭐ --}}
                            <div style="text-align:center;margin:18px 0;">
                                <a href="{{ $verificationUrl }}"
                                    style="display:inline-block;padding:12px 22px;border-radius:8px;background:#FF7A18;color:#fff;text-decoration:none;font-weight:600;">
                                    Verify Email Address
                                </a>
                            </div>

                            <p style="margin:10px 0 0;font-size:13px;color:#888;line-height:1.5;">
                                If you did not create an account, no further action is required.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:18px 28px 28px;background:#fffaf0;text-align:center;">
                            <p style="margin:0;font-size:13px;color:#7a5b3d;">
                                Cheers,<br>
                                <strong>The OrderMate Team</strong>
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;color:#a88f6a;">
                                &copy; {{ date('Y') }} OrderMate — making restaurant life simpler
                            </p>
                        </td>
                    </tr>
                </table>

                {{-- small note under card --}}
                <div style="max-width:680px;margin:12px auto 0;text-align:center;color:#9b9b9b;font-size:12px;">
                    You can change notification preferences in your account settings.
                </div>
            </td>
        </tr>
    </table>
</body>
</html>