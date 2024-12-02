@can('view permission')
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
                                {{ get_label('permission_settings', 'Permission settings') }}
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
                                                    {{ get_label('permissions', 'Permissions') }}
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                    <div>
                                        <a href="{{ url('/roles/create') }}">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="{{ get_label('create_role', 'Create role') }}">
                                                Create Role<i class='bx bx-plus'></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ get_label('role', 'Role') }}</th>
                                                        <th>{{ get_label('permissions', 'Permissions') }}</th>
                                                        <th>{{ get_label('actions', 'Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($roles as $role)
                                                    <tr>
                                                    <td>
    <h4 class="text-capitalize fw-bold mb-0">{{ get_label($role->name, ucwords(str_replace('_', ' ', $role->name))) }}</h4>
</td>

                                                        @if($role->name == 'admin')
                                                        <td>
                                                            <span class="badge bg-success">{{ get_label('admin_has_all_permissions', 'Admin has all the permissions') }}</span>
                                                        </td>
                                                        <td>-</td> <!-- Display dash for actions -->
                                                        @else
                                                        <?php $permissions = $role->permissions; ?>
                                                        @if(count($permissions) != 0)
                                                        <td style="display: flex; flex-wrap: wrap;">
                                                            @foreach($permissions as $permission)
                                                            <span class="badge rounded p-2 m-1 px-3 bg-{{ $permission->name == 'access_all_data' ? 'success' : 'primary' }}">
                                                                {{ $role->hasPermissionTo($permission->name) ? str_replace("_", " ", $permission->name) : '' }}
                                                            </span>
                                                            @endforeach
                                                        </td>
                                                        @else
                                                        <td class="align-items-center">
                                                            <span>
                                                                {{ get_label('no_permissions_assigned', 'No Permissions Assigned!') }}
                                                            </span>
                                                        </td>
                                                        @endif
                                                        <td class="align-items-center">
                                                            <div class="d-flex">
                                                                <a href="/roles/edit/{{ $role->id }}" class="card-link"><i class='bx bx-edit text-primary' style="font-size: 1.2rem;"></i></a>
                                                                <a href="javascript:void(0);" data-id="{{ $role->id }}" class="card-link mx-4 delete" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class='bx bx-trash text-danger' style="font-size: 1.2rem;"></i></a>
                                                                
                                                                <!-- Hidden form for deletion -->
                                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" id="delete-form-{{ $role->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </div>
                                                        </td>
                                                        @endif
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
            </section>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this role?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let roleIdToDelete;

        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                roleIdToDelete = this.getAttribute('data-id');
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            document.getElementById('delete-form-' + roleIdToDelete).submit();
        });
    </script>
@endsection

<!-- Boxicons CSS -->
<link href='https://unpkg.com/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endcan