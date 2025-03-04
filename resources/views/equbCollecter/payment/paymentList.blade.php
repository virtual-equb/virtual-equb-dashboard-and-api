@can('view payment')
    <?php
        $total_amount=0;
        $total_credit=0;
        $total_balance=0;
    ?>
    <div id="payment_list_table_data" class="table-responsive">
        <table id="payment-list-table_in_tab" class="table table-bordered table-striped" >
            <thead>
                <tr>
                <th colspan="2">Full Name</th>
                <th colspan="2">Phone</th>
                <th colspan="2">Gender</th>
                <th colspan="2">Equb Type</th>
                </tr>
                <tr style="background-color: rgb(242 242 242);">
                    <td colspan="2">{{$member->full_name}}</td>
                    <td colspan="2">{{$member->phone}}</td>
                    <td colspan="2">{{$member->gender}}</td>
                    <td colspan="2">{{$equb->equbType->name}}</td>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Credit</th>
                    <th>Balance</th>
                    <th>Collecter</th>
                    <th>Payment Date</th>
                    <th style="width: 50px">Action</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                @foreach($payments as $index => $payment)
                    <?php $user = App\Models\User::where('id', $payment['collecter'])->pluck('name')->first(); ?>
                        <td>{{ $loop->iteration }}</td>
                        <td> {{$payment->payment_type}}</td>
                        <td> {{number_format($payment->amount)}}</td>
                        <td> {{number_format($payment->creadit)}}</td>
                        <td> {{number_format($payment->balance)}}</td>
                        <td> {{$user}}</td>
                        <td>
                            <?php
                            $toCreatedAt= new DateTime($payment['created_at']);
                            $createdDate = $toCreatedAt->format("M-j-Y");
                            echo $createdDate;?>
                        </td>
                        <td><a href="javascript:void(0);" class="btn btn-secondary {{$member->status != 'Active' ? 'disabled' : ($payment->equb->status == 'Deactive' ? 'disabled' : (($payment->equb->total_amount <= $totalPaid) ? 'disabled' : '' ))}}"  onclick="openPaymentEditModal({{$payment}})" id="editButton"><span class="fa fa-edit"> </span> Edit</a></td>
                </tr>
                <?php
                        $total_amount=$total_amount+$payment->amount;
                        $total_credit=$total_credit+$payment->creadit;
                        $total_balance=$total_balance+$payment->balance;
                        ?>
                @endforeach
            </tbody>
            <tr>
                <th colspan="2">Total</th>
                <td colspan="1">{{number_format($totalPaid)}}</td>
                <td colspan="1">{{number_format($total_credit)}}</td>
                <td colspan="1">{{number_format($total_balance)}}</td>
                <td colspan="3"></td>
            </tr>
        </table>
    </div>
    
    <script>
        $(function () {
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
         });
    </script>
    @endcan
