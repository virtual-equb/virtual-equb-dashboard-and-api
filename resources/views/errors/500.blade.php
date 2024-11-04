@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="container text-center">
    <h1 class="display-1">500</h1>
    <h2>Internal Server Error</h2>
    <p>Oops! Something went wrong on our end. Please try again later.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
</div>
@endsection