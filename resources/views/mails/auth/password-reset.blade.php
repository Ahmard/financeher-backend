@extends('mails.layout')

@section('title', 'Password Reset')

@section('content')
    <p>We received a request to reset the password for the {{ config('app.name') }} user associated with this email address. If you did not request to reset this password, you can ignore this request.</p>
    <p>Click the link below to reset your password:</p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 20px 0;">
        <tr>
            <td align="center" style="border-radius: 5px; text-align: center">
                <div class="button-container">
                    <a href="{{ $link }}" target="_blank">
                        Reset Password
                    </a>
                </div>
            </td>
        </tr>
    </table>

    <p>If you did not request a password reset, please ignore this email.</p>
@endsection
