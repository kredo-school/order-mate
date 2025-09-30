<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reset your password</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#333;">
  <div style="max-width:600px;margin:0 auto;padding:24px;background:#fff;border-radius:8px;">
    <div style="text-align:center;margin-bottom:16px;">
      <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="OrderMate" width="120" style="display:block;margin:0 auto 12px;">
      <h2 style="margin:0;color:#5C3D2E;">Reset your password</h2>
    </div>

    <p>Hello {{ $notifiable->name ?? '' }},</p>

    <p>We received a request to reset your password. Click the button below to choose a new password. This link will expire in 60 minutes.</p>

    <div style="text-align:center;margin:24px 0;">
      <a href="{{ $url }}" style="display:inline-block;padding:12px 20px;background:#ff6600;color:#fff;text-decoration:none;border-radius:6px;">
        Reset Password
      </a>
    </div>

    <p>If you did not request a password reset, you can safely ignore this email — no changes will be made to your account.</p>

    <p style="margin-top:24px;">Regards,<br>OrderMate Team</p>

    <hr style="border:none;border-top:1px solid #eee;margin-top:18px;">
    <small style="color:#999;">If the button doesn't work, copy and paste the following URL into your browser:<br><a href="{{ $url }}">{{ $url }}</a></small>
  </div>
</body>
</html>