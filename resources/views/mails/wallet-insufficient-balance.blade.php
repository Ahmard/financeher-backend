@extends('mails.layout')

@section('title', 'Insufficient Wallet Balance')

@section('content')
    <p>We noticed that {{ $user['first_name'] }} attempted to perform an operation but currently has insufficient units in the account to complete the request.</p>

    <p>To proceed with normal operations and avoid any service disruptions, we recommend purchasing additional units for your account.</p>

    <p>To purchase more units, please follow these steps:</p>

    <ol>
        <li>Log in to your dashboard.</li>
        <li>Go to the "Wallet" or "Account" section.</li>
        <li>Select the number of units you’d like to purchase and complete the payment process.</li>
    </ol>

    <p>If you need any assistance with purchasing units or have questions, please reach out to our support team:</p>

    <ul>
        <li>Email: <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a></li>
        <li>Phone: {{ config('app.support_phone') }}</li>
    </ul>

    <p>We encourage you to top up your account as soon as possible to continue uninterrupted NIN verifications.</p>

    <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
@endsection
