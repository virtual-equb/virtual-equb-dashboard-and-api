{{-- @if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'finance' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'it') --}}
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

            @media (max-width: 1400px) {
                #lottery-table {
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
                                                    <h4>Reserved Lottery Dates Report</h4>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <label>Date range:</label>
                                                    <div class="form-group col-md-3">
                                                        <input type="text" class="form-control" name="dateFrom"
                                                            id="dateFrom" placeholder="Start Date">
                                                        <p class="text-red d-none" id="dateFromRequired">Please enter start
                                                            date</p>
                                                    </div>
                                                    <div class="m-1">
                                                        <span> - </span>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <input type="text" class="form-control" name="dateTo"
                                                            id="dateTo" placeholder="End Date">
                                                        <p class="text-red d-none" id="dateToRequired">Please enter end date
                                                        </p>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <select class="form-control select2" id="equb_type" name="equb_type"
                                                            placeholder="Equb Type" required>
                                                            <option value="">Select Equb Type</option>
                                                            <option value="all">All</option>
                                                            @foreach ($equbTypes as $equbType)
                                                                <option value="{{ $equbType->id }}">{{ $equbType->name }} - Round {{ $equbType->round }}
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
                                        <div id="filterLotteryTable"></div>

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
            function lotterys(offsetVal, pageNumberVal) {
                let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                $.ajax({
                    url: "{{ url('reports/paginateReservedLotteryDates') }}" + '/' + $('#dateFrom').val() + '/' + $(
                        '#dateTo').val() + '/' + offsetVal + '/' + pageNumberVal + '/' + equbType,
                    type: 'get',
                    success: function(data) {
                        $('#filterLotteryTable').html(data);
                    }
                });
            }

            function fiter() {
                if ($('#dateFrom').val() == "" && $('#dateTo').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                } else {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                    $.ajax({
                        url: "{{ url('reports/reservedLotteryDates') }}" + '/' + $('#dateFrom').val() + '/' + $(
                            '#dateTo').val() + '/' + equbType,
                        method: 'get',
                        success: function(form) {
                            $('#filterLotteryTable').html(form);
                        }
                    });
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                }
            }

            $(function() {
                $("#dateFrom").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
                $("#dateTo").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
            })
        </script>
    @endSection
{{-- @endif --}}
