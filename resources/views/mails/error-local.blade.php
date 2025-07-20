@extends('mails.layout')

@section('title', 'Error Report')

@section('content')
    <p><strong>Error Report</strong></p>

    <p><strong>Exception Details:</strong></p>
    <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $exception->getMessage() }}</pre>
    <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $exception->getTraceAsString() }}</pre>

    <p><strong>Request Information:</strong></p>
    <ul style="list-style-type: none; padding: 0;">
        <li><strong>URL:</strong> {{ $request->url() }}</li>
        <li><strong>Method:</strong> {{ $request->method() }}</li>
        <li><strong>User-Agent:</strong> {{ $request->header('User-Agent') }}</li>
        <li><strong>IP Address:</strong> {{ $request->ip() }}</li>
        <li><strong>Query Parameters:</strong> {{ json_encode($request->query()) }}</li>
        <li><strong>Form Data:</strong> {{ json_encode($request->all()) }}</li>
    </ul>

    <p><strong>Additional Context:</strong></p>
    <p>{{ $exception->getFile() }} on line {{ $exception->getLine() }}</p>

    <p>This error was logged on {{ now()->toDateTimeString() }}.</p>
@endsection
