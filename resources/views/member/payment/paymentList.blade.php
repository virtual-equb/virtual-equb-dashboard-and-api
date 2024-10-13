             <?php
                 $total_amount=0;
                 $total_credit=0;
                 $total_balance=0;
                ?>
 <!-- <div class="table-responsive">  -->
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
                        @foreach($payments as $key => $payment)
                        <tr id="tr{{$payment['id']}}">
                            <?php $user = App\Models\User::where('id', $payment['collecter'])->pluck('name')->first(); ?>
                                <input type="hidden" name="detailValue" id="detailValue{{ $payment['id'] }}"
                                value="{{ $payment }}" />
                                <td class="details-control_payment" id="{{ $payment['id'] }}"></td>
                                <td>{{$offset+$key+1}}</td>
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
             <!-- </div> -->
              <div class="justify-content-end">
                <nav aria-label="Page navigation" id="paginationDiv">
                    <ul class="pagination">

                        @if ($offset == 0 || $offset < 0)
                            <li class="page-item disabled">
                                <a class="page-link" href="javascript:void(0);" tabindex="-1">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset - $limit }},{{ $pageNumber - 1 }})"
                                    aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        @endif
                        @if ($pageNumber > 3)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                            </li>
                        @endif
                        @if ($pageNumber > 2)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                            </li>
                        @endif
                        @if ($pageNumber > 1)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                            </li>
                        @endif

                        <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                                <span class="sr-only">(current)</span></a></li>

                        @if ($offset + $limit < $total)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                            </li>
                        @endif
                        @if ($offset + 2 * $limit < $total)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                            </li>
                        @endif
                        @if ($offset + 3 * $limit < $total)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                    onclick="members({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                            </li>
                        @endif

                        @if ($offset + $limit == $total || $offset + $limit > $total)
                            <li class="page-item disabled">
                                <a class="page-link" href="javascript:void(0);" tabindex="-1">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0);"
                                    onclick="payments({{$equb}},{{ $offset + $limit }},{{ $pageNumber + 1 }})"
                                    aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </nav>
            </div>
    <script>
        $(function () {
             var table=$("#payment-list-table_in_tab").DataTable({
              "responsive": false, "lengthChange": false,"searching": true,"paging":false, "autoWidth": false,
              language: {
                search: "",
                searchPlaceholder: "Search",},
              // "buttons": ["excel", "pdf", "print", "colvis"]
            });
             table.buttons().container().appendTo('#payment-list-table_in_tab_wrapper .col-md-6:eq(0)');
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

