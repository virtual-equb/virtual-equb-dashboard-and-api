@extends('layouts.app')


@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <div class="container">
            <div class="row mt-5">
                <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4>({{ $role->guard_name }}) Role : {{ $role->name }}
                                <a href="{{ url('roles')}}" class="btn btn-danger float-right"> Back</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('roles/'.$role->id.'/assign-permission')}}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    @error('permission')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <label for="">Permissions</label>
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-3">
                                                <label for="">
                                                    <input type="checkbox" name="permission[]" value="{{ $permission->name }}" {{ in_array($permission->id, $rolePermissions) ? 'checked': ''}}/>
                                                    {{ $permission->name }} ({{ $permission->guard_name }})
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


