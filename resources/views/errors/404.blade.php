@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="container text-center">
    <h1 class="display-1">404</h1>
    <h2>Oops! Page Not Found</h2>
    <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
    <br>
    <br>
    <p><a href="{{ url()->previous() }}">Go Back</a></p>
</div>
@endsection