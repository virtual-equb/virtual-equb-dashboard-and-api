@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'finance' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'it')
    @extends('layouts.app')
    @section('styles')
        <style type="text/css">
            div.dataTables_wrapper div.dataTables_info {
                padding-top: 0.85em;
                display: none;
            }

            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }

            @media (max-width: 575.98px) {
                #equb-table {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .table-responsive-sm>.table-bordered {
                    border: 0;
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
                                <div class="row d-flex justify-content-center">
                                    <div class="card col-md-12">
                                        <div class="card-body">
                                            <form role="form" method="post" class="form-horizontal form-group nn"
                                                action="" enctype="multipart/form-data" id="equbReport">
                                                <div class="row justify-content-center" style="margin-bottom: 20px;">
                                                    <h4>Filter By Equb End Date Report</h4>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <label>Date range:</label>
                                                    <div class="form-group col-md-3">
                                                        <input type="text" class="form-control" name="equbFrom"
                                                            id="equbFrom" placeholder="Start Date">
                                                        <p class="text-red d-none" id="equbFromRequired">Please enter start
                                                            date</p>
                                                    </div>
                                                    <div class="m-1">
                                                        <span> - </span>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <input type="text" class="form-control" name="equbTo"
                                                            id="equbTo" placeholder="End Date">
                                                        <p class="text-red d-none" id="equbToRequired">Please enter end date
                                                        </p>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <select class="form-control select2" id="equb_type" name="equb_type"
                                                            placeholder="Equb Type" required>
                                                            <option value="">Select Equb Type</option>
                                                            <option value="all">All</option>
                                                            @foreach ($equbTypes as $equbType)
                                                                <option value="{{ $equbType->id }}">{{ $equbType->name }} -
                                                                    Round {{ $equbType->round }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <p class="text-red d-none" id="equbTypeRequired">Please select equb
                                                            type</p>
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-center">
                                                    <button type="button" class="btn btn-primary col-md-3"
                                                        onclick="fiter()">Filter</button>
                                                </div>
                                        </div>
                                        <div id="filterEqubTable"></div>
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
            function equbs(offsetVal, pageNumberVal) {
                let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                $.ajax({
                    url: "{{ url('reports/loadMoreFilterEqubEndDates') }}" + '/' + $('#equbFrom').val() + '/' + $(
                        '#equbTo').val() + '/' + offsetVal + '/' + pageNumberVal + '/' + equbType,
                    type: 'get',
                    success: function(data) {
                        $('#filterEqubTable').html(data);
                    }
                });
            }

            function fiter() {
                if ($('#equbFrom').val() == "" && $('#equbTo').val() == "") {
                    $('#equbFromRequired').removeClass('d-none');
                    $('#equbToRequired').removeClass('d-none');
                } else if ($('#equbFrom').val() == "") {
                    $('#equbFromRequired').removeClass('d-none');
                } else if ($('#equbTo').val() == "") {
                    $('#equbToRequired').removeClass('d-none');
                } else {
                    $('#equbFromRequired').addClass('d-none');
                    $('#equbToRequired').addClass('d-none');
                    let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                    $.ajax({
                        url: "{{ url('reports/filterEqubEndDates') }}" + '/' + $('#equbFrom').val() + '/' + $('#equbTo')
                            .val() + '/' + equbType,
                        method: 'get',
                        success: function(form) {
                            $('#filterEqubTable').html(form);
                        }
                    });
                }
                if ($('#equbFrom').val() != "" && $('#equbTo').val() != "") {
                    $('#equbFromRequired').addClass('d-none');
                    $('#equbToRequired').addClass('d-none');
                }
                if ($('#equbFrom').val() != "") {
                    $('#equbFromRequired').addClass('d-none');
                }
                if ($('#equbTo').val() != "") {
                    $('#equbToRequired').addClass('d-none');
                }
            }
            $(function() {
                $("#equbFrom").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
                $("#equbTo").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
            })
        </script>
    @endSection
@endif
