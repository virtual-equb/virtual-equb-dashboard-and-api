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
                                            <div class="row justify-content-center" style="margin-bottom: 20px;">
                                                <h4>UnPaid Lotteries By Lottery Date Report</h4>
                                            </div>
                                            <div class="row justify-content-center">
                                                <label>Lottery Date:</label>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="lotteryDate"
                                                        id="lotteryDate" placeholder="Lottery Date">
                                                    <p class="text-red d-none" id="lotteryDateRequired">Please enter date
                                                        From</p>
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
                    url: "{{ url('reports/paginateUnPaidLotterysByDate') }}" + '/' + offsetVal + '/' + pageNumberVal +
                        '/' + equbType,
                    type: 'get',
                    success: function(data) {
                        $('#filterLotteryTable').html(data);
                    }
                });
            }

            function fiter() {
                if ($('#lotteryDate').val() == "") {
                    $('#lotteryDateRequired').removeClass('d-none');
                } else {
                    $('#lotteryDateRequired').addClass('d-none');
                    let equbType = $('#equb_type').val() != "" ? $('#equb_type').val() : 'all';
                    $.ajax({
                        url: "{{ url('reports/unPaidLotterysByDate') }}" + '/' + $('#lotteryDate').val() + '/' +
                            equbType,
                        method: 'get',
                        success: function(form) {
                            $('#filterLotteryTable').html(form);
                        }
                    });
                }
                if ($('#lotteryDate').val() != "") {
                    $('#lotteryDateRequired').addClass('d-none');
                }
                if ($('#lotteryDate').val() != "") {
                    $('#lotteryDateRequired').addClass('d-none');
                }
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
                                    `<option value="${equbType.id}"> ${equbType.name}</option>`;
                            });
                            $('#equb').empty();
                            $('#equb').append(option);
                            $('#equb_type_id').empty();
                            $('#equb_type_id').append(option);
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
                                    `<option value="${equb.equb_type.id}"> ${equb.equb_type.name}</option>`;
                            });
                            $('#equb').empty();
                            $('#equb').append(option);
                            $('#equb_type_id').empty();
                            $('#equb_type_id').append(option);
                        }
                    });
                }

            }
            $(function() {
                $.ajax({
                    url: "{{ url('reports/unPaidLotterysByDate') }}",
                    method: 'get',
                    success: function(form) {
                        $('#filterLotteryTable').html(form);
                    }
                });
                $("#lotteryDate").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
            })
        </script>
    @endSection
{{-- @endif --}}
