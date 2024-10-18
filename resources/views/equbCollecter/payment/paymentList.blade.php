@if (Auth::user()->role == 'equb_collector')
             <?php
                 $total_amount=0;
                 $total_credit=0;
                 $total_balance=0;
                ?>
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
        @foreach($payments as $key => $payment)
             <?php $user = App\Models\User::where('id', $payment['collecter'])->pluck('name')->first(); ?>
                <td>{{$key+1}}</td>
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
                 "buttons": ["excel", "pdf", "print", "colvis"]
            });
             table.buttons().container().appendTo('#payment-list-table_in_tab_wrapper .col-md-6:eq(0)');
         });
    </script>
    @endif
