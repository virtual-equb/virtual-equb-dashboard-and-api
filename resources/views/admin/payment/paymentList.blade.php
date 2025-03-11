    <?php
        $total_amount = 0;
        $total_credit = 0;
        $total_balance = 0;
    ?>
    <div id="payment_list_table_data" class="table-responsive">
        <table id="payment-list-table_in_tab" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th colspan="2">Full Name</th>
                    <th colspan="2">Phone</th>
                    <th colspan="2">Gender</th>
                    <th colspan="3">Equb Type</th>
                </tr>
                <tr style="background-color: rgb(242 242 242);">
                    <td colspan="2">{{ $member->full_name }}</td>
                    <td colspan="2">{{ $member->phone }}</td>
                    <td colspan="2">{{ $member->gender }}</td>
                    <td colspan="3">{{ $equb->equbType->name }}</td>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Payment Type</th>
                    <th>Paid Amt.</th>
                    <th>Credit</th>
                    <th>Balance</th>
                    <th>Collecter</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th style="width: 50px">Action</th>

                </tr>
            </thead>

            <tbody>
                <tr>
                    @foreach ($payments as $index => $payment)
                        <?php $user = App\Models\User::where('id', $payment['collecter'])->pluck('name')->first(); ?>
                        <td>{{ $loop->iteration }}</td>
                        <td> {{ $payment->payment_type }}</td>
                        <td> {{ number_format($payment->amount) }}</td>
                        <td> {{ number_format($payment->creadit) }}</td>
                        <td> {{ number_format($payment->balance) }}</td>
                        <td> {{ $user }}</td>
                        <td> {{ $payment->status }}</td>
                        <td>
                            <?php
                            $toCreatedAt = new DateTime($payment['created_at']);
                            $createdDate = $toCreatedAt->format('M-j-Y');
                            echo $createdDate; ?>
                        </td>
                        <td>
                            <div class='dropdown'>
                                <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                    data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                <ul class='dropdown-menu dropdown-menu-right p-4'>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($payment->equb->status == 'Deactive' ? 'disabled' : ($payment->equb->total_amount <= $totalPaid ? 'disabled' : '')) }}
                                                        "
                                            onclick="openPaymentEditModal({{ $payment }})" id="editButton"><span
                                                class="fa fa-edit"> </span> Edit</a>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="text-secondary btn btn-flat {{ $member->status == 'Deactive' ? 'disabled' : ($payment->equb->status == 'Deactive' ? 'disabled' : ($payment->equb->total_amount <= $totalPaid ? 'disabled' : '')) }} "
                                            onclick="openDeletePaymentModal({{ $payment }})" id="deleteButton"><i
                                                class="fas fa-trash"></i> Delete</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                            onclick="showPaymentProofModal({{ $payment }})" id="showProof"><i
                                                class="fas fa-image"></i> Show Proof</a>
                                    </li>
                                    @if ($payment->status == 'pending')
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="approvePayment({{ $payment }})" id="showProof"><i
                                                    class="fas fa-check"></i> Approve</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="rejectPayment({{ $payment }})" id="showProof"><i
                                                    class="fas fa-times"></i> Reject</a>
                                        </li>
                                    @elseif ($payment->status == 'unpaid')
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="approvePayment({{ $payment }})" id="showProof"><i
                                                    class="fas fa-check"></i> Approve</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                </tr>
                <?php
                    $total_amount = $total_amount + $payment->amount;
                    $total_credit = $total_credit + $payment->creadit;
                    $total_balance = $total_balance + $payment->balance;
                ?>
                @endforeach
            </tbody>
            <tr>
                <th colspan="2">Total</th>
                <td colspan="1">{{ number_format($totalPaid) }}</td>
                <td colspan="1">{{ number_format($total_credit) }}</td>
                <td colspan="1">{{ number_format($total_balance) }}</td>
                <td colspan="4"></td>
            </tr>
        </table>
    </div>
    
<script>
    function disablePaymentHistoryButton(item) {}
    $(function() {
        $("#payment-list-table_in_tab").DataTable({
            "responsive": false,
            "lengthChange": false,
            "searching": true,
            "autoWidth": false,
            language: {
                search: "",
                searchPlaceholder: "Search",
            },
            "buttons": ["excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#payment_list_table_data .col-md-6:eq(0)');
        $("#payment-list-table_in_tab_filter").prepend(
            `<button href="javascript:void(0);" class="btn btn-primary {{ $member->status == 'Deactive' ? 'disabled' : ($equb->status == 'Deactive' ? 'disabled' : ($equb->total_amount <= $totalPaid ? 'disabled' : '')) }} " onclick="openDeleteAllPaymentModal({{ $member->id }},{{ $equb->id }})" id="deleteButton"><i class="fas fa-plus-circle"></i> Delete all Payment</button>`
        )
    });
</script>
