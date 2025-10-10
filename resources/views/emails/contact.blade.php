<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>新規お問い合わせ</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; background-color:#f8f8f8; padding: 20px 0;">
    <div style="max-width:600px;margin:0 auto;padding:24px;background:#fff;border-radius:8px;">

        <div style="text-align:center;margin-bottom:24px;">
            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="OrderMate" width="120"
                style="display:block;margin:0 auto 12px;">
            <h2 style="margin:0;color:#C0392B;">新規お問い合わせがありました！</h2>
        </div>

        <p style="font-size: 16px; margin-bottom: 24px; font-weight: bold;">
            Ordermate LPから新しいお問い合わせがありました。迅速にご確認ください。
        </p>

        <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">

        <h3 style="color:#E67E22; margin-top:0; font-size: 18px;">お問い合わせ詳細</h3>

        <div style="padding: 15px; background-color: #FDF6EB; border: 1px solid #F0E6D2; border-radius: 6px;">
            <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; line-height: 1.6;">
                <tr>
                    <td style="font-weight: bold; width: 150px; padding: 4px 0;">顧客名:</td>
                    <td style="padding: 4px 0;">{{ $data['name'] }} {{ $data['last_name'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">レストラン名:</td>
                    <td style="padding: 4px 0;">{{ $data['store_name'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">メールアドレス:</td>
                    <td style="padding: 4px 0;">{{ $data['email'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">電話番号:</td>
                    <td style="padding: 4px 0;">{{ $data['phone'] ?? 'N/A' }}</td>
                </tr>
            </table>

            <p style="margin-top: 15px; margin-bottom: 0; font-weight: bold; color: #5C3D2E;">お問い合わせ内容:</p>
            <p style="white-space: pre-wrap; margin-top: 5px;">{{ $data['message'] }}</p>
        </div>

        <p style="margin-top:30px;">This lead data has also been logged to Google Sheets via Webhook.</p>

        <p style="margin-top:24px;">Regards,<br>OrderMate System</p>

    </div>
</body>

</html>
