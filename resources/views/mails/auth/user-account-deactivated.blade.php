@extends('mails.layout')

@section('title', 'Account Deactivation Notice')

@section('content')
    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; text-align: justify;">
        Your {{ config('app.name') }} account has been deactivated due to multiple failed login attempts.
    </p>

    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; text-align: justify;">
        For security reasons, we have temporarily disabled access to your account to prevent unauthorized access.
    </p>

    Actions:

    <ul>
        <li>If you believe this was a mistake, please reset your password to regain access.</li>
        <li>If you need further assistance, contact our support team.</li>
    </ul>

    You can reach us at
    <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>
    for further assistance.
@endsection
