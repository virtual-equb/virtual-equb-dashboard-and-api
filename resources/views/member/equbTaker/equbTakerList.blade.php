@extends('layouts.app')
@section('styles')
    <style type="text/css">
        td.details-control_equb {
            background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control_equb {
            background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
        }
        .form-group.required .control-label:after { content: "*"; color: red; }
        div.dataTables_wrapper div.dataTables_paginate, div.dataTables_wrapper div.dataTables_info { display: none; }
        @media (max-width: 768px) {
            .addMember, .checkLottery, .search, .clear, .searchandClear, .checkLotteryandAddMember, .memberTab {
                width: 100%; margin-bottom: 20px;
            }
            .searchEqubandClear { width: 30%; }
        }
        @media (max-width: 575.98px) {
            #equb-taker-list-table_in_tab { display: block; width: 100%; overflow-x: auto; }
            .table-responsive-sm>.table-bordered { border: 0; }
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
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-pills" id="custom-tabs-two-tab" role="tablist">
                                        <li class="nav-item nav-blue memberTab">
                                            <a class="nav-link active" id="custom-tabs-two-member-tab" data-toggle="pill" href="#custom-tabs-two-member" role="tab" aria-controls="custom-tabs-two-member" aria-selected="true">
                                                <span class="fa fa-list"></span> Equb Taker
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-two-member" role="tabpanel" aria-labelledby="custom-tabs-two-member-tab">
                                            @include('admin/equb.addEqubTaker') <!-- Include updated Equb Taker form -->
                                            <div class="float-left checkLotteryandAddMember" id="member_table_filter"></div>
                                            <div class="row">
                                                <div class="col-7"></div>
                                                <div class="float-right searchandClear row col-5 mb-2" id="member_table_filter">
                                                    <input class="form-control col-10" type="text" id="memberSearchText" placeholder="Search Equb Taker">
                                                    <button class="btn btn-default clear col-2" id="clearActiveSearch" onclick="clearSearchEntry()">Clear</button>
                                                </div>
                                            </div>
                                            <div id="member_table_data_w" class="col-md-8"></div>
                                            <div id="member_table_data">
                                                @foreach ($equbTakers as $equbTaker)
                                                    <div>{{ $equbTaker->name }}</div> <!-- Customize display as needed -->
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @include('admin/equb.deleteEqub')
    @include('admin/equb.editEqub')

    <div class="modal modal-danger fade" id="lotteryDetailModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Reserved Lottery Detail</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="lotteryDetail"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const memberSearchField = document.getElementById('memberSearchText');
        memberSearchField.addEventListener("keydown", function(e) {
            if (e.keyCode === 13) {
                $.LoadingOverlay("show");
                searchForMember(memberSearchField.value);
            }
        });

        function searchForMember(searchInput) {
            console.log("Searching for:", searchInput);
            if (searchInput) {
                $.ajax({
                    url: "{{ url('equb/search-equb-taker') }}/" + searchInput + '/0', // Updated URL
                    type: 'get',
                    success: function(data) {
                        $('#member_table_data').html(data);
                        $.LoadingOverlay("hide");
                    }
                });
            } else {
                clearSearchEntry();
            }
        }

        function clearSearchEntry() {
            $.LoadingOverlay("show");
            document.getElementById('memberSearchText').value = "";
            $.ajax({
                url: "{{ url('equb/clearEqubTakerSearchEntry') }}", // Updated URL
                type: 'get',
                success: function(data) {
                    $('#member_table_data').html(data);
                    $.LoadingOverlay("hide");
                }
            });
        }

        function markAsEqubTaker(equbTakerId) {
            $.LoadingOverlay("show");
            $.ajax({
                url: "{{ url('equb/mark-as-equb-taker') }}/" + equbTakerId, // Updated endpoint
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $.LoadingOverlay("hide");
                    alert("Equb Taker marked!");
                    searchForMember(memberSearchField.value);
                },
                error: function() {
                    $.LoadingOverlay("hide");
                    alert("Error marking Equb Taker.");
                }
            });
        }

        $(function() {
            $.LoadingOverlay("show");
            $.ajax({
                url: "{{ url('equb/show-equb-taker') }}/0/1", // Updated URL
                type: 'get',
                success: function(data) {
                    $('#member_table_data').html(data);
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>
@endsection