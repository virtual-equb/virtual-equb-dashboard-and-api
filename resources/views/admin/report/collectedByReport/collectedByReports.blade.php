@can('view report')
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
                                                <h4>Collected by Report</h4>
                                            </div>
                                            <div class="row justify-content-center">
                                                <label>Date range:</label>
                                                <div class="form-group col-md-2">
                                                    <input type="text" class="form-control" name="dateFrom"
                                                        id="dateFrom" placeholder="Date From" required>
                                                    <p class="text-red d-none" id="dateFromRequired">Please enter date From
                                                    </p>
                                                </div>
                                                <div class="m-1">
                                                    <span> - </span>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <input type="text" class="form-control" name="dateTo" id="dateTo"
                                                        placeholder="Date To" required>
                                                    <p class="text-red d-none" id="dateToRequired">Please enter date To</p>
                                                </div>
                                                <label>Collecter:</label>
                                                <div class="form-group col-md-2">
                                                    <select class="form-control select2" id="collecter" name="collecter"
                                                        placeholder="Collecter" required>
                                                        <option value="">Select collecter</option>
                                                        <option value="all">All</option>
                                                        @foreach ($collecters as $collecter)
                                                            <option value="{{ $collecter->id }}">{{ $collecter->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <p class="text-red d-none" id="collecterRequired">Please enter collecter
                                                    </p>
                                                </div>
                                                <div class="form-group col-md-2">
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
                                                <div class="form-group col-md-2">
                                                        <select class="form-control select2" id="paymentMethod" name="payment_method"
                                                            placeholder="payment_method" required>
                                                            <option value="">Select Payment Method</option>
                                                            <option value="all">All</option>
                                                            @foreach ($paymentMethod as $method)
                                                            <option value="{{ $method['name'] }}">{{ $method['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        
                                                        <p class="text-red d-none" id="payment_method">Please select Payment Method</p>
                                                    </div>
                                            </div>
                                            <div class="form-group row justify-content-center">
                                                <button type="button" class="btn btn-primary col-md-3"
                                                    onclick="fiter()">Filter</button>
                                            </div>
                                        </div>
                                        <div id="filterCollectedByTable"></div>
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
                let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                $.ajax({
                    url: "{{ url('reports/paginateCllectedBys') }}" + '/' + $('#dateFrom').val() + '/' + $('#dateTo')
                        .val() + '/' + $('#collecter').val()  + '/' + $('#paymentMethod').val() + '/' + offsetVal + '/' + pageNumberVal + '/' + equbType,
                    type: 'get',
                    success: function(data) {
                        $('#filterCollectedByTable').html(data);
                    }
                });
            }

            function fiter() {
                if ($('#dateFrom').val() == "" && $('#dateTo').val() == "" && $('#collecter').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                    $('#collecterRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#dateTo').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#dateToRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "" && $('#collecter').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                    $('#collecterRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "" && $('#collecter').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                    $('#collecterRequired').removeClass('d-none');
                } else if ($('#dateFrom').val() == "") {
                    $('#dateFromRequired').removeClass('d-none');
                } else if ($('#dateTo').val() == "") {
                    $('#dateToRequired').removeClass('d-none');
                } else if ($('#collecter').val() == "") {
                    $('#collecterRequired').removeClass('d-none');
                } else {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    $('#collecterRequired').addClass('d-none');
                    let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                    let paymentMethod =  $('#paymentMethod').val() != "" ? $('#paymentMethod').val() : 'all';
                    $.ajax({
                        url: "{{ url('reports/collectedBys') }}" + '/' + $('#dateFrom').val() + '/' + $('#dateTo')
                            .val() + '/' + $('#collecter').val() + '/' + paymentMethod + '/' + equbType,
                        method: 'get',
                        success: function(form) {
                            $('#filterCollectedByTable').html(form);
                        }
                    });
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "" && $('#collecter').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                    $('#collecterRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#dateTo').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#dateToRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "" && $('#collecter').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                    $('#collecterRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "" && $('#collecter').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                    $('#collecterRequired').addClass('d-none');
                }
                if ($('#dateFrom').val() != "") {
                    $('#dateFromRequired').addClass('d-none');
                }
                if ($('#dateTo').val() != "") {
                    $('#dateToRequired').addClass('d-none');
                }
                if ($('#collecter').val() != "") {
                    $('#collecterRequired').addClass('d-none');
                }
            }
            $(function() {
                $('#updatePayment').validate({
                    onfocusout: false,
                    rules: {
                        dateFrom: {
                            required: true,
                        },
                        dateTo: {
                            required: true,
                        },
                        member: {
                            required: true,
                        }
                    },
                    messages: {
                        dateFrom: {
                            required: "Select date from",
                        },
                        dateTo: {
                            required: "Select date to",
                        },
                        member: {
                            required: "Select member",
                        },
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }

                });
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
