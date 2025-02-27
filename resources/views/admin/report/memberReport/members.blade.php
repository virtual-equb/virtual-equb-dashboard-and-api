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
                #member-table {
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
                                                <h4>Member Report</h4>
                                            </div>
                                            <div class="row justify-content-center">
                                                <label>Date range:</label>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="memberFrom"
                                                        id="memberFrom" placeholder="Date From">
                                                    <p class="text-red d-none" id="memberFromRequired">Please enter date
                                                        From</p>
                                                </div>
                                                <div class="m-1">
                                                    <span> - </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="memberTo"
                                                        id="memberTo" placeholder="Date To">
                                                    <p class="text-red d-none" id="memberToRequired">Please enter date To
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-center">
                                                <button type="button" class="btn btn-primary col-md-3"
                                                    onclick="fiter()">Filter</button>
                                            </div>
                                        </div>
                                        <div id="filterMemberTable"></div>
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
            function members(offsetVal, pageNumberVal) {
                $.ajax({
                    url: "{{ url('reports/paginateMembers') }}" + '/' + $('#memberFrom').val() + '/' + $('#memberTo')
                        .val() + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#filterMemberTable').html(data);
                    }
                });
            }

            function fiter() {
                if ($('#memberFrom').val() == "" && $('#memberTo').val() == "") {
                    $('#memberFromRequired').removeClass('d-none');
                    $('#memberToRequired').removeClass('d-none');
                } else if ($('#memberFrom').val() == "") {
                    $('#memberFromRequired').removeClass('d-none');
                } else if ($('#memberTo').val() == "") {
                    $('#memberToRequired').removeClass('d-none');
                } else {
                    $('#memberFromRequired').addClass('d-none');
                    $('#memberToRequired').addClass('d-none');
                    $.ajax({
                        url: "{{ url('reports/members') }}" + '/' + $('#memberFrom').val() + '/' + $('#memberTo').val(),
                        method: 'get',
                        success: function(form) {
                            $('#filterMemberTable').html(form);
                        }
                    });
                }
                if ($('#memberFrom').val() != "" && $('#memberTo').val() != "") {
                    $('#memberFromRequired').addClass('d-none');
                    $('#memberToRequired').addClass('d-none');
                }
                if ($('#memberFrom').val() != "") {
                    $('#memberFromRequired').addClass('d-none');
                }
                if ($('#memberTo').val() != "") {
                    $('#memberToRequired').addClass('d-none');
                }
            }
            
            $(function() {
                $("#memberFrom").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
                $("#memberTo").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
            })
        </script>
@endSection
