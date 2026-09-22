<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'EMAC') }} - OTP Verification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .container {
            max-width: 560px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 16px;
            color: #374151;
        }
        .message {
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 24px;
        }
        .otp-container {
            text-align: center;
            margin: 28px 0;
            padding: 20px;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
        }
        .otp-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #1e3a8a;
            font-family: 'Courier New', Courier, monospace;
        }
        .notice {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.5;
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 18px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'EMAC Management') }}</h1>
        </div>
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $user?->name ?? 'Valued Customer' }}</strong>,
            </div>
            
            <p class="message">
                @if($purpose->value === 'registration')
                    Thank you for signing up with {{ config('app.name', 'EMAC') }}. Please use the following One-Time Password (OTP) to verify your account.
                @elseif($purpose->value === 'forgot_password')
                    We received a request to reset your password. Use the OTP below to proceed with resetting your password.
                @else
                    Please use the following OTP to complete your verification process.
                @endif
            </p>

            <div class="otp-container">
                <div class="otp-label">Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p class="message" style="font-size: 14px; text-align: center; color: #64748b;">
                This code is valid for <strong>5 minutes</strong>. For security reasons, never share this code with anyone.
            </p>

            <div class="notice">
                If you did not request this verification, please ignore this email or contact our support immediately.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'EMAC') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
