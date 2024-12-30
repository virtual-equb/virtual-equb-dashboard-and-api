@can('view report')
               <?php
            //    dd($collecters);
               $total_amount = 0;
               $total_credit = 0;
               $total_balance = 0;
               ?>
               <table id="payment-table" class="table table-bordered table-striped ">
                   <thead>
                       <tr>
                           <th>No</th>
                           <th>Member</th>
                           <th>Equb Type</th>
                           <th>Payment Type</th>
                           <th>Amount</th>
                           <th>Credit</th>
                           <th>Balance</th>
                           <th>Status</th>
                           <th>Payment Date</th>
                       </tr>
                   </thead>
                   <tbody>
                    {{-- {{'test'}} --}}
                       @foreach ($collecters as $key => $item)
                           <tr id="trm{{ $item['id'] }}">
                               <td>{{ $offset + $key + 1 }}</td>
                               <td> {{ $item->full_name }}</td>
                               <td> {{ $item->name }} - Round {{ $item->round }}</td>
                               <td> {{ $item->payment_type }}</td>
                               <td> {{ number_format($item->amount) }}</td>
                               <td> {{ number_format($item->creadit) }}</td>
                               <td> {{ number_format($item->balance) }}</td>
                               <td> {{ $item->status }}</td>
                               <td>
                                   <?php
                                   $toCreatedAt = new DateTime($item['created_at']);
                                   $createdDate = $toCreatedAt->format('M-j-Y');
                                   echo $createdDate; ?>
                               </td>
                           </tr>
                           <?php
                           $total_amount = $total_amount + $item->amount;
                           $total_credit = $total_credit + $item->creadit;
                           $total_balance = $total_balance + $item->balance;
                           ?>
                       @endforeach
                   </tbody>
                   <tr>
                       <th colspan="4">Total</th>
                       <td colspan="1">{{ number_format($total_amount) }}</td>
                       <td colspan="1">{{ number_format($total_credit) }}</td>
                       <td colspan="1">{{ number_format($total_balance) }}</td>
                       <td colspan="2"></td>
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
                                       onclick="payments({{ $offset - $limit }},{{ $pageNumber - 1 }})"
                                       aria-label="Previous">
                                       <span aria-hidden="true">&laquo;</span>
                                       <span class="sr-only">Previous</span>
                                   </a>
                               </li>
                           @endif
                           @if ($pageNumber > 3)
                               <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                               </li>
                           @endif
                           @if ($pageNumber > 2)
                               <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                               </li>
                           @endif
                           @if ($pageNumber > 1)
                               <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                               </li>
                           @endif

                           <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                                   <span class="sr-only">(current)</span></a></li>

                           @if ($offset + $limit < $totalPayments)
                               <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                               </li>
                           @endif
                           @if ($offset + 2 * $limit < $totalPayments)
                               <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                               </li>
                           @endif
                           @if ($offset + 3 * $limit < $totalPayments)
                               <li class="page-item"><a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                               </li>
                           @endif

                           @if ($offset + $limit == $totalPayments || $offset + $limit > $totalPayments)
                               <li class="page-item disabled">
                                   <a class="page-link" href="javascript:void(0);" tabindex="-1">
                                       <span aria-hidden="true">&raquo;</span>
                                       <span class="sr-only">Next</span>
                                   </a>
                               </li>
                           @else
                               <li class="page-item">
                                   <a class="page-link" href="javascript:void(0);"
                                       onclick="payments({{ $offset + $limit }},{{ $pageNumber + 1 }})"
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
                   $(function() {
                       $("#payment-table").DataTable({
                           "responsive": false,
                           "lengthChange": false,
                           "searching": true,
                           "paging": false,
                           "autoWidth": false,
                           language: {
                               search: "",
                               searchPlaceholder: "Search",
                           },
                           @can('export reports_data')
                           "buttons": ["excel", "pdf", "print", "colvis"]
                           @else
                           "buttons": []
                           @endcan
                       }).buttons().container().appendTo('#payment-table_wrapper .col-md-6:eq(0)');
                   });
               </script>
@endcan
