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

        div.dataTables_wrapper div.dataTables_info {
            padding-top: 0.85em;
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalEqubTaker }}</h3>
                                    <p>Total Equb Taker</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Equb Taker</h5>
                                </div>
                                <div class="card-body">
                                    <div id="equb_taker_table_data" class="table-responsive">
                                        <table id="equbTakerTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Equb Type</th>
                                                    <th>status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($equbTakers as $index => $taker)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $taker->member->full_name }}</td>
                                                        <td>{{ $taker->member->email }}</td>
                                                        <td>{{ $taker->member->phone }}</td>
                                                        <td>{{ $taker->equb->equbType->name ?? 'N/A'}}</td>
                                                        <td>{{ $taker->status }}</td>
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
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#equbTakerTable').DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": true,
                "autoWidth": false,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                },
                "buttons": ["excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#equb_taker_table_data .col-md-6:eq(0)');
        });
    </script>
@endsection