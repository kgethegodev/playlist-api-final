<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
        }
        .header img {
            width: 100px;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #aaaaaa;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="YOUR_LOGO_URL" alt="{{ config('app.name') }} Logo">
    </div>
    <div class="content">
        <h1>Verify Your Account</h1>
        <p>Hello {{ $user->name }},</p>
        <p>Thank you for signing up for {{ config('app.name') }}! Please click the button below to verify your email address and complete your registration.</p>
        <p><a href="{{ $verification_url }}" class="button">Verify Email Address</a></p>
        <p>If you did not create an account, no further action is required.</p>
        <p>Thank you,<br>The {{ config('app.name') }} team.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
