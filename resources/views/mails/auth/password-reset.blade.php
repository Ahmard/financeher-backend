@extends('mails.layout')

@section('content')
    <div class="greeting">Hello {{$user->fullName()}},</div>

    <div class="main-text">
        You recently asked to reset your password for your Financeher account.
        Please click here to log in and set a new password for your account:
    </div>

    <a href="{{ $link }}" class="verify-button">Reset Password</a>

    <div class="expiry-text">{{ $link }}</div>

    <div class="main-text">
        If you’re having trouble clicking the Reset Password button,
        copy and paste the URL below into your browser.
    </div>

    <div class="thank-you">Thank you.</div>
@endsection

@section('footer')
    If you did not request a password reset, please ignore this email.
@endsection