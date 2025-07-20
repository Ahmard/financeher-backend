@extends('mails.layout')

@section('title', 'Your Login OTP')

@section('content')
    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; text-align: justify;">
        We have received a request to log in to your NINAuth account. To proceed, please use the following One-time Password (OTP):</p>
    <p style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; font-weight: 700; font-size: 28px; text-align: center; margin-bottom: 1.25rem;">{{$otp}}</p>
    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; text-align: justify;">
        This OTP is valid for the next {{$otpExpiry}}minutes. If you did not request this OTP, please disregard this email and ensure your account is secured.</p>

    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; ">
        For any assistance, feel free to reach out  to us at  <a href="mailto:support@ninauth.com"
                                                                 style="color: #00A86B; text-decoration: none; font-weight: 500;">{{ config('app.support_email') }}</a>
    </p>
@endsection
