@extends('layouts.app')

@section('styles')
    <style type="text/css">
        td.details-control_equb, td.details-control_payment {
            background: url("{{ url('images/plus20.webp') }}") no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control_equb, tr.shown td.details-control_payment {
            background: url("{{ url('images/minus20.webp') }}") no-repeat center center;
        }
        .form-group.required .control-label:after { content: "*"; color: red; }
        div.dataTables_wrapper div.dataTables_paginate, div.dataTables_wrapper div.dataTables_info { display: none; }
        @media (max-width: 768px) {
            .addMember, .checkLottery, .search, .clear, .searchandClear, .checkLotteryandAddMember, .paymentTab, .memberTab {
                width: 100%; margin-bottom: 20px;
            }
            .searchEqubandClear { width: 30%; }
        }
        @media (max-width: 575.98px) {
            #payment-list-table_in_tab { display: block; width: 100%; overflow-x: auto; }
            .table-responsive-sm > .table-bordered { border: 0; }
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
                                            <table id="equbTakerTable" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Equb Type</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($equbTakers as $index => $taker)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $taker->name }}</td>
                                                            <td>{{ $taker->email }}</td>
                                                            <td>{{ $taker->phone }}</td>
                                                            <td>{{ $taker->equb_type }}</td>
                                                            <td>
                                                                <a href="" class="btn btn-info btn-sm">View</a>
                                                                <a href="" class="btn btn-warning btn-sm">Edit</a>
                                                                <form action="" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#equbTakerTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "zeroRecords": "No matching records found",
                    "lengthMenu": "Display _MENU_ records",
                    "search": "Search:",
                }
            });
        });
    </script>
@endsection