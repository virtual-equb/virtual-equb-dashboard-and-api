@can('view city')
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
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalCity }}</h3>
                                    <p>Total Cities</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $totalActiveCity }}</h3>
                                    <p>Active City</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $totalInactiveCity }}</h3>
                                    <p>Inactive City</p>
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
                            <div class="card">
                                <div class="card-header">
                                    <h5>City
                                        @can ('create city')
                                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addCityModal"> <span class="fa fa-plus-circle"> </span>  Add City </button>
                                        @endcan
                                    </h5>
                                </div>
                                
                                <div class="card-body">
                                    <div id="city_table_data" class="table-responsive">
                                        <table id="cityTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>City Name</th>
                                                    <th>Status</th>
                                                    <th style="width: 50px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cities as $index => $city)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $city->name }}</td>
                                                        <td>
                                                            {{ $city->active == 1 ? 'Active' : 'Inactive' }}
                                                        </td>
                                                            <td>
                                                                <div class='dropdown'>
                                                                    <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                                    <ul class='dropdown-menu dropdown-menu-right p-4'>
                                                                        @can('update city')
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $city->id }})">
                                                                                    <span class="fa fa-edit"></span> Edit
                                                                                </button>
                                                                            </li>
                                                                        @endcan
                                                                        @can('delete city')
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="btn-sm btn btn-flat"
                                                                                    onclick="openDeleteModal({{ $city }})"><i
                                                                                        class="fas fa-trash-alt"></i>
                                                                                    Delete</a>
                                                                            </li>
                                                                        @endcan
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

    <div class="table-responsive">
        <div class="modal modal-danger fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <p class="modal-title" id="exampleModalLabel">Delete City</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="deleteCityData">
                            @csrf
                            @method('DELETE')
                            <input id="id" name="id" hidden value="">
                            <p class="text-center">Are you sure you want to delete this city data ?</p>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the Add City Modal -->
    @include('admin.city.addCity')
    @include('admin.city.editCity')
    <!-- Edit City Modal -->
@endsection

@section('scripts')
    <script>
        $("#deleteCityData").submit(function() {
            $.LoadingOverlay("show");
        });

        function openDeleteModal(city) {
            $('#id').val(city.id);
            $('#deleteModal').modal('show');
            $('#deleteCityData').attr('action', '/cities/' + city.id);
        }

        function openEditModal(cityId) {
            $.ajax({
                url: '/cities/' + cityId,
                type: 'GET',
                success: function(city) {
                    // Populate the modal fields
                    $('#editCityId').val(city.id);
                    $('#editCityName').val(city.name);
                    $('#editCityStatus').val(city.active);
                    $('#editCityModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Error fetching city data:', xhr.responseText);
                }
            });
        }

        $('#saveEditCity').click(function() {
            const id = $('#editCityId').val();
            const name = $('#editCityName').val();
            const status = $('#editCityStatus').val();

            $.ajax({
                type: 'PUT',
                url: '/cities/' + id, // Ensure this matches the route
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    status: status
                },
                success: function(result) {
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Error updating city: ' + xhr.responseText);
                }
            });
        });

        $(function() {
            $('#cityLink').addClass('active');
            $('#nav-u').addClass('active');;
            $("#cityTable").DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": true,
                "autoWidth": false,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                },
                "buttons": ["excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#city_table_data .col-md-6:eq(0)');
        });
    </script>
@endsection
@endcan