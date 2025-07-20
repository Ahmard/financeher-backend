@extends('mails.layout')

@section('title', 'Accept Your Invitation')

@section('content')
    <p>
        You've been invited to collaborate on the {{ config('app.name') }}. Note that this link will expire in 15 minutes.
    </p>

    <div class="button-container">
        <a href="{{$invitationLink}}">Verify Email</a>
    </div>

    <p class="footer-thanks">
        If you have any question contact us at <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>.
    </p>
@endsection
