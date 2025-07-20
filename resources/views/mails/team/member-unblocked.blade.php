@extends('mails.layout')

@section('title', 'Account Unblocked Notification')

@section('content')
    <p>We're happy to inform you that your account associated with <strong>{{ $teamName }}</strong> has been successfully unblocked and restored to full functionality.</p>

    <p>You can now log in and continue using all the features and services available to you.</p>

    <p>If you have any questions or encounter any issues accessing your account, please don't hesitate to contact our support team:</p>

    <ul>
        <li>Email: <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a></li>
        <li>Phone: {{ config('app.support_phone') }}</li>
    </ul>

    <p>Thank you for your patience and cooperation during this process. We're glad to have you back!</p>

    <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
@endsection
