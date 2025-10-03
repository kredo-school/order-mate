<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inquiry Received</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color:#333; background-color:#f8f8f8; padding: 20px 0;">
    <div style="max-width:600px;margin:0 auto;padding:24px;background:#fff;border-radius:8px;">

        <div style="text-align:center;margin-bottom:24px;">
            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="OrderMate" width="120"
                style="display:block;margin:0 auto 12px;">
            <h2 style="margin:0;color:#5C3D2E;">Inquiry Received</h2>
        </div>

        <p style="margin-bottom: 24px;">Hello {{ $data['name'] }} {{ $data['last_name'] }},</p>

        <p>Thank you for reaching out to **OrderMate**. We have successfully received your inquiry and will review the
            details shortly.</p>

        <p>A dedicated member of our team will get back to you as soon as possible, typically within **2 business
            days**.</p>

        <hr style="border:none;border-top:1px solid #eee;margin:24px 0;">

        <h3 style="color:#E67E22; margin-top:0; font-size: 18px;">Your Submitted Details</h3>

        <div style="padding: 15px; background-color: #FDF6EB; border: 1px solid #F0E6D2; border-radius: 6px;">
            <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; line-height: 1.6;">
                <tr>
                    <td style="font-weight: bold; width: 150px; padding: 4px 0;">Your Name:</td>
                    <td style="padding: 4px 0;">{{ $data['name'] }} {{ $data['last_name'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">Restaurant Name:</td>
                    <td style="padding: 4px 0;">{{ $data['store_name'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">Email Address:</td>
                    <td style="padding: 4px 0;">{{ $data['email'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">Phone Number:</td>
                    <td style="padding: 4px 0;">{{ $data['phone'] ?? 'N/A' }}</td>
                </tr>
            </table>
            <p style="margin-top: 15px; margin-bottom: 0; font-weight: bold;">Your Message:</p>
            <p style="white-space: pre-wrap; margin-top: 5px;">{{ $data['message'] }}</p>
        </div>

        <p style="margin-top:30px;">This is an automated confirmation email. Please reply to this email if you have any
            immediate follow-up questions.</p>

        <p style="margin-top:24px;">Best regards,<br>OrderMate Team</p>

    </div>
</body>

</html>
