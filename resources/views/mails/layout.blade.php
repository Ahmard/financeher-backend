<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Financeher</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #ffffff;
            padding: 30px 40px 20px;
            border-bottom: 1px solid #e5e5e5;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            margin-right: 12px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
        }

        .logo-text .finance {
            color: #1f2937;
        }

        .logo-text .her {
            color: #006A4B;
        }

        .content {
            padding: 40px;
            background-color: #ffffff;
        }

        .greeting {
            font-size: 16px;
            color: #374151;
            margin-bottom: 24px;
        }

        .main-text {
            font-size: 16px;
            color: #374151;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .instruction-text {
            font-size: 16px;
            color: #374151;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .verification-code {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            margin: 24px 0;
        }

        .alternative-text {
            font-size: 16px;
            color: #374151;
            margin: 24px 0;
        }

        .verify-button {
            display: inline-block;
            background-color: #006A4B;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 28px;
            border-radius: 100px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: background-color 0.2s;
        }

        .verify-button:hover {
            background-color: #059669;
        }

        .expiry-text {
            font-size: 16px;
            color: #374151;
            margin: 24px 0;
        }

        .thank-you {
            font-size: 16px;
            color: #374151;
            margin-top: 24px;
        }

        .footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            border-top: 1px solid #e5e5e5;
        }

        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .social-links {
            display: flex;
            gap: 16px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background-color: #006A4B;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .social-link:hover {
            background-color: #059669;
        }

        .social-icon {
            width: 20px;
            height: 20px;
            fill: #ffffff;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .content, .header, .footer {
                padding: 20px;
            }

            .logo-text {
                font-size: 20px;
            }

            .verification-code {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <div class="logo-icon">
                <img alt="Financeher" src="{{frontend('images/financeher-green.png')}}">
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-text">
            @yield('footer')
        </div>

        <div class="social-links">
            <!-- Twitter -->
            <a href="#" class="social-link">
                <svg class="social-icon" viewBox="0 0 24 24">
                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                </svg>
            </a>

            <!-- Facebook -->
            <a href="#" class="social-link">
                <svg class="social-icon" viewBox="0 0 24 24">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                </svg>
            </a>

            <!-- LinkedIn -->
            <a href="#" class="social-link">
                <svg class="social-icon" viewBox="0 0 24 24">
                    <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
                    <circle cx="4" cy="4" r="2"/>
                </svg>
            </a>
        </div>
    </div>
</div>
</body>
</html>