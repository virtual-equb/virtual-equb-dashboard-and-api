@can('view role')
@extends('layouts.app')
@section('styles')
    <style type="text/css">
        .details-control {
            background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
            cursor: pointer;
        }

        tr.shown .details-control {
            background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
        }

        .form-group.required .control-label:after {
            content: "*";
            color: red;
        }

        div.dataTables_wrapper div.dataTables_info {
            padding-top: 0.85em;
            display: none;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .responsive-input {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
@endsection
@section('content')
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalRoles }}</h3>
                                <p>Total Roles</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @if (session('status'))
                            <div class="alert alert-success mt-3">{{ session('status') }}</div>
                        @endif
                        <div class="card">
                            <div class="card-header">
                                <h4>Roles
                                    <a href="{{ url('roles/create')}}" class="btn btn-primary float-right"><span class="fa fa-plus-circle"></span> Create Roles</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div id="role_table_data" class="table-responsive">
                                    <table id="roleTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Guard Name</th>
                                                <th>Created Date</th>
                                                <th style="width: 50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $index => $role)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $role->name}}</td>
                                                <td>{{ $role->guard_name}}</td>
                                                <td>{{ date('d-m-Y', strtotime($role->created_at))}}</td>
                                                <td>
                                                    <div class='dropdown'>
                                                        <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                        <ul class='dropdown-menu p-4'>
                                                            <a href="{{ url('roles/'.$role->id.'/edit') }}" class="btn-sm btn btn-flat">Edit</a>
                                                            <a href="{{ url('roles/'.$role->id.'/delete')}}" class="btn-sm btn btn-flat">Delete</a>
                                                        </ul>
                                                    </div>
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
        </section>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $("#roleTable").DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": true,
                "autoWidth": false,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                },
                "buttons": ["excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#role_table_data .col-md-6:eq(0)');
        });
    </script>
@endsection

@endcan

