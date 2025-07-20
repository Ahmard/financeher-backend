<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{config('app.name')}}</title>
    <style>
        body {
            font-family: Matter, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        @font-face {
            font-family: Matter;
            src: url({{app_url('mail/Matter-Light.ttf')}});
            font-weight: 300;
        }

        @font-face {
            font-family: Matter;
            src: url({{app_url('mail/Matter-Regular.ttf')}});
            font-weight: 400;
        }

        @font-face {
            font-family: Matter;
            src: url({{app_url('mail/Matter-Medium.ttf')}});
            font-weight: 500;
        }

        @font-face {
            font-family: Matter;
            src: url({{app_url('mail/Matter-SemiBold.ttf')}});
            font-weight: 600;
        }

        @font-face {
            font-family: Matter;
            src: url({{app_url('mail/Matter-Bold.ttf')}});
            font-weight: 700;
        }

        @font-face {
            font-family: Matter;
            src: url({{app_url('mail/Matter-Heavy.ttf')}});
            font-weight: 800;
        }


        .email-container {
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #556b2f;
            background-color: #ffffff;
            padding: 20px;
            box-sizing: border-box;
        }

        .header {
            text-align: left;
            margin-bottom: 20px;
            margin-top: 10px;
        }

        .header img {
            width: 250px;
            height: auto;
        }

        .headerContainer {
            text-align: center;
            max-width: 436px;
            margin: 0 auto;
            padding-bottom: 8px;
            border-bottom: 1px solid #615d5c;
        }

        .header h1 {
            font-size: 32px;
            color: #615d5c;
            margin: 24px 0 0;
            letter-spacing: -3%;
        }

        .content {
            text-align: left;
            color: #555555;
            margin-bottom: 60px;
        }

        .content p {
            margin: 24px 0;
            font-size: 15px;
            font-weight: 400;
            line-height: 140%;
            letter-spacing: 0%;
        }

        .button-container {
            text-align: center;
            margin-bottom: 36px;
        }

        .button-container a {
            display: inline-block;
            padding: 8px 48px;
            background-color: #556b2f;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
        }

        .icon {
            text-align: center;
            margin-top: 56px;
            margin-bottom: 36px;
        }

        .icon img {
            width: 150px;
            height: auto;
        }

        .how-to-start {
            text-align: left;
            line-height: 192%;
        }

        .list-subtitle {
            font-weight: 700;
        }

        a {
            color: #556b2f;
        }

        .support-link {
            color: var(--primary-color);
        }

        .footer {
            text-align: center;
            color: #777777;
            font-size: 15px;
            font-weight: 400;
            padding: 24px 0px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
<div class="email-container">
    <div class="header">
        <div style="text-align: center!important;">
            <img style="width: 150px; height: 150px" src="{{frontend('images/logo.png')}}" alt="{{config('app.name')}}">
        </div>
        <div class="headerContainer">
            <h1>@yield('title')</h1>
        </div>
    </div>
    <div class="content">
        @yield('content')
    </div>


    <div class="content">
        @yield('content-second')
    </div>
    <div class="footer">
        <p>This email was sent from an unmonitored mailbox. For any inquiries or assistance, please contact our
            support team directly.</p>
    </div>
</div>
</body>

</html>