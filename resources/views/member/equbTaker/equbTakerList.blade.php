@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $title }} - Equb Taker List</h1>

    @if ($equbTakers->isEmpty())
        <p>No Equb Takers found.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Equb</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equbTakers as $equbTaker)
                    <tr>
                        <td>{{ $equbTaker->id }}</td>
                        <td>{{ $equbTaker->name }}</td>
                        <td>{{ $equbTaker->equb->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection