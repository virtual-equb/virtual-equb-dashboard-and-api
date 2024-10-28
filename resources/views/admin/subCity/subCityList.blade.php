{{-- @if (Auth::user()->role == 'admin' ||
    Auth::user()->role == 'general_manager' ||
    Auth::user()->role == 'operation_manager' ||
    Auth::user()->role == 'finance' ||
    Auth::user()->role == 'customer_service' ||
    Auth::user()->role == 'assistant' ||
    Auth::user()->role == 'it') --}}

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
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-pills" id="custom-tabs-two-tab" role="tablist">
                                        <li class="nav-item nav-blue memberTab">
                                            <a class="nav-link active" id="custom-tabs-two-member-tab"
                                               data-toggle="pill" href="#custom-tabs-two-member" role="tab"
                                               aria-controls="custom-tabs-two-member" aria-selected="true">
                                                <span class="fa fa-list"></span> Sub Cities
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="float-right">
                                        @if (Auth::user()->role != 'assistant' && Auth::user()->role != 'finance')
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSubCityModal" style="margin-right: 30px;">
                                                <span class="fa fa-plus-circle"></span> Add Sub City
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <a href="{{ route('subcities.index') }}" class="btn btn-primary" style="margin-right: 30px;">
                                            <i class="fa fa-check-square"></i> Sub City
                                        </a>
                                        <div class="col-md-4">
                                            <input class="form-control responsive-input" type="text" id="subCitySearchText" placeholder="Search Sub City">
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-default" id="clearSearch">Clear</button>
                                        </div>
                                    </div>
                                    <div id="sub_city_table_data" class="table-responsive">
                                        <table class="table table-bordered" id="subCityTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Sub City Name</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($subCities as $key => $subCity)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $subCity->name }}</td>
                                                        <th>
                                                            <span class="badge {{ $subCity->active == 1 ? 'badge-success' : 'badge-danger' }}">
                                                                {{ $subCity->active == 1 ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </th>
                                                        @if (Auth::user()->role != 'assistant')
                                                            <td>
                                                                <div class='dropdown'>
                                                                    <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                                    <ul class='dropdown-menu p-4'>
                                                                        @if (Auth::user()->role != 'finance')
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $subCity->id }})">
                                                                                    <span class="fa fa-edit"></span> Edit
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat delete-sub-city" data-id="{{ $subCity->id }}">
                                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                                </button>
                                                                            </li>
                                                                        @endif
                                                                    </ul>
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
            </section>
        </div>
    </div>

    <!-- Include the Add Sub City Modal -->
    @include('admin.subCity.addSubCity')

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
                        <div class="form-group">
                            <label for="editSubCityName" class="control-label">Sub City Name:</label>
                            <input type="text" class="form-control" id="editSubCityName" required>
                        </div>
                        <div class="form-group">
                            <label for="editSubCityStatus" class="control-label">Status:</label>
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

@endsection

@section('scripts')
    <script>
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
            // Optionally refresh the table or apply a filter reset
        });
    </script>
@endsection
{{-- @endif --}}