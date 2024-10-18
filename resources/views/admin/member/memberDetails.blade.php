@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'customer_service' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'finance' ||
        Auth::user()->role == 'it')
    <table id="equb-list-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th></th>
                <th>No</th>
                <th style="width: 150px">Type</th>
                <th>Amt</th>
                <th>Start</th>
                <th>End</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Expected</th>
                <th>Lottery Date</th>
                <th>Till Lottery</th>
                <th>Till End</th>
                <th>Status</th>
                <th style="width: 50px">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($member->equbs as $key => $equb)
                <tr id="tre{{ $equb['id'] }}">
                    <?php
                    // dd($equb);
                    $totalPpayment = App\Models\Payment::where('equb_id', $equb['id'])
                        ->where('status', 'paid')
                        ->sum('amount');
                    $totalEqubAmount = App\Models\Equb
                        // ::where('status', 'Active')->
                        ::select('total_amount')
                        ->where('id', $equb['id'])
                        ->pluck('total_amount')
                        ->first();
                    $remainingPayment = $totalEqubAmount - $totalPpayment;
                    $lotteryDates = App\Models\Equb::where('status', 'Active')
                        ->where('id', $equb['id'])
                        ->pluck('lottery_date')
                        ->first();
                    $equbType = App\Models\EqubType::where('id', $equb->equb_type_id)->first();
                    $endDate = App\Models\Equb::where('status', 'Active')
                        ->where('id', $equb['id'])
                        ->pluck('end_date')
                        ->first();
                    $lotteryDates = explode(',', $lotteryDates);
                    $lotteryDate = $lotteryDates[0];
                    foreach ($lotteryDates as $lottery) {
                        $date1 = Carbon\Carbon::parse($lottery);
                        $date2 = Carbon\Carbon::parse($lotteryDate);
                        if ($date1->greaterThan($date2)) {
                            $lotteryDate = $lottery;
                        }
                    }
                    $date = date('Y-m-d');
                    $date1 = new DateTime($date);
                    $date2 = new DateTime($lotteryDate);
                    $typeDate2 = new DateTime($equbType->lottery_date);
                    $date3 = new DateTime($endDate);
                    if ($date2 > $date1) {
                        $interval = $date2->diff($date1);
                        $interval = $interval->days;
                        // dd($interval, $date2);
                    } elseif ($date2 == $date1) {
                        $interval = 0;
                    } else {
                        $interval = 'passed';
                    }
                    if ($typeDate2 > $date1) {
                        $typeInterval = $typeDate2->diff($date1);
                        $typeInterval = $typeInterval->days;
                    } elseif ($typeDate2 == $date1) {
                        $typeInterval = 0;
                    } else {
                        $typeInterval = 'passed';
                    }
                    if ($date3 > $date1) {
                        $endDateInterval = $date3->diff($date1);
                        $endDateInterval = $endDateInterval->days;
                    } elseif ($date3 == $date1) {
                        $endDateInterval = 0;
                    } else {
                        $endDateInterval = 'passed';
                    }
                    // dd($equb->lottery_date != null);
                    $finalLotteryInterval = $equbType->type == 'Automatic' ? $typeInterval : ($equb->lottery_date != null ? $interval : 'Unassigned');
                   ?>
                    <td class="details-control_payment" id="{{ $equb['id'] }}"></td>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <a href="javascript:void(0);"
                            onclick="openPaymentTab({{ $equb }})">{{ $equb->equbType->name }} round
                            {{ $equb->equbType->round }}</a>
                    </td>
                    <td> {{ number_format($equb->amount) }}</td>
                    <td>
                        <?php
                        $toCreatedAt = new DateTime($equb['start_date']);
                        $createdDate = $toCreatedAt->format('M-j-Y');
                        echo $createdDate; ?>
                    </td>
                    <td>
                        <?php
                        $toCreatedAt = new DateTime($equb['end_date']);
                        $createdDate = $toCreatedAt->format('M-j-Y');
                        echo $createdDate; ?>
                    </td>
                    <td> {{ number_format($totalPpayment) }}</td>
                    <td> {{ number_format($remainingPayment) }}</td>
                    <td> {{ number_format($equb->total_amount) }}</td>

                    <td>
                        <?php
                        if ($equb->lottery_date !== null) {
                            if ($equbType->type == 'Automatic') {
                                $toCreatedAt = new DateTime($equbType->lottery_date);
                                $createdDate = $toCreatedAt->format('M-j-Y');
                                echo $createdDate;
                            } else {
                                foreach (explode(',', $equb->lottery_date) as $lottery_date) {
                                    $toCreatedAt = new DateTime($lottery_date);
                                    $createdDate = $toCreatedAt->format('M-j-Y');
                                    echo $createdDate;
                                    echo '<br>';
                                }
                            }
                        } else {
                            echo 'Unassigned';
                        }
                        ?>
                    </td>
                    <td> {{ $finalLotteryInterval != 'passed' ? ($finalLotteryInterval != 'Unassigned' ? $finalLotteryInterval . ' Days' : 'Unassigned') : 'Passed' }}
                    </td>
                    <td> {{ $endDateInterval != 'passed' ? $endDateInterval . ' Days' : 'Passed' }}</td>
                    <td> {{ $equb->status }}</td>
                    <?php
                    $equbTakers = $equb->equb_takers;
                    if (!empty($equbTakers)) {
                        $indexE = sizeof($equbTakers) - 1;
                    } else {
                        $indexE = 0;
                    }
                    $expectedTotal = $equb->total_amount;
                    $p = $equb->payments;
                    if (!empty($p)) {
                        $indexP = sizeof($p);
                    } else {
                        $indexP = 0;
                    }

                    $sum = 0;
                    for ($i = 0; $i < $indexP; $i++) {
                        if ($p[$i]->status == 'paid' || $p[$i]->status == 'pending') {
                            $sum = $sum + $p[$i]->amount;
                        }
                    }
                    $equbTakers = $equb->equb_takers;
                    if (!empty($equbTakers)) {
                        $remainingAmount = $equb->equb_takers[$indexE]->remaining_amount;
                    } else {
                        $remainingAmount = $equb->total_amount;
                    }

                    ?>
                    @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')
                        <td>
                            <div class='dropdown'>
                                <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                    data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                <ul class='dropdown-menu p-4'>
                                    {{-- <li>
                                    <a href="javascript:void(0);"
                                        class="text-secondary btn btn-flat"
                                        onclick="addUnpaid({{ $equb }})" id="addUnpaidButton"><i
                                            class="fas fa-plus-circle"></i> Add Unpaid</a>
                                </li> --}}
                                    @if (Auth::user()->role != 'finance')
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status == 'Deactive' ? 'disabled' : ($sum >= $expectedTotal ? 'disabled' : '')) }}"
                                                onclick="openPaymentModal({{ $equb }})" id="paymentButton"><i
                                                    class="fas fa-plus-circle"></i> Payment</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status == 'Deactive' ? 'disabled' : ($remainingAmount == 0 ? 'disabled' : '')) }}"
                                                id="lotteryPaymentButton"
                                                onclick="openLotteryModal({{ $equb }})"><i
                                                    class="fas fa-plus-circle"></i> Lottery</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status == 'Deactive' ? 'disabled' : ($remainingAmount == 0 && $sum == $expectedTotal ? 'disabled' : '')) }}"
                                                onclick="openEqubEditModal({{ $equb }})"
                                                style="margin-right:10px;"id="paymentEdit"><span class="fa fa-edit">
                                                </span>
                                                Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status == 'Deactive' ? 'disabled' : ($remainingAmount == 0 && $sum == $expectedTotal ? 'disabled' : '')) }}"
                                                onclick="openEqubDeleteModal({{ $equb }})"
                                                id="paymentDelete"><i class="fas fa-trash-alt"></i> Delete</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="equbStatusChange({{ $equb }})"
                                                style="margin-right:10px;" id="statuss" name="statuss"><i
                                                    class="fab fa-shopware"></i>
                                                <?php if ($equb->status == 'Active') {
                                                    echo 'Deactivate';
                                                } else {
                                                    echo 'Activate';
                                                }
                                                ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="equbDrawCheckChange({{ $equb }})"
                                                style="margin-right:10px;" id="drawCheck" name="drawCheck"><i
                                                    class="fab fa-shopware"></i>
                                                <?php if ($equb->check_for_draw) {
                                                    echo 'Deactivate For Draw';
                                                } else {
                                                    echo 'Activate For Draw';
                                                }
                                                ?>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    <!-- </div> -->
    <div class="modal modal-danger fade" id="aaaa" tabindex="-1" role="dialog" aria-labelledby="Delete"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Update equb type status</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="bbbb">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <p class="text-center">Are you sure you want to update status?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-danger">update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal modal-danger fade" id="checkForEqubModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Update equb type status</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" id="checkForDrawForm">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <p class="text-center">Are you sure you want to update status?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-danger">update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function equbStatusChange(item) {
            $('#aaaa').modal('show');
            $('#bbbb').attr('action', "{{ url('member/equbStatus-update') }}" + '/' + item.id);
        }

        function equbDrawCheckChange(item) {
            $('#checkForEqubModal').modal('show');
            $('#checkForDrawForm').attr('action', "{{ url('member/equb-check-for-draw-update') }}" + '/' + item.id);
        }
        $("#bbbb").submit(function() {
            $.LoadingOverlay("show");
        });

        function addUnpaid(item) {
            $.ajax({
                url: "{{ url('member/add-unpaid') }}" + '/' + item.id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                success: function(data) {

                },
                error: function() {}
            });
            $('#addUnpaidButton').attr('action', "{{ url('member/add-unpaid') }}" + '/' + item.id);
        }
        $(function() {
            if ($.fn.DataTable.isDataTable('#equb-list-table')) {
                $('#equb-list-table').DataTable().destroy();
            }
            var table = $("#equb-list-table").DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": false,
                "autoWidth": false,
                "bSort": false,
                "bDestroy": true,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                }
            });
            table.buttons().container().appendTo('#equb-list-table_wrapper .col-md-6:eq(0)');
            $('#equb-list-table tbody').on('click', 'td.details-control_payment', function() {
                var tr = $(this).closest('tr');
                var inputId = $(this).prop("id");
                var row = table.row(tr);
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(loadHere).show();
                    $.ajax({
                        url: "{{ url('member/show-equb') }}" + '/' + inputId,
                        type: 'get',
                        success: function(data) {
                            row.child(data).show();
                            row.child.show();
                            tr.addClass('shown');

                        },
                        error: function() {}
                    });
                }
            });
        });
    </script>
@endif
