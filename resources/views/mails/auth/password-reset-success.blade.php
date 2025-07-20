@extends('mails.layout')

@section('title', 'Changed Password')

@section('content')
    <p style="margin-bottom: 15px; font-size: 16px;">
        This is to confirm that your password has been changed!<br/>
    </p>
@endsection

@section('content-second')
    <div style="margin-top: -30px">
        We received a request to change your {{ config('app.name') }} password. The change was successful.
    </div>
    <div style="margin-top: 10px">
        Didn't request this change? Contact us at <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>
        immediately so that we can ensure your {{ config('app.name') }} account remains completely secure.
    </div>
@endsection
