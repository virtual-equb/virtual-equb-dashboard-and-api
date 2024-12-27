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

            .table-responsive-sm > .table-bordered {
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
                                        <form role="form" method="post" class="form-horizontal" enctype="multipart/form-data" id="equbReport">
                                            <div class="row justify-content-center mb-3">
                                                <h4>Reserved Lottery Dates Report</h4>
                                            </div>
                                            <div class="row justify-content-center">
                                                <label>Date range:</label>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="dateFrom" id="dateFrom" placeholder="Start Date">
                                                    <p class="text-red d-none" id="dateFromRequired">Please enter start date</p>
                                                </div>
                                                <div class="m-1">
                                                    <span> - </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="dateTo" id="dateTo" placeholder="End Date">
                                                    <p class="text-red d-none" id="dateToRequired">Please enter end date</p>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <select class="form-control select2" id="equb_type" name="equb_type" required>
                                                        <option value="">Select Equb Type</option>
                                                        <option value="all">All</option>
                                                        @foreach ($equbTypes as $equbType)
                                                            <option value="{{ $equbType->id }}">{{ $equbType->name }} - Round {{ $equbType->round }}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="text-red d-none" id="equbTypeRequired">Please select equb type</p>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-center">
                                                <button type="button" class="btn btn-primary col-md-3" onclick="filter()">Filter</button>
                                            </div>
                                        </form>
                                        <div id="filterLotteryTable"></div>
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
        function lotteries(offsetVal, pageNumberVal) {
            let equbType = $('#equb_type').val() || 'all';
            $.ajax({
                url: "{{ url('reports/paginateReservedLotteryDates') }}" + '/' + $('#dateFrom').val() + '/' + $('#dateTo').val() + '/' + offsetVal + '/' + pageNumberVal + '/' + equbType,
                type: 'get',
                success: function(data) {
                    $('#filterLotteryTable').html(data);
                }
            });
        }

        function filter() {
            let dateFrom = $('#dateFrom').val();
            let dateTo = $('#dateTo').val();
            let equbType = $('#equb_type').val() || 'all';

            // Reset error messages
            $('#dateFromRequired, #dateToRequired, #equbTypeRequired').addClass('d-none');

            if (!dateFrom && !dateTo) {
                $('#dateFromRequired, #dateToRequired').removeClass('d-none');
            } else {
                if (!dateFrom) {
                    $('#dateFromRequired').removeClass('d-none');
                }
                if (!dateTo) {
                    $('#dateToRequired').removeClass('d-none');
                }
                if (dateFrom && dateTo) {
                    $.ajax({
                        url: "{{ url('reports/reservedLotteryDates') }}" + '/' + dateFrom + '/' + dateTo + '/' + equbType,
                        method: 'get',
                        success: function(form) {
                            $('#filterLotteryTable').html(form);
                        }
                    });
                }
            }
        }

        $(function() {
            $("#dateFrom, #dateTo").datetimepicker({
                format: "YYYY-MM-DD"
            });
        });
    </script>
@endsection