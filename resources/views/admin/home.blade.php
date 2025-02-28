
    @extends('layouts.app')
    @section('content')
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="lable" value="{{ $lables }}" />
            {{-- {{$lables}} --}}
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        @can('view dashboard_members')
                        <div class="col-md-4">
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
                        @endcan
                        @can('view dashboard_members')
                            <div class="col-md-4">
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
                        @endcan
                        @can('view dashboard_summary')
                        <div class="col-md-4">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($totalEqubPayment) }} ETB</h3>
                                    <p>Summary</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        @endcan
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @can('view dashboard_daily_summary')
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
                            </div>
                            @endcan
                            @can('view dashboard_projection_equb_type')
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
                                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;">
                                        </canvas>
                                    </div>
                                </div>
                            </div>
                            
                            @endcan
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Top 5 Most Used Features</h3>
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
                                        <canvas id="featureUsageChart">
                                        </canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @can('view dashboard_all_projection')
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">Projection</h3>
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
                                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;">
                                        </canvas>
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @can('view dashboard_lottery_winner')
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
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tudayPaidMember as $item)
                                                <tr>
                                                    <td>{{ $item->member->full_name }}</td>
                                                    <td>{{ $item->member->phone }}</td>
                                                    <td>{{ $item->member->gender }}</td>
                                                    <td>Manual</td>

                                                </tr>
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
                                                {{-- {{ $automaticMembersArray }} --}}
                                            @foreach ($automaticMembersArray as $item)
                                                <tr>
                                                    <td>{{ $item['full_name'] }}</td>
                                                    <td>{{ $item['phone'] }}</td>
                                                    <td>{{ $item['gender'] }}</td>
                                                    <td>Automatic</td>

                                                </tr>
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
                            </div>
                            @endcan
                        </div>
                        
                    </div>
                </div>
            </section>
        </div>
    @endSection
    @section('scripts')
        <script>
            var ctx = document.getElementById('featureUsageChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json($topFeatures->pluck('page')),
                    datasets: [{
                        label: 'Feature Usage',
                        data: @json($topFeatures->pluck('visits')),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            })
            $(function() {
                $('#settingNava').addClass('menu-is-opening menu-open');
                $('#dashboard').addClass('active');
                $('#mainDash').addClass('active');
                var label = document.getElementById('lable').value;
                var labelOpenBracket = label.replace('[', "");
                var replaced = labelOpenBracket.replace(']', "");
                var replacedd = replaced.replace('"', "");
                var replaceddd = replacedd.replace('","', ",");
                var replacedddd = replaceddd.replace('","', ",");
                var replaceddddd = replacedddd.replace('"', "");
                var equbPaymentsArr = replaceddddd.split(',');
                var barChartCanvas = $('#barChart').get(0).getContext('2d')

                var barChartData = {
                    labels: equbPaymentsArr,
                    datasets: [{
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
                            pointsummaryChartRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: {{ $Expected }}
                        },
                    ]
                }
                // Projection for each equb type
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
                // Projection
                var summaryChartCanvas = $('#summaryChart').get(0).getContext('2d')

                var summaryChartData = {
                    labels: ['Weekly', 'Monthly', 'Yearly'],
                    datasets: [{
                            label: 'Paid',
                            backgroundColor: 'rgba(60,141,188,0.9)',
                            borderColor: 'rgba(60,141,188,0.8)',
                            pointRadius: false,
                            pointColor: '#3b8bba',
                            pointStrokeColor: 'rgba(60,141,188,1)',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(60,141,188,1)',
                            data: [{{ $weeklyPaidAmount }}, {{ $monthlyPaidAmount }},
                                {{ $yearlyPaidAmount }}
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
                            data: [{{ $weeklyExpected }}, {{ $monthlyExpected }}, {{ $yearlyExpected }}]
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
                // Daily Summary
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
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieData,
                    options: pieOptions
                })
            })
        </script>
        {{-- <script>
            
            $(function () {
                // Add active classes to navigation
                $('#settingNava').addClass('menu-is-opening menu-open');
                $('#dashboard').addClass('active');
                $('#mainDash').addClass('active');
                var label = document.getElementById('lable').value;
                var labelOpenBracket = label.replace('[', "");
                var replaced = labelOpenBracket.replace('[', "");
                var replacedd = replaced.replace('"', "");
                var replaceddd = replacedd.replace('","', ",");
                var replacedddd = replaceddd.replace('","', ",");
                var replaceddddd = replacedddd.replace('"', "");
                var equbPaymentsArr = replaceddddd.split(',');
        
                // Bar Chart Data
                const barChartLabels = @json($lables); // Labels for equb types
                const barChartPaid = @json($fullPaidAmount); // Paid amounts for equb types
                const barChartExpected = @json($Expected); // Expected amounts for equb types
                const barChartUnpiad = @json($fullUnPaidAmount);

                console.log('lables', barChartLabels)
                console.log('expected', barChartExpected)
                console.log('paid', barChartPaid)

                const barChartData = {
                    labels: barChartLabels,
                    datasets: [
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
                            backgroundColor: 'rgba(80, 214, 222, 0.9)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            pointsummaryChartRadius: false,
                            pointColor: 'rgba(210, 214, 222, 1)',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: {{$Expected}}
                        },
                        {
                            label: 'Unpaid',
                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            data: {{ $fullUnPaidAmount }}
                        }
                    ]
                };
        
                // Bar Chart Initialization
                const barChartCanvas = document.getElementById('barChart').getContext('2d');
                new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false } },
                            y: { grid: { display: false } }
                        }
                    }
                });
        
                // Summary Chart Data
                const summaryChartLabels = ['Weekly', 'Monthly', 'Yearly'];
                const summaryChartPaid = [@json($weeklyPaidAmount), @json($monthlyPaidAmount), @json($yearlyPaidAmount)];
                const summaryChartExpected = [@json($weeklyExpected), @json($monthlyExpected), @json($yearlyExpected)];
        
                const summaryChartData = {
                    labels: summaryChartLabels,
                    datasets: [
                        {
                            label: 'Paid',
                            backgroundColor: 'rgba(60,141,188,0.9)',
                            borderColor: 'rgba(60,141,188,0.8)',
                            data: summaryChartPaid
                        },
                        {
                            label: 'Expected',
                            backgroundColor: 'rgba(80, 214, 222, 0.9)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            data: summaryChartExpected
                        }
                    ]
                };
        
                // Summary Chart Initialization
                const summaryChartCanvas = document.getElementById('summaryChart').getContext('2d');
                new Chart(summaryChartCanvas, {
                    type: 'bar',
                    data: summaryChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false } },
                            y: { grid: { display: false } }
                        }
                    }
                });
        
                // Pie Chart Data (Daily Summary)
                const pieChartLabels = ['Paid', 'Unpaid', 'Expected'];
                const pieChartData = [@json($daylyPaidAmount), @json($daylyUnpaidAmount), @json($daylyExpected)];
        
                const pieChartDataConfig = {
                    labels: pieChartLabels,
                    datasets: [
                        {
                            data: pieChartData,
                            backgroundColor: ['#00a65a', '#f56954', '#f39c12']
                        }
                    ]
                };
        
                // Pie Chart Initialization
                const pieChartCanvas = document.getElementById('pieChart').getContext('2d');
                new Chart(pieChartCanvas, {
                    type: 'pie',
                    data: pieChartDataConfig,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        </script> --}}
@endSection
