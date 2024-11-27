@can('view permission')
@extends('layouts.app')
@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success mt-3">{{ session('status') }}</div>
                    @endif
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Permissions
                                <a href="{{ url('permission/create')}}" class="btn btn-primary float-right">Create Permission</a>
                                <a href="{{ route('user')}}" class="btn btn-danger float-right mr-3">Back</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Guard Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id}}</td>
                                        <td>{{ $permission->name}}</td>
                                        <td>{{ $permission->guard_name}}</td>
                                        <td>
                                            <a href="{{ url('permission/'.$permission->id.'/edit') }}" class="btn btn-success">Edit</a>
                                            <a href="{{ url('permission/'.$permission->id.'/delete')}}" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@endcan


