@can('view unpaid_lottories_report')
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
                                                <h4>Paid Lotteries Report</h4>
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
                $.ajax({
                    url: "{{ url('reports/paginatePaidLotterys') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#filterLotteryTable').html(data);
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
                    url: "{{ url('reports/paidLotterys') }}",
                    method: 'get',
                    success: function(form) {
                        $('#filterLotteryTable').html(form);
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
