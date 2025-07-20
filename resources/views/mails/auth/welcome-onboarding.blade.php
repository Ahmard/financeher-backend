@extends('mails.layout')

@section('title', 'Verify your account')

@section('content')
    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; text-align: justify;">
        Thank you for signing up for a {{config('app.name')}} account! To complete your registration and start using our services,
        please confirm your email address by clicking the button below:
    </p>

    <div class="button-container" style="margin-bottom: 16px;">
        <a href="{{$verificationLink}}" class="verify-link">
            Verify your email address
        </a>
    </div>

    <p
            style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; line-height: 1.75; margin-bottom: 1.25rem; ">

        If you did not initiate this registration, kindly disregard this message.
    </p>
@endsection
