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
                                                <span class="fa fa-list"></span> City
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="float-right">
                                        @if (Auth::user()->role != 'assistant' && Auth::user()->role != 'finance')
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCityModal" style="margin-right: 30px;">
                                                <span class="fa fa-plus-circle"></span> Add City
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
                                            <input class="form-control responsive-input" type="text" id="citySearchText" placeholder="Search City">
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-default" id="clearSearch">Clear</button>
                                        </div>
                                    </div>
                                    <div id="city_table_data" class="table-responsive">
                                        <table class="table table-bordered" id="cityTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>City Name</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cities as $key => $city)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $city->name }}</td>
                                                        <td>
                                                            <span class="badge {{ $city->active == 1 ? 'badge-success' : 'badge-danger' }}">
                                                                {{ $city->active == 1 ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                        @if (Auth::user()->role != 'assistant')
                                                            <td>
                                                                <div class='dropdown'>
                                                                    <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                                    <ul class='dropdown-menu p-4'>
                                                                        @if (Auth::user()->role != 'finance')
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $city->id }})">
                                                                                    <span class="fa fa-edit"></span> Edit
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat delete-city" data-id="{{ $city->id }}">
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

    <!-- Include the Add City Modal -->
    @include('admin.city.addCity')

    <!-- Edit City Modal -->
    <div class="modal fade" id="editCityModal" tabindex="-1" role="dialog" aria-labelledby="editCityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editCityForm">
                        <input type="hidden" id="editCityId">
                        <div class="form-group">
                            <label for="editCityName" class="control-label">City Name:</label>
                            <input type="text" class="form-control" id="editCityName" required>
                        </div>
                        <div class="form-group">
                            <label for="editCityStatus" class="control-label">Status:</label>
                            <select class="form-control" id="editCityStatus" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditCity">Save changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function openEditModal(cityId) {
            // Fetch city data
            $.ajax({
                url: '/cities/' + cityId,
                type: 'GET',
                success: function(city) {
                    // Populate the modal fields
                    $('#editCityId').val(city.id);
                    $('#editCityName').val(city.name);
                    $('#editCityStatus').val(city.active); // Set the status dropdown
                    $('#editCityModal').modal('show'); // Show the modal
                },
                error: function(xhr) {
                    console.error('Error fetching city data:', xhr.responseText);
                }
            });
        }

        $('#saveEditCity').click(function() {
            const id = $('#editCityId').val(); // Get the city ID
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
        location.reload(); // Refresh the city table after saving
    },
    error: function(xhr) {
        console.log(xhr.responseText);
        alert('Error updating city: ' + xhr.responseText);
    }
});
        });
    </script>
@endsection
{{-- @endif --}}