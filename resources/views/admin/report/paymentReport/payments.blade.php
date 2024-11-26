@can('view payment_report')
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
                #payment-table {
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
                                            <div class="row justify-content-center" style="margin-bottom: 20px;">
                                                <h4>Payment Report</h4>
                                            </div>
                                            <div class="row justify-content-center">
                                                <label>Date range:</label>
                                                <div class="form-group col-md-2">
                                                    <input type="text" class="form-control" name="dateFrom"
                                                        id="dateFrom" placeholder="Date From">
                                                    <p class="text-red d-none" id="dateFromRequired">Please select date From
                                                    </p>
                                                </div>
                                                <div class="m-1">
                                                    <span> - </span>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <input type="text" class="form-control" name="dateTo" id="dateTo"
                                                        placeholder="Date To">
                                                    <p class="text-red d-none" id="dateToRequired">Please select date To</p>
                                                </div>
                                                <label>Member:</label>
                                                <div class="form-group col-md-2">
                                                    <select class="form-control select2" id="member_id" name="member_id"
                                                        placeholder="Member" onchange="getEqubType(this)" required>
                                                        <option value="">Select Member</option>
                                                        <option value="all">All</option>
                                                        @foreach ($members as $member)
                                                            <option value="{{ $member->id }}">{{ $member->full_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <p class="text-red d-none" id="memberRequired">Please select member</p>
                                                </div>
                                                <label>Equb type:</label>
                                                <div class="form-group col-md-2">
                                                    <select name="equb_id" id="equb_id" class="form-control">
                                                        <option value="">Select equb</option>
                                                        <option value="all">All</option>
                                                            @foreach ($equbTypes as $equbType)
                                                                <option value="{{ $equbType->id }}">{{ $equbType->name }} - Round {{ $equbType->round }}
                                                                </option>
                                                            @endforeach
                                                    </select>
                                                    <p class="text-red d-none" id="equbTypeRequired">Please select equbType
                                                    </p>
                                                </div>

                                            </div>

                                            <div class="form-group row justify-content-center">
                                                <button type="button" class="btn btn-primary col-md-3"
                                                    onclick="fiter()">Filter</button>
                                            </div>

                                        </div>
                                        <div id="filterPaymentTable"></div>
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
            function payments(offsetVal, pageNumberVal) {
                $.ajax({
                    url: "{{ url('reports/paginatePayments') }}" + '/' + $('#dateFrom').val() + '/' + $('#dateTo')
                    .val() + '/' + $('#member_id').val() + '/' + $('#equb_id').val() + '/' + offsetVal + '/' +
                        pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#filterPaymentTable').html(data);
                    }
                });
            }

            function getEqubType(sel, cb = $.noop) {
                const memberId = sel.value;
                if (memberId == 'all') {
                    $.ajax({
                        url: '/member/get-allEqubs/',
                        method: 'get',
                        success: function(response) {
                            let option = "<option value='all'>All</option>";
                            response.map(function(equbType) {
                                option +=
                                    `<option value="${equbType.id}"> ${equbType.name} - Round ${equbType.round}</option>`;
                            });
                            $('#equb').empty();
                            $('#equb').append(option);
                            $('#equb_id').empty();
                            $('#equb_id').append(option);
                        }
                    });

                } else {
                    $.ajax({
                        url: '/member/get-equbs/' + memberId,
                        method: 'get',
                        success: function(response) {
                            let option = "<option value='all'>All</option>";
                            response.equbs.map(function(equb) {
                                option +=
                                    `<option value="${equb.id}"> ${equb.equb_type.name} - Round ${equb.equb_type.round}</option>`;
                            });
                            $('#equb').empty();
                            $('#equb').append(option);
                            $('#equb_id').empty();
                            $('#equb_id').append(option);
                        }
                    });
                }

            }

            function fiter() {
                if ($('#dateFrom').val() == "" && $('#dateTo').val() == "" && $('#member_id').val() == "" && $('#equb_id')
                .val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                    $('#memberRequired').removeClass('d-none');
                    $('#equbTypeRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#dateTo').val() == "" && $('#member_id').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                    $('#memberRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#dateTo').val() == "" && $('#equb_id').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                    $('#equbTypeRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "" && $('#member_id').val() == "" && $('#equb_id').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                    $('#memberRequired').removeClass('d-none');
                    $('#equbTypeRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#dateTo').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#member_id').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#memberRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "" && $('#member_id').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                    $('#memberRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#equb_id').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#equbTypeRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "" && $('#equb_id').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                    $('#equbTypeRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                } else if ($('#member_id').val() == "") {
                    $('#memberRequired').removeClass('d-none');
                } else if ($('#equb_id').val() == "") {
                    $('#equbTypeRequired').removeClass('d-none');
                } else {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    $('#collecterRequired').addClass('d-none');
                    $('#memberRequired').addClass('d-none');
                    $('#equbTypeRequired').addClass('d-none');
                    $.ajax({
                        url: "{{ url('reports/payments') }}" + '/' + $('#dateFrom').val() + '/' + $('#dateTo').val() +
                            '/' + $('#member_id').val() + '/' + $('#equb_id').val(),
                        method: 'get',
                        success: function(form) {
                            $('#filterPaymentTable').html(form);
                        }
                    });
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "" && $('#member_id').val() != "" && $('#equb_id')
                .val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    $('#memberRequired').addClass('d-none');
                    $('#equbTypeRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "" && $('#member_id').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    $('#memberRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "" && $('#equb_id').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    $('#equbTypeRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "" && $('#member_id').val() != "" && $('#equb_id').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                    $('#memberRequired').addClass('d-none');
                    $('#equbTypeRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#member_id').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#memberRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "" && $('#member_id').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                    $('#memberRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#equb_id').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#equbTypeRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "" && $('#equb_id').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                    $('#equbTypeRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                }
                if ($('#member_id').val() != "") {
                    $('#memberRequired').addClass('d-none');
                }
                if ($('#equb_id').val() != "") {
                    $('#equbTypeRequired').addClass('d-none');
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
    @endcan
