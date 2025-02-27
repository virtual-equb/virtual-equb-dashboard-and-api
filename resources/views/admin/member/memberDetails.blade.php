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
                    $totalPpayment = App\Models\Payment::where('equb_id', $equb['id'])->where('status', 'paid')->sum('amount');
                    $totalEqubAmount = App\Models\Equb::where('id', $equb['id'])->value('total_amount');
                    $remainingPayment = max(0, $totalEqubAmount - $totalPpayment);

                    $lotteryDates = App\Models\Equb::where('id', $equb['id'])->value('lottery_date');
                    $equbType = App\Models\EqubType::find($equb->equb_type_id);
                    $endDate = App\Models\Equb::where('id', $equb['id'])->value('end_date');

                    $lotteryDates = explode(',', $lotteryDates);
                    // $lotteryDate = $lotteryDates[0];
                    $lotteryDate = !empty($lotteryDates) ? max($lotteryDates) : null;
                    
                    $date  = date('Y-m-d');
                    $currentDate = new DateTime($date);
                    $lotteryDateObj = $lotteryDate ? new DateTime($lotteryDate) : null;
                    $endDateObj = $endDate ? new DateTime($endDate) : null;
                    $typeDateObj = new DateTime($equbType->lottery_date);

                    // Till Lottery Calculation
                    if ($lotteryDateObj && $lotteryDateObj > $currentDate) {
                        $lotteryInterval = $lotteryDateObj->diff($currentDate)->days;
                    } elseif ($lotteryDateObj && $lotteryDateObj == $currentDate) {
                        $lotteryInterval = '0';
                    } else {
                        $lotteryInterval = 'Passed';
                    }
                    // Till End Calculation
                    if ($endDateObj && $endDateObj > $currentDate) {
                        $endDateInterval = $endDateObj->diff($currentDate)->days;
                    } elseif ($endDateObj && $endDateObj == $currentDate) {
                        $endDateInterval = '0';
                    } else {
                        $endDateInterval = 'Passed';
                    }

                    // Final Lottery Interval
                    $finalLotteryInterval = ($equbType->type == 'Automatic') 
                    ? $typeDateObj->diff($currentDate)->days . ' Days'
                    : ($lotteryDate ? $lotteryInterval : 'Unassigned');
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
                        {{ date('M-j-Y', strtotime($equb['start_date'])) }}
                    </td>
                    <td>
                        {{ date('M-j-Y', strtotime($equb['end_date'])) }}
                    </td>
                    <td> {{ number_format($totalPpayment) }}</td>
                    <td> {{ number_format($remainingPayment) }}</td>
                    <td> {{ number_format($equb->total_amount) }}</td>

                    <td>
                        @if ($equb->lottery_date)
                            @foreach (explode(',', $equb->lottery_date) as $lottery_date)
                                {{ date('M-j-Y', strtotime($lottery_date))}}
                            @endforeach
                        @else 
                            Unassigned
                        @endif
                    </td>
                    <td> {{ $finalLotteryInterval != 'Passed' ? ($finalLotteryInterval != 'Unassigned' ? $finalLotteryInterval . ' Days' : 'Unassigned') : 'Passed' }}
                    </td>
                    <td> {{ $endDateInterval != 'Passed' ? $endDateInterval . ' Days' : 'Passed' }}</td>
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
                    @if (Auth::user()->role != 'operation_manager' &&
                            Auth::user()->role != 'assistant' &&
                            Auth::user()->role != 'legal_affair_officer')
                        <td>
                            <div class='dropdown'>
                                @if (Auth::user()->role != 'marketing_manager') 
                                <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>
                                    Menu <span class='caret'></span>
                                </button>
                            @else
                                <span>N/A</span>
                            @endif
                                <ul class='dropdown-menu p-4'>
                                    @if (Auth::user()->role != 'finance')
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status != 'Active' ? 'disabled' : ($sum >= $expectedTotal ? 'disabled' : '')) }}"
                                                id="paymentButton"
                                                onclick="openPaymentModal({{ $equb }})"><i
                                                    class="fas fa-plus-circle"></i> Payment</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status != 'Active' ? 'disabled' : ($remainingAmount == 0 ? 'disabled' : '')) }}"
                                                id="lotteryPaymentButton"
                                                onclick="openLotteryModal({{ $equb }})"><i
                                                    class="fas fa-plus-circle"></i> Lottery</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status != 'Active' ? 'disabled' : ($remainingAmount == 0 && $sum == $expectedTotal ? 'disabled' : '')) }}"
                                                onclick="openEqubEditModal({{ $equb }})"
                                                style="margin-right:10px;"id="paymentEdit"><span class="fa fa-edit">
                                                </span>
                                                Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"
                                                class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status != 'Active' ? 'disabled' : ($remainingAmount == 0 && $sum == $expectedTotal ? 'disabled' : '')) }}"
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
        // function equbStatusChange(item) {
        //     let newStatus = item.status === "Active" ? "Deactive" : "Active";

        //     $('#aaaa').modal('show'); // Show the modal
        //     $('#bbbb').attr('action', "{{ url('member/equbStatus-update') }}" + '/' + item.id);

        //     // Set the correct status inside a hidden input in the form
        //     $('#equbStatusInput').val(newStatus);
        // }

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
