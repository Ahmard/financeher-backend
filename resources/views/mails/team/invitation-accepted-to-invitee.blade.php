@extends('mails.layout')

@section('title', 'Your Invitation Has Been Accepted')

@section('content')
    <p>Good news! <strong></strong> has accepted your invitation to join the <strong>{{ $teamName }}</strong> team on {{ config('app.name') }}.</p>

    <p>You can now collaborate with them and work together towards your goals. Here are the details of the new team member:</p>

    <p>If you need further assistance or have any questions, feel free to reach out to us at {{ config('app.support_email') }}.</p>

    <p>Thanks for building your team with {{ config('app.name') }}. We wish you and your team continued success!</p>

    <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
@endsection
