@if (Auth::user()->role == 'admin' ||
    Auth::user()->role == 'general_manager' ||
    Auth::user()->role == 'operation_manager' ||
    Auth::user()->role == 'finance' ||
    Auth::user()->role == 'customer_service' ||
    Auth::user()->role == 'assistant' ||
    Auth::user()->role == 'it')

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
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSubCityModal" style="margin-right: 30px;">
                                                <span class="fa fa-plus-circle"></span> Sub City
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
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
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cities as $key => $city)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>  {{-- Using $key directly for indexing --}}
                                                        <td>{{ $city->name }}</td>
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
                                                                                <button class="text-secondary btn btn-flat" onclick="openDeleteModal({{ $city->id }})">
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
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.delete-city', function() {
            const cityId = $(this).data('id');
            if (confirm('Are you sure you want to remove this city?')) {
                $.ajax({
                    url: '/cities/' + cityId,
                    type: 'DELETE',
                    success: function(result) {
                        location.reload(); // Refresh the city table
                    }
                });
            }
        });

        $('#clearSearch').click(function() {
            $('#citySearchText').val('');
            // Optionally refresh the table or apply a filter reset
        });
    </script>
@endsection
@endif