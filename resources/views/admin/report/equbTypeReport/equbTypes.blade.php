
    @extends('layouts.app')
    @section('styles')
        <style type="text/css">
            .form-group.required .control-label:after {
                content: "*";
                color: red;
            }

            @media (max-width: 575.98px) {
                #equbType-table {
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
                                            <div class="row justify-content-center">
                                                <label>Date range:</label>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="equbTypeFrom"
                                                        id="equbTypeFrom" placeholder="Date From">
                                                    <p class="text-red d-none" id="equbTypeFromRequired">Please enter date
                                                        From</p>
                                                </div>
                                                <div class="m-1">
                                                    <span> - </span>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <input type="text" class="form-control" name="equbTypeTo"
                                                        id="equbTypeTo" placeholder="Date To">
                                                    <p class="text-red d-none" id="equbTypeToRequired">Please enter date To
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-center">
                                                <button type="button" class="btn btn-primary col-md-3"
                                                    onclick="fiter()">Filter</button>
                                            </div>

                                        </div>
                                        <div id="filterEqubTypeTable"></div>
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
            function fiter() {
                if ($('#equbTypeFrom').val() == "" && $('#equbTypeTo').val() == "") {
                    $('#equbTypeFromRequired').removeClass('d-none');
                    $('#equbTypeToRequired').removeClass('d-none');
                } else if ($('#equbTypeFrom').val() == "") {
                    $('#equbTypeFromRequired').removeClass('d-none');
                } else if ($('#equbTypeTo').val() == "") {
                    $('#equbTypeToRequired').removeClass('d-none');
                } else {
                    $('#equbTypeFromRequired').addClass('d-none');
                    $('#equbTypeToRequired').addClass('d-none');
                    $.ajax({
                        url: "{{ url('reports/equbTypes') }}" + '/' + $('#equbTypeFrom').val() + '/' + $('#equbTypeTo')
                            .val(),
                        method: 'get',
                        success: function(form) {
                            $('#filterEqubTypeTable').html(form);
                        }
                    });
                }
                if ($('#equbTypeFrom').val() != "" && $('#equbTypeTo').val() != "") {
                    $('#equbTypeFromRequired').addClass('d-none');
                    $('#equbTypeToRequired').addClass('d-none');
                }
                if ($('#equbTypeFrom').val() != "") {
                    $('#equbTypeFromRequired').addClass('d-none');
                }
                if ($('#equbTypeTo').val() != "") {
                    $('#equbTypeToRequired').addClass('d-none');
                }
            }
            $(function() {
                $("#equbTypeFrom").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
                $("#equbTypeTo").datetimepicker({
                    'format': "YYYY-MM-DD",
                });
            })
        </script>
    @endSection
