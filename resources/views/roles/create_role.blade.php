@extends('layouts.app')

@section('title')
    {{ get_label('create_role', 'Create Role') }}
@endsection

<?php
use Spatie\Permission\Models\Permission;
?>

@section('content')
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
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
                                                    {{ get_label('permissions', 'Permissions') }}
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                    <div>
                                        <a href="{{ url('/roles/create') }}">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="left" 
                                                    title="{{ get_label('create_role', 'Create Role') }}">
                                                Create Role <i class='bx bx-plus'></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if (session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        <div class="alert alert-primary alert-dismissible" role="alert">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#permission_instruction_modal">
                                                {{ get_label('click_for_permission_settings_instructions', 'Click Here for Permission Settings Instructions.') }}
                                            </a>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>

                                        <form action="{{ url('/roles/store') }}" class="form-submit-event" method="POST">
                                            @csrf
                                            <input type="hidden" name="redirect_url" value="/settings/permission">
                                            
                                            <div class="row">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">{{ get_label('name', 'Name') }} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" 
                                                           placeholder="{{ get_label('please_enter_role_name', 'Please enter role name') }}" 
                                                           id="name" name="name" required>
                                                    @error('name')
                                                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        {{ get_label('data_access', 'Data Access') }} 
                                                        <small class="text-muted mt-2">
                                                            {{ get_label('all_data_access_info', 'If All Data Access Is Selected, Users Under This Role Will Have Unrestricted Access to All Data, Irrespective of Any Specific Assignments or Restrictions') }}
                                                        </small>
                                                    </label>
                                                    <div class="btn-group d-flex justify-content-center" role="group" aria-label="Basic radio toggle button group">
                                                        @php
                                                            $accessAllDataPermission = Permission::where('name', 'access_all_data')->where('guard_name', 'web')->first();
                                                        @endphp
                                                        <input type="radio" class="btn-check" name="permissions[]" id="access_all_data" value="{{ optional($accessAllDataPermission)->id }}">
                                                        <label class="btn btn-outline-primary" for="access_all_data">{{ get_label('all_data_access', 'All Data Access') }}</label>

                                                        <input type="radio" class="btn-check" name="permissions[]" id="access_allocated_data" value="0" checked>
                                                        <label class="btn btn-outline-primary" for="access_allocated_data">{{ get_label('allocated_data_access', 'Allocated Data Access') }}</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="mb-2" />

                                            <div class="table-responsive text-nowrap">
    <table class="table my-2">
        <thead>
            <tr>
                <th>
                    <div class="form-check">
                        <input type="checkbox" id="selectAllColumnPermissions" class="form-check-input">
                        <label class="form-check-label" for="selectAllColumnPermissions">{{ get_label('select_all', 'Select All') }}</label>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach(config("taskhub.permissions") as $module => $permissions)
            <tr>
                <td>
                    <div class="form-check">
                        <input type="checkbox" id="selectRow{{ $module }}" class="form-check-input row-permission-checkbox" data-module="{{ $module }}">
                        <label class="form-check-label" for="selectRow{{ $module }}">{{ $module }}</label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="d-flex flex-wrap justify-content-between">
                        @foreach($permissions as $permission)
                        @php
                            $permissionModel = Permission::findByName($permission, 'web'); // Specify guard here
                        @endphp
                        <div class="form-check mx-4">
                            <input type="checkbox" name="permissions[]" value="{{ optional($permissionModel)->id }}" class="form-check-input permission-checkbox" data-module="{{ $module }}" {{ $permissionModel ? '' : 'disabled' }}>
                            <label class="form-check-label text-capitalize">{{ substr($permission, 0, strpos($permission, "_")) }}</label>
                        </div>
                        @endforeach
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

                                            <div class="mt-2">
                                                <button type="submit" class="btn btn-primary me-2" id="submit_btn">{{ get_label('create', 'Create') }}</button>
                                                <button type="reset" class="btn btn-outline-secondary">{{ get_label('cancel', 'Cancel') }}</button>
                                            </div>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAllColumnPermissions');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-permission-checkbox');

        // Select/Deselect all permission checkboxes
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = selectAllCheckbox.checked;
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            rowCheckboxes.forEach(rowCheckbox => {
                rowCheckbox.checked = isChecked;
            });
        });

        // Update "Select All" checkbox based on individual checkboxes
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                selectAllCheckbox.checked = Array.from(permissionCheckboxes).every(cb => cb.checked);
            });
        });

        // Handle row checkbox clicks to select/deselect all permissions in that row
        rowCheckboxes.forEach(rowCheckbox => {
            rowCheckbox.addEventListener('change', function () {
                const permissionsInRow = rowCheckbox.closest('tr').querySelectorAll('.permission-checkbox');
                permissionsInRow.forEach(permissionCheckbox => {
                    permissionCheckbox.checked = rowCheckbox.checked;
                });
            });
        });
    });
</script>
@endsection

@endsection