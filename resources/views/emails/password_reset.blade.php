<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{__('manager.reset_your_password')}}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#333;">
  <div style="max-width:600px;margin:0 auto;padding:24px;background:#fff;border-radius:8px;">
    <div style="text-align:center;margin-bottom:16px;">
      <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="OrderMate" width="120" style="display:block;margin:0 auto 12px;">
      <h2 style="margin:0;color:#5C3D2E;">{{__('manager.reset_your_password')}}</h2>
    </div>

    <p>{{__('manager.hi')}} {{ $notifiable->name ?? '' }},</p>

    <p>{{__('manager.reset_password_message')}}</p>

    <div style="text-align:center;margin:24px 0;">
      <a href="{{ $url }}" style="display:inline-block;padding:12px 20px;background:#ff6600;color:#fff;text-decoration:none;border-radius:6px;">
        {{__('manager.reset_password')}}
      </a>
    </div>

    <p>{{__('manager.ignore_if_not_request')}}</p>

    <p style="margin-top:24px;">{{__('manager.regards')}}<br>OrderMate Team</p>

    <hr style="border:none;border-top:1px solid #eee;margin-top:18px;">
    <small style="color:#999;">{{__('manager.button_not_work')}}<br><a href="{{ $url }}">{{ $url }}</a></small>
  </div>
</body>
</html>