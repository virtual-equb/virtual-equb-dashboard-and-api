@extends('layouts.app')

@section('title', 'Forbidden')

@section('content')
<div class="container text-center">
    <h1 class="display-1">403</h1>
    <h2>Access Denied</h2>
    <p>You do not have permission to access this page. Please contact your administrator if you believe this is an error.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Go to Home</a>
</div>
@endsection