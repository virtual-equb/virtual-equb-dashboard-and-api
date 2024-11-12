@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                @section('title')
                                {{ get_label('create_role', 'Create Role') }}
                                @endsection    

                                <div class="d-flex justify-content-between mb-2 mt-4">
                                    <div>
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb breadcrumb-style1">
                                                <li class="breadcrumb-item">
                                                    <a href="{{ url('/home') }}">{{ get_label('home', 'Home') }}</a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                    {{ get_label('settings', 'Settings') }}
                                                </li>
                                                <li class="breadcrumb-item active">
                                                    {{ get_label('create_role', 'Create Role') }}
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                    <div>
                                        <a href="{{ url('/roles') }}">
                                            <button type="button" class="btn btn-sm btn-secondary">
                                                Back to Roles
                                            </button>
                                        </a>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <form action="{{ route('roles.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="role_name" class="form-label">{{ get_label('role_name', 'Role Name') }}</label>
                                                <input type="text" name="name" id="role_name" class="form-control" required>
                                            </div>

                                            <h5>{{ get_label('permissions', 'Permissions') }}</h5>
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="form-check-input" id="permission_{{ $permission->name }}">
                                                            <label class="form-check-label" for="permission_{{ $permission->name }}">{{ str_replace('_', ' ', ucfirst($permission->name)) }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <button type="submit" class="btn btn-primary mt-3">{{ get_label('create_role', 'Create Role') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
