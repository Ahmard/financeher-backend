@extends('mails.layout')

@section('content')
    <div class="greeting">Hi there,</div>

    <div class="main-text">
        Thank you for joining Financeher, your gateway to AI-powered business opportunities and funding support.
    </div>

    <div class="instruction-text">
        Please use the 6-digit code below to verify your email address and activate your account:
    </div>

    <div class="verification-code">[ {{ $user['email_verification_code'] }} ]</div>

    <div class="alternative-text">Or click the button below to verify automatically:</div>

    <a href="{{ $verificationLink }}" class="verify-button">Verify My Email</a>

    <div class="expiry-text">This code will expire in 10 minutes.</div>

    <div class="thank-you">Thank you.</div>
@endsection

@section('footer')
    If you didn't try to log in, you can safely ignore this email.
@endsection