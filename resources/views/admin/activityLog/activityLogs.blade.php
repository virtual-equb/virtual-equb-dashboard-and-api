@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'it')
    @extends('layouts.app')
    @section('styles')
        <style type="text/css">
            div.dataTables_wrapper div.dataTables_paginate {
                margin: 0;
                display: none;
                white-space: nowrap;
                text-align: right;
            }

            div.dataTables_wrapper div.dataTables_info {
                padding-top: 0.85em;
                display: none;
            }

            @media (max-width: 575.98px) {
                #logs {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .table-responsive-sm>.table-bordered {
                    border: 0;
                }
            }

            @media (max-width: 575.98px) {
                #logs-detail {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .table-responsive-sm>.table-bordered {
                    border: 0;
                }
            }

            @media (max-width: 768px) {
                .activityLogs {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .logsDeatails {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .col-md-6 {
                    margin-bottom: 20px;
                    width: 100%;
                    padding-left: 0px;
                    padding-right: 0px;
                    float: left;
                }
            }
        </style>
    @endsection
    @section('content')
        <div class="content-wrapper">
            <div class="content">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item activityLogs">
                                <a class="nav-link active" id="activity-log-tab" data-toggle="pill" href="#activity-log"
                                    role="tab" onclick="removeTables();" aria-controls="employees" aria-selected="true">
                                    <span class="fa fa-list"> </span>
                                    Activity Logs</a>
                            </li>
                            <li class="nav-item ml-1 logsDeatails">
                                <a class="nav-link" id="log-details-tab" style="display: none;" data-toggle="pill"
                                    href="#log-details" role="tab" aria-controls="log-details" aria-selected="true">
                                    <span class="fa fa-list">
                                    </span>
                                    Log Details</a>
                            </li>
                            <div class="float-right searchandClear row col-4 offset-md-5 d-none" id="member_table_filter">
                                <input class="form-control col-10" type="text" id="memberSearchText"
                                    placeholder="Search User" class="search">
                                <button class="btn btn-default clear col-2" id="clearActiveSearch"
                                    onclick="clearSearchEntry()">
                                    Clear
                                </button>
                            </div>

                        </ul>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content" id="activity-log-data">
                            <div class="tab-pane fade show active" id="activity-log"
                                aria-labelledby="custom-tabs-one-home-tab">
                                {{-- <table id="logs" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Type</th>
                                            <th>Action Number</th>
                                            <th style="width:150px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        @foreach ($countedTypes as $key => $countedType)
                                            <tr>
                                                <td> {{ $key + 1 }}</td>
                                                <td> On {{ $countedType->type }}</td>
                                                <td>{{ $countedType->total }}</td>
                                                <td><button class="btn btn-secondary"
                                                        onclick="viewLogs('{{ $countedType->getRawOriginal('type') }}')">
                                                        View
                                                        Logs</button></td>
                                            </tr>
                                        @endforeach




                                    </tbody>
                                </table> --}}
                                <div id="user_table_data">

                                </div>
                            </div>

                        </div>
                        <div class="tab-content" id="log-details-data">
                            <div class="tab-pane fade show" id="log-details" aria-labelledby="custom-tabs-one-home-tab">

                            </div>

                        </div>
                    </div>
                </div>


            </div>


        </div>
    @endsection


    @section('scripts')
        <script>
            let viewLogs, removeTables;
            const loading =
                '<br><div id="loading" class="row d-flex justify-content-center"><div class="row"><img src="' +
                "{{ url('/img/loading.gif') }}" + '"/></div></div>';

            function viewLogsPaginate(offsetVal, pageNumberVal) {
                const type = getFromCache('type');
                const searchInput = memberSearchField.value;
                // console.log(type);
                $('#activity-log-data').css('display', 'none');
                $('#activity-log-tab').removeClass('active');


                $('#log-details-tab').addClass('active');
                $('#log-details-tab').css('display', '');

                $('#log-details-data').css('display', '');


                $.ajax({
                    url: "activityLog/logDetailPaginate/" + type + '/' + offsetVal + '/' + pageNumberVal,
                    method: 'get',
                    success: function(data) {
                        $('#log-details-data').html(data);
                    }
                });

            }

            function viewSearchLogsPaginate(searchInput, offsetVal, pageNumberVal) {
                const type = getFromCache('type');
                // const searchInput = memberSearchField.value;
                $('#activity-log-data').css('display', 'none');
                $('#activity-log-tab').removeClass('active');


                $('#log-details-tab').addClass('active');
                $('#log-details-tab').css('display', '');

                $('#log-details-data').css('display', '');


                $.ajax({
                    url: "activityLog/search-activity/" + type + '/' + searchInput + '/' + offsetVal +
                        '/' + pageNumberVal,
                    method: 'get',
                    success: function(data) {
                        $('#log-details-data').html(data);
                    }
                });

            }
            var memberSearchField = document.getElementById('memberSearchText');
            memberSearchField.addEventListener("keydown", function(e) {
                var memberSearchInput = memberSearchField.value;
                if (e.keyCode === 13) { //checks whether the pressed key is "Enter"
                    $.LoadingOverlay("show");
                    searchForMember(memberSearchInput);
                }
            });

            function searchForMember(searchInput) {
                const type = getFromCache('type');
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('activityLog/search-activity') }}" + '/' +
                            type + '/' + searchInput + '/0',
                        type: 'get',
                        success: function(data) {
                            $('#log-details-data').html(data);
                            // $('#user_table_data').html(data);
                            $.LoadingOverlay("hide");
                        }
                    });
                } else {
                    clearSearchEntry();
                    $.LoadingOverlay("hide");
                }
            }

            function clearSearchEntry() {
                $.LoadingOverlay("show");
                var searchInput = document.getElementById('memberSearchText').value;
                // if (searchInput != "") {
                document.getElementById('memberSearchText').value = "";
                // Refresh the current page
                window.location.reload();
                // $.ajax({
                //     url: "{{ url('activityLog/clearSearchEntry') }}",
                //     type: 'get',
                //     success: function(data) {
                //         $('#user_table_data').html(data);
                //         $.LoadingOverlay("hide");
                //     }
                // });

                // }
            }

            function loadMoreSearchActivity(searchInput, offsetVal, pageNumberVal) {
                const type = getFromCache('type');
                if (searchInput != "") {
                    $.ajax({
                        url: "{{ url('activityLog/search-activity') }}" + '/' + type + '/' + searchInput + '/' +
                            offsetVal + '/' +
                            pageNumberVal,
                        type: 'get',
                        success: function(data) {
                            $('#user_table_data').html(data);
                        }
                    });

                }
            }

            function activityLogs(offsetVal, pageNumberVal) {
                $.ajax({
                    url: "{{ url('activityLog/activityLog') }}" + '/' + offsetVal + '/' + pageNumberVal,
                    type: 'get',
                    success: function(data) {
                        $('#user_table_data').html(data);
                    }
                });
            }
            $(function() {
                $.ajax({
                    url: "{{ url('activityLog/activityLog') }}" + '/' + 0 + '/' + 1,
                    type: 'get',
                    success: function(data) {
                        $('#user_table_data').html(data);
                    }
                });
                // $('#nav-user').addClass('menu-is-opening menu-open active');
                $('#activity_log').addClass('active');
                //    $('#nav-u').addClass('active');
                $('#conf-list').addClass('menu-is-opening menu-open');
                $('#configurables-link').addClass('active');

                $('#settings-link').addClass('menu-is-opening menu-open');
                $('#settings-a-link').addClass('active');
                $('#activity-log-link').addClass('active');

                removeTables = function() {
                    $('#activity-log-data').css('display', '');
                    $('#log-details-data').css('display', 'none');
                    $('#log-details-tab').css('display', 'none');
                    var element = document.getElementById('member_table_filter');
                    element.classList.add('d-none');
                }



                viewLogs = function(type) {
                    storeInCache('type', type, 60); // Store for 1 hour
                    var element = document.getElementById('member_table_filter');
                    element.classList.remove('d-none');
                    const searchInput = memberSearchField.value;
                    $('#activity-log-data').css('display', 'none');
                    $('#activity-log-tab').removeClass('active');


                    $('#log-details-tab').addClass('active');
                    $('#log-details-tab').css('display', '');

                    $('#log-details-data').css('display', '');

                    $.ajax({
                        url: "activityLog/logDetail/" + type,
                        method: 'get',
                        success: function(data) {
                            $('#log-details-data').html(data);
                        }
                    });

                }

                $('#logs').DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "searching": true,
                    "autoWidth": true,
                    "buttons": ["colvis"],
                    language: {
                        search: "",
                        searchPlaceholder: "Search",
                    },
                    "buttons": ["excel", "pdf", "print", "colvis"]

                }).buttons().container().appendTo('#logs_wrapper .col-md-6:eq(0)');

            });
            // Store data in the cache
            function storeInCache(key, value, expirationInMinutes) {
                const expirationTime = new Date().getTime() + expirationInMinutes * 60000;
                const data = {
                    value: value,
                    expiration: expirationTime
                };
                localStorage.setItem(key, JSON.stringify(data));
            }

            // Retrieve data from the cache
            function getFromCache(key) {
                const data = JSON.parse(localStorage.getItem(key));
                if (data && data.expiration > new Date().getTime()) {
                    return data.value;
                } else {
                    return null;
                }
            }
        </script>
    @endsection
@endif
