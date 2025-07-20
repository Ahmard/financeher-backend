@extends('mails.layout')

@section('title', sprintf('Welcome to %s!', config('app.name')))

@section('content')
    <p>Welcome to <strong>{{ config('app.name') }}</strong>! We're thrilled to have you on board.</p>

    <p>To get started, you can log in and explore your dashboard by clicking the button below:</p>

    <p>Need help getting started? Here are some resources to guide you:</p>
    <div><a href="#">User Guide</a></div>
    <div><a href="#">FAQs</a></div>
    <div><a href="#">Contact Support</a></div>

    <p>If you have any questions, feel free to reach out to us at {{ config('app.support_email') }}. We're always here to help!</p>

    <p>Thank you for joining us, and we hope you have a great time using {{ config('app.name') }}!</p>
@endsection
