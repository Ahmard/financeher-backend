@extends('mails.layout')

@section('title', sprintf('Welcome to %s', config('app.name')))

@section('content')
    <p>Your email has been successfully verified, and your account is now active.</p>

    <p>Here are a few resources to help you make the most of your new account:</p>

    <ul>
        <li><a href="{{ $dashboardLink }}" target="_blank">Access Your Dashboard</a> to manage your account and get started.</li>
        <li><a href="{{ $helpCenterLink }}" target="_blank">Help Center</a> for tips, tutorials, and support.</li>
        <li>Contact our support team at <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>.</li>
    </ul>
@endsection
