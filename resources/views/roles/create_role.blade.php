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
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb breadcrumb-style1">
                                            <li class="breadcrumb-item">
                                                <a href="{{ url('/home') }}">{{ get_label('home', 'Home') }}</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                {{ get_label('settings', 'Settings') }}
                                            </li>
                                            <li class="breadcrumb-item active">
                                                {{ get_label('role', 'Role') }}
                                            </li>
                                        </ol>
                                    </nav>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="alert alert-primary alert-dismissible" role="alert">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#permission_instruction_modal">
                                                {{ get_label('click_for_permission_settings_instructions', 'Click Here for Permission Settings Instructions.') }}
                                            </a>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>

                                        <form action="{{ url('/roles/store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="redirect_url" value="/settings/permission">

                                            <div class="mb-3">
                                                <label for="name" class="form-label">{{ get_label('name', 'Name') }} <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" placeholder="{{ get_label('please_enter_role_name', 'Please enter role name') }}" id="name" name="name" required>
                                                @error('name')
                                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <hr class="mb-2" />

                                            <div class="table-responsive">
                                                <table class="table my-2 table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center" style="width: 50px;">
                                                                <div class="form-check">
                                                                    <input type="checkbox" id="selectAllColumnPermissions" class="form-check-input">
                                                                    <label class="form-check-label" for="selectAllColumnPermissions">{{ get_label('select_all', 'Select all') }}</label>
                                                                </div>
                                                            </th>
                                                            <th class="text-start fw-bold" style="font-size: 1.2em;">{{ get_label('permissions', 'Permissions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach(config("roles.permissions") as $module => $permissions)
                                                            <tr>
                                                                <td class="text-start">
                                                                    <div class="form-check">
                                                                        <input type="checkbox" id="selectRow{{ $module }}" class="form-check-input row-permission-checkbox" data-module="{{ $module }}">
                                                                        <label class="form-check-label fw-bold" for="selectRow{{ $module }}">{{ $module }}</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="row">
                                                                        @foreach($permissions as $permission)
                                                                            <?php $permissionModel = Permission::where('name', $permission)->where('guard_name', 'web')->first(); ?>
                                                                            <div class="col-3 mb-3">
                                                                                <div class="form-check">
                                                                                    <input type="checkbox" name="permissions[]" value="{{ optional($permissionModel)->id }}" class="form-check-input permission-checkbox" data-module="{{ $module }}">
                                                                                    <label class="form-check-label text-capitalize">{{ optional($permissionModel)->name ? str_replace('_', ' ', optional($permissionModel)->name) : '' }}</label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-2 d-flex justify-content-end">
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

        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = selectAllCheckbox.checked;
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            rowCheckboxes.forEach(rowCheckbox => {
                rowCheckbox.checked = isChecked;
            });
        });

        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                selectAllCheckbox.checked = Array.from(permissionCheckboxes).every(cb => cb.checked);
            });
        });

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