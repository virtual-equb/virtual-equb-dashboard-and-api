@can('view role')
@extends('layouts.app')
@section('content')
{{-- @include('rolePermission.nav-links') --}}
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
                            <h4>Roles
                                <a href="{{ url('roles/create')}}" class="btn btn-primary float-right">Create Roles</a>
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
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id}}</td>
                                        <td>{{ $role->name}}</td>
                                        <td>{{ $role->guard_name}}</td>
                                        <td>{{ date('d-m-Y', strtotime($role->created_at))}}</td>
                                        <td>
                                            <a href="{{ url('roles/'.$role->id.'/assign-permission') }}" class="btn btn-info">
                                                Add / Edit Role Permission
                                            </a>
                                            <a href="{{ url('roles/'.$role->id.'/edit') }}" class="btn btn-success">Edit</a>
                                            <a href="{{ url('roles/'.$role->id.'/delete')}}" class="btn btn-danger">Delete</a>
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

