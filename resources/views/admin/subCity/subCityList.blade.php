@can('view sub_city')

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
                                    <h3>{{ $totalSubCity }}</h3>
                                    <p>Total Sub Cities</p>
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
                                    <h3>{{ $totalActiveSubCity }}</h3>
                                    <p>Active Sub City</p>
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
                                    <h3>{{ $totalInactiveSubCity }}</h3>
                                    <p>Inactive Sub City</p>
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
                                    <h5>Sub City
                                        @can ('create sub_city')
                                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addSubCityModal"> <span class="fa fa-plus-circle"></span> Add Sub City</button>
                                        @endcan
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="sub_city_table_data" class="table-responsive">
                                        <table id="subCityTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th style="width: 50px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($subCities as $index => $subCity)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $subCity->name }}</td>
                                                        <th>
                                                            {{ $subCity->active == 1 ? 'Active' : 'Inactive' }}
                                                        </th>
                                                            <td>
                                                                <div class='dropdown'>
                                                                    <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                                    <ul class='dropdown-menu dropdown-menu-right p-4'>
                                                                        @can('update sub_city')
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $subCity->id }})">
                                                                                    <span class="fa fa-edit"></span> Edit
                                                                                </button>
                                                                            </li>
                                                                        @endcan
                                                                        @can('delete sub_city')
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                    class="btn-sm btn btn-flat"
                                                                                    onclick="openDeleteModal({{ $subCity }})"><i
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
                        <p class="modal-title" id="exampleModalLabel">Delete Sub City</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="deleteSubCityData">
                            @csrf
                            @method('DELETE')
                            <input id="id" name="id" hidden value="">
                            <p class="text-center">Are you sure you want to delete this sub city data ?</p>
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

    <!-- Edit Sub City Modal -->
    <div class="modal fade" id="editSubCityModal" tabindex="-1" role="dialog" aria-labelledby="editSubCityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubCityModalLabel">Edit Sub City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editSubCityForm">
                        <input type="hidden" id="editSubCityId">
                        <div class="form-group required">
                            <label for="editSubCityName" class="control-label">Sub City Name</label>
                            <input type="text" class="form-control" id="editSubCityName" required>
                        </div>
                        <div class="form-group required">
                            <label for="editSubCityStatus" class="control-label">Status</label>
                            <select class="form-control" id="editSubCityStatus" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditSubCity">Save changes</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Include the Add Sub City Modal -->
     @include('admin.subCity.addSubCity')
@endsection

@section('scripts')
    <script>
        $("#deleteSubCityData").submit(function() {
            $.LoadingOverlay("show");
        });

        function openDeleteModal(city) {
            $('#id').val(city.id);
            $('#deleteModal').modal('show');
            $('#deleteSubCityData').attr('action', '/subcities/' + city.id);
        }

        function openEditModal(subCityId) {
            console.log('Opening edit modal for subCityId:', subCityId); // Added log
            $.ajax({
                url: '/subcities/' + subCityId,
                type: 'GET',
                success: function(subCity) {
                    console.log('Fetched subCity data:', subCity); // Log the fetched data
                    $('#editSubCityId').val(subCity.id);
                    $('#editSubCityName').val(subCity.name);
                    $('#editSubCityStatus').val(subCity.active);
                    $('#editSubCityModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Error fetching sub city data:', xhr.responseText);
                }
            });
        }

        $('#saveEditSubCity').click(function() {
            const id = $('#editSubCityId').val();
            const name = $('#editSubCityName').val();
            const status = $('#editSubCityStatus').val();

            $.ajax({
                type: 'PUT',
                url: '/subcities/' + id,
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
                    alert('Error updating sub city: ' + xhr.responseText);
                }
            });
        });

        $(document).on('click', '.delete-sub-city', function() {
            const subCityId = $(this).data('id');
            if (confirm('Are you sure you want to remove this sub city?')) {
                $.ajax({
                    url: '/subcities/' + subCityId,
                    type: 'DELETE',
                    success: function(result) {
                        location.reload();
                    }
                });
            }
        });

        $('#clearSearch').click(function() {
            $('#subCitySearchText').val('');
        });

        $(function() {
            $('#subcityLink').addClass('active');
            $('#nav-u').addClass('active');;
            $("#subCityTable").DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": true,
                "autoWidth": false,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                },
                "buttons": ["excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#sub_city_table_data .col-md-6:eq(0)');
        });
    </script>
@endsection
@endcan