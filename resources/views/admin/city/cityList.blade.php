
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
                                        @can ('create city')
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCityModal" style="margin-right: 30px;">
                                                <span class="fa fa-plus-circle"></span> Add City
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <a href="{{ route('subcities.index') }}" class="btn btn-primary" style="margin-right: 30px;">
                                            <i class="fa fa-check-square"></i> Sub City Of City
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
                                                            <td>
                                                                <div class='dropdown'>
                                                                    <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                                                    <ul class='dropdown-menu p-4'>
                                                                        @can('update city')
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat" onclick="openEditModal({{ $city->id }})">
                                                                                    <span class="fa fa-edit"></span> Edit
                                                                                </button>
                                                                            </li>
                                                                        @endcan
                                                                        @can('delete city')
                                                                            <li>
                                                                                <button class="text-secondary btn btn-flat delete-city" data-id="{{ $city->id }}">
                                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                                </button>
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

    <!-- Include the Add City Modal -->
    @include('admin.city.addCity')
    @include('admin.city.editCity')
    <!-- Edit City Modal -->


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