@extends('mails.layout')

@section('title', 'Account Blocked Notification')

@section('content')
    <p>We regret to inform you that your account associated with <strong>{{ $teamName }}</strong> has been temporarily blocked due to suspicious activity or a violation of our terms of service.</p>

    <p>If you believe this is a mistake, please contact our support team immediately to resolve the issue:</p>

    <ul>
        <li>Email: <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a></li>
        <li>Phone: {{ config('app.support_phone') }}</li>
    </ul>

    <p>Until the issue is resolved, you will not be able to access your account or perform any actions on the platform.</p>

    <p>For security purposes, we recommend that you:</p>

    <ul>
        <li>Review recent activity on your account.</li>
        <li>Ensure that your password and credentials have not been compromised.</li>
    </ul>

    <p>If you need assistance or have any questions, please don't hesitate to reach out to our support team. We're here to help resolve this as quickly as possible.</p>

    <p>Thank you for your understanding.</p>

    <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
@endsection
