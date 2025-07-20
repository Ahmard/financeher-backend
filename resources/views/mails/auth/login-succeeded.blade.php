@extends('mails.layout')

@section('title', 'Login Confirmation')

@section('content')
    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; text-align: justify;">
        Your {{ config('app.name') }} was recently accessed at {{date('Y-m-d H:i:s')}} from IP Address {{$ipAddress}}.
    </p>

    Actions:

    <ul>
        <li>No need to take any actions if this was initiated by you.</li>
        <li>If you did not initiate any action, your account may have been comprised! Please log into your account and
            reset your password now.
        </li>
    </ul>

    Alternatively, you can contact us at
    <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>
    to help secure your account.
@endsection
