@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'marketing_manager' ||
        Auth::user()->role == 'customer_service' ||
        Auth::user()->role == 'it')
    @extends('layouts.app')

    @section('content')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <!-- Main content -->
            <!-- <input type="hidden" id="label" value="$lables[0]"/> -->
            <input type="hidden" id="employeesData" value="{{ $lables }}" />
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalMember }}</h3>
                                    <p>Total Members</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="{{ route('showMember') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $activeMember }}</h3>

                                    <p>Active Members</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="{{ route('showMember') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <!-- <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        @foreach ($tudayPaidMember as $tudayPaidMembers)
    {{ $tudayPaidMembers->member->full_name }} <br>
    @endforeach

                        <p>Active Members</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                      </div>
                      <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div> -->
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($totalEqubPayment, 2) }} Birr</h3>

                                    <p>Summary</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <!-- PIE CHART -->
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Today's Summary</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                            <!-- BAR CHART -->
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Projection for each equb type</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <canvas id="barChart"
                                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col (LEFT) -->
                        <div class="col-md-6">

                            <!-- BAR CHART -->
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">Projection </h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <canvas id="summaryChart"
                                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                            <!-- BAR CHART -->
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Today's Lottery Winning Members</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="member-list-table" class="table table-bordered table-striped ">
                                        <thead>
                                            <tr>
                                                <th>Full name</th>
                                                <th>Phone</th>
                                                <th>Gender</th>
                                                <th>Register At </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tudayPaidMember as $item)
                                                <tr>
                                                    <td>{{ $item->member->full_name }}</td>
                                                    <td>{{ $item->member->phone }}</td>
                                                    <td>{{ $item->member->gender }}</td>
                                                    <td>
                                                        <?php
                                                        $toCreatedAt = new DateTime($item->member['created_at']);
                                                        $createdDate = $toCreatedAt->format('M j, Y');
                                                        echo $createdDate; ?>
                                                    </td>

                                                </tr>
                                                <!-- Delete Warning Modal -->
                                                <div class="modal modal-danger fade" id="deleteModal" tabindex="-1"
                                                    role="dialog" aria-labelledby="Delete" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Delete
                                                                    Member</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="" method="post" id="deleteMember">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input id="id" name="id" hidden
                                                                        value="">
                                                                    <h5 class="text-center">Are you sure you want to delete
                                                                        this member?</h5>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-sm btn-danger">Yes,
                                                                    Delete Member</button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col (RIGHT) -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
        </div>
        <!-- /.content -->
    @endSection
    @section('scripts')
        <script>
            $(function() {
                $('#nav-dashboard').addClass('menu-is-opening menu-open');
                $('#dashboard').addClass('active');
                /* ChartJS
                 * -------
                 * Here we will create a few charts using ChartJS
                 */

                var equbPayments = document.getElementById('employeesData').value;
                var equbPaymentOpenBracket = equbPayments.replace('[', "");
                var replaced = equbPaymentOpenBracket.replace(']', "");
                var replacedd = replaced.replace('"', "");
                var replaceddd = replacedd.replace('","', ",");
                var replacedddd = replaceddd.replace('","', ",");
                var replaceddddd = replacedddd.replace('"', "");
                var equbPaymentsArr = replaceddddd.split(',');


                var barChartCanvas = $('#barChart').get(0).getContext('2d')

                var barChartData = {
                    labels: equbPaymentsArr,
                    datasets: [{
                            label: 'Unpaid',
                            backgroundColor: 'rgba(210, 214, 222, 1)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            pointRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: {{ $fullUnPaidAmount }}
                        },
                        {
                            label: 'Paid',
                            backgroundColor: 'rgba(60,141,188,0.9)',
                            borderColor: 'rgba(60,141,188,0.8)',
                            pointRadius: false,
                            pointColor: '#3b8bba',
                            pointStrokeColor: 'rgba(60,141,188,1)',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(60,141,188,1)',
                            data: {{ $fullPaidAmount }}
                        },
                        {
                            label: 'Expected',
                            backgroundColor: 'rgba(80, 214, 222, 200)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            pointRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: {{ $Expected }}
                        },
                    ]
                }

                var barChartOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false,
                            }
                        }]
                    }
                }
                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $('#barChart').get(0).getContext('2d')
                var barChartData = $.extend(true, {}, barChartData)
                var temp0 = barChartData.datasets[0]
                var temp1 = barChartData.datasets[1]
                barChartData.datasets[0] = temp1
                barChartData.datasets[1] = temp0

                var barChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    datasetFill: false
                }

                new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData,
                    options: barChartOptions
                })
                // summary
                var summaryChartCanvas = $('#summaryChart').get(0).getContext('2d')

                var summaryChartData = {
                    labels: ['Weekly', 'Monthly', 'Yearly'],
                    datasets: [{
                            label: 'Unpaid',
                            backgroundColor: 'rgba(210, 214, 222, 1)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            pointRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: [{{ $weeklyUnpaidAmount }}, {{ $monthlyUnpaidAmount }},
                                {{ $monthlyUnpaidAmount }}
                            ]
                        },
                        {
                            label: 'Paid',
                            backgroundColor: 'rgba(60,141,188,0.9)',
                            borderColor: 'rgba(60,141,188,0.8)',
                            pointRadius: false,
                            pointColor: '#3b8bba',
                            pointStrokeColor: 'rgba(60,141,188,1)',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(60,141,188,1)',
                            data: [{{ $weeklyPaidAmount }}, {{ $monthlyPaidAmount }},
                                {{ $monthlyPaidAmount }}
                            ]
                        },
                        {
                            label: 'Expected',
                            backgroundColor: 'rgba(80, 214, 222, 200)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            pointRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: [{{ $weeklyExpected }}, {{ $monthlyExpected }}, {{ $monthlyExpected }}]
                        },
                    ]
                }

                var summaryChartOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false,
                            }
                        }]
                    }
                }
                //-------------
                //- summary CHART -
                //-------------
                var summaryChartCanvas = $('#summaryChart').get(0).getContext('2d')
                var summaryChartData = $.extend(true, {}, summaryChartData)
                var temp0 = summaryChartData.datasets[0]
                var temp1 = summaryChartData.datasets[1]
                summaryChartData.datasets[0] = temp1
                summaryChartData.datasets[1] = temp0

                var summaryChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    datasetFill: false
                }

                new Chart(summaryChartCanvas, {
                    type: 'bar',
                    data: summaryChartData,
                    options: summaryChartOptions
                })
                //-------------
                //- pie CHART -
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
                var pieData = {
                    labels: [
                        'Paid',
                        'Unpaid',
                        'Expected',
                    ],
                    datasets: [{
                        data: [{{ $daylyPaidAmount }}, {{ $daylyUnpaidAmount }}, {{ $daylyExpected }}],
                        backgroundColor: ['#00a65a', '#f56954', '#f39c12'],
                    }]
                }
                var pieOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                }
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieData,
                    options: pieOptions
                })

                //-------------
                //- PIE CHART -
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var pieChartCanvas = $('#pieChartweekly').get(0).getContext('2d')
                var pieDataweekly = {
                    labels: [
                        'Paid',
                        'Unpaid',
                        'Expected',
                    ],
                    datasets: [{
                        data: [{{ $weeklyPaidAmount }}, {{ $weeklyUnpaidAmount }},
                            {{ $weeklyExpected }}
                        ],
                        backgroundColor: ['#00a65a', '#f56954', '#f39c12'],
                    }]
                }
                var pieOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                }
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieDataweekly,
                    options: pieOptions
                })
                //-------------
                //- PIE CHART -
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var pieChartCanvas = $('#pieChartmonthly').get(0).getContext('2d')
                var pieDataweekly = {
                    labels: [
                        'Paid',
                        'Unpaid',
                        'Expected',
                    ],
                    datasets: [{
                        data: [{{ $monthlyPaidAmount }}, {{ $monthlyUnpaidAmount }},
                            {{ $monthlyExpected }}
                        ],
                        backgroundColor: ['#00a65a', '#f56954', '#f39c12'],
                    }]
                }
                var pieOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                }
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieDataweekly,
                    options: pieOptions
                })

                //-------------
                //- PIE CHART -
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var pieChartCanvas = $('#pieChartYearly').get(0).getContext('2d')
                var pieDataweekly = {
                    labels: [
                        'Paid',
                        'Unpaid',
                        'Expected',
                    ],
                    datasets: [{
                        data: [{{ $yearlyPaidAmount }}, {{ $yearlyUnpaidAmount }},
                            {{ $yearlyExpected }}
                        ],
                        backgroundColor: ['#00a65a', '#f56954', '#f39c12'],
                    }]
                }
                var pieOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                }
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieDataweekly,
                    options: pieOptions
                })
            })
        </script>
    @endSection
@endif
