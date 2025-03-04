    <?php
        $total_amount=0;
        $total_credit=0;
        $total_balance=0;
    ?>
    <div id="payment_list_table_data" class="table-responsive">
        <table id="payment-list-table_in_tab" class="table table-bordered table-striped" >
            <thead>
                <tr>
                    <td></td>
                    <th>No</th>
                    <th>Payment Date</th>
                    <th>Paid Amt.</th>
                </tr>
            </thead>

            <tbody>
                @foreach($payments as $index => $payment)
                <tr id="tr{{$payment['id']}}">
                    <?php $user = App\Models\User::where('id', $payment['collecter'])->pluck('name')->first(); ?>
                        <input type="hidden" name="detailValue" id="detailValue{{ $payment['id'] }}"
                        value="{{ $payment }}" />
                        <td class="details-control_payment" id="{{ $payment['id'] }}"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <?php
                            $toCreatedAt= new DateTime($payment['created_at']);
                            $createdDate = $toCreatedAt->format("M-j-Y");
                            echo $createdDate;?>
                        </td>
                        <td> {{number_format($payment->amount)}}</td>
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
                    <th></th>
                    <td colspan="1">{{number_format($totalPaid)}}</td>
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
                searchPlaceholder: "Search",},
              "buttons": ["excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#payment_list_table_data .col-md-6:eq(0)');

            $('#payment-list-table_in_tab').on('click', 'td.details-control_payment', function() {
                    var tr = $(this).closest('tr');
                    var inputId = $(this).prop("id");
                    var createdBy = '';
                    var detailDatas = JSON.parse((document.getElementById("detailValue" + inputId).value));
                    var row = table.row(tr);
                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                    row.child(loadHere).show();
                                row.child(viewAvailableDetails(detailDatas)).show();
                                row.child.show();
                                tr.addClass('shown');
                    }
                });
            });

            function viewAvailableDetails(d) {
                var skill = null;
                var learner = null;
                if (d.skill) {
                    skill = d.skill;
                } else {
                    skill = "";
                };
                if (d.is_learner) {
                    learner = "Still Studying";
                } else {
                    learner = "Dont Take Classes";
                };

                datas =
                    '<table cellpadding="5" cellspacing="0" border="0" class="p-0 ml-5 bg-white" id="paymentDetails' +
                    d.id + '"' +
                    '<tr>' +
                    '<th>Payment Type</th>' +
                    '<td>' + d.payment_type+'</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Total Credit</th>' +
                    '<td>' + '{{number_format($totalCredit)}}'+'</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Total Balance</th>' +
                    '<td>' + '{{number_format($total_balance)}}'+'</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Credit</th>' +
                    '<td>' + d.creadit+'</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Balance</th>' +
                    '<td>' + d.balance+'</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<th>Collecter</th>' +
                    '<td>' + '{{$user}}'+'</td>' +
                    '</tr>' +
                    '<tr>' +
                    '</table>';
                    return datas;
        }
    </script>