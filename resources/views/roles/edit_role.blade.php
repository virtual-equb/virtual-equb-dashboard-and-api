@extends('layouts.app')

@section('title')
    {{ get_label('update_role', 'Update role') }}
@endsection

<?php use Spatie\Permission\Models\Permission; ?>

@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
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
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/settings/permission') }}">{{ get_label('permissions', 'Permissions') }}</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    {{ get_label('update_role', 'Update role') }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-primary alert-dismissible" role="alert">
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#permission_instuction_modal">
                                {{ get_label('click_for_permission_settings_instructions', 'Click Here for Permission Settings Instructions.') }}
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <form action="{{ url('/roles/update/' . $role->id) }}" class="form-submit-event" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="redirect_url" value="/settings/permission">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        {{ get_label('name', 'Name') }} <span class="asterisk">*</span>
                                    </label>
                                    <input class="form-control" type="text" placeholder="{{ get_label('please_enter_role_name', 'Please enter role name') }}" id="name" name="name" value="{{ $role->name }}" required>
                                    @error('name')
                                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ get_label('data_access', 'Data Access') }} 
                                        (<small class="text-muted mt-2">{{ get_label('all_data_access_info', 'If All Data Access Is Selected, Users Under This Role Will Have Unrestricted Access to All Data, Irrespective of Any Specific Assignments or Restrictions') }}</small>)
                                    </label>
                                    <div class="btn-group d-flex justify-content-center" role="group">
                                        <input type="radio" class="btn-check" name="permissions[]" id="access_all_data" value="{{ optional(Permission::where('name', 'access_all_data')->where('guard_name', 'web')->first())->id }}" {{ $role_permissions->contains('name', 'access_all_data') ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="access_all_data">{{ get_label('all_data_access', 'All Data Access') }}</label>

                                        <input type="radio" class="btn-check" name="permissions[]" id="access_allocated_data" value="0" {{ !$role_permissions->contains('name', 'access_all_data') ? 'checked' : '' }}>
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
                                                    <label class="form-check-label" for="selectAllColumnPermissions">{{ get_label('select_all', 'Select all') }}</label>
                                                </div>
                                            </th>
                                            <th>{{ get_label('permissions', 'Permissions') }}</th>
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
                                                            <div class="form-check mx-4">
                                                                <?php $permissionModel = Permission::where('name', $permission)->where('guard_name', 'web')->first(); ?>
                                                                <input type="checkbox" name="permissions[]" value="{{ optional($permissionModel)->id }}" class="form-check-input permission-checkbox" data-module="{{ $module }}" {{ $role_permissions->contains('name', $permission) ? 'checked' : '' }}>
                                                                <label class="form-check-label text-capitalize">{{ optional($permissionModel)->name ? substr($permissionModel->name, 0, strpos($permissionModel->name, "_")) : '' }}</label>
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
                                <button type="submit" class="btn btn-primary me-2" id="submit_btn">{{ get_label('update', 'Update') }}</button>
                                <button type="reset" class="btn btn-outline-secondary">{{ get_label('cancel', 'Cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@section('scripts')
<script>
    document.getElementById('selectAllColumnPermissions').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.checked = checked;
        });
    });
</script>
@endsection
