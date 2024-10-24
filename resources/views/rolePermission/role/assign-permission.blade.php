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
                            <h4>Role: {{ $role->name }}
                                <a href="{{ url('roles')}}" class="btn btn-danger float-right">Back</a>
                                <a href="{{ route('user')}}" class="btn btn-primary float-right mr-3">Users</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('roles/'.$role->id.'/assign-permission')}}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Permissions Section -->
                                <div class="mb-3">
                                    @error('permission')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <label for="">Permissions</label>

                                    <!-- Check All Checkbox -->
                                    <div class="mb-2">
                                        <input type="checkbox" id="checkAllPermissions">
                                        <label for="checkAllPermissions"><strong>Check All</strong></label>
                                    </div>

                                    <!-- List of Permissions with checkboxes -->
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-3">
                                                <label for="{{ 'permission_'.$permission->id }}">
                                                    <!-- Ensure each permission checkbox has a unique ID based on its name -->
                                                    <input type="checkbox" class="permission-checkbox" name="permission[]" value="{{ $permission->name }}" id="{{ 'permission_'.$permission->id }}" {{ in_array($permission->id, $rolePermissions) ? 'checked': '' }}/>
                                                    {{ $permission->name }} 
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

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Handle the "Check All" functionality
        $('#checkAllPermissions').click(function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Handle individual permission checkboxes
        $('.permission-checkbox').click(function() {
            if (!$(this).prop('checked')) {
                $('#checkAllPermissions').prop('checked', false);
            }

            if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
                $('#checkAllPermissions').prop('checked', true);
            }
        });
    });
</script>
@endsection
