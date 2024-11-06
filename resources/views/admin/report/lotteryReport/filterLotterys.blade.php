{{-- @if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'finance' ||
        Auth::user()->role == 'assistant' ||
        Auth::user()->role == 'it') --}}
    <?php
    $total_amount = 0;
    $total_remaining_amount = 0;
    $total_payment = 0;
    $total_remaining_payment = 0;
    $total_cheque_amount = 0;
    ?>
    <table id="lottery-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Member</th>
                <th>Equb Type</th>
                <th>Lottery Amount</th>
                <th>Remaining Amount</th>
                <th>Total Payment</th>
                <th>Remaining Payment</th>
                <th>Cheque Amount</th>
                <th>Cheque Bank Name</th>
                <th>Cheque Description</th>
                <th>Status</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lotterys as $key => $item)
                {{-- @if ($item->remaining_amount != $item->amount) --}}
                <tr id="trm{{ $item['id'] }}">
                    <td>{{ $offset + $key + 1 }}</td>
                    <td> {{ $item->full_name }}</td>
                    <td> {{ $item->name }} - Round {{ $item->round }}</td>
                    <td> {{ number_format($item->amount) }}</td>
                    <td> {{ number_format($item->remaining_amount) }}</td>
                    <td> {{ number_format($item->total_payment) }}</td>
                    <td> {{ number_format($item->remaining_payment) }}</td>
                    <td> {{ number_format($item->cheque_amount) }}</td>
                    <td> {{ $item->cheque_bank_name }}</td>
                    <td> {{ $item->cheque_description }}</td>
                    <td> {{ $item->status }}</td>
                    <td>
                        <?php
                        $toCreatedAt = new DateTime($item['created_at']);
                        $createdDate = $toCreatedAt->format('M-j-Y');
                        echo $createdDate; ?>
                    </td>
                </tr>
                <?php
                if ($item->status == 'paid') {
                    $total_amount = $total_amount + $item->amount;
                }
                // $total_remaining_amount=$total_remaining_amount+$item->remaining_amount;
                $total_payment = $total_payment + $item->total_payment;
                $total_remaining_payment = $total_remaining_payment + $item->remaining_payment;
                $total_cheque_amount = $total_cheque_amount + $item->cheque_amount;
                ?>
                {{-- @endif --}}
            @endforeach
        </tbody>
        @if (count($lotterys) > 0)
            <tr>
                <th colspan="3">Total</th>
                <td colspan="1">{{ number_format($total_amount) }}</td>
                <td colspan="1">{{ number_format($item->remaining_amount) }}</td>
                <td colspan="1">{{ number_format($item->total_payment) }}</td>
                <td colspan="1">{{ number_format($item->remaining_payment) }}</td>
                <!-- <td colspan="1">{{ number_format($total_cheque_amount) }}</td> -->
                <td colspan="5"></td>
            </tr>
        @endif
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
                            onclick="lotterys({{ $offset - $limit }},{{ $pageNumber - 1 }})" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                @endif
                @if ($pageNumber > 3)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                    </li>
                @endif
                @if ($pageNumber > 2)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                    </li>
                @endif
                @if ($pageNumber > 1)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                    </li>
                @endif

                <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                        <span class="sr-only">(current)</span></a></li>

                @if ($offset + $limit < $totalLotterys)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                    </li>
                @endif
                @if ($offset + 2 * $limit < $totalLotterys)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                    </li>
                @endif
                @if ($offset + 3 * $limit < $totalLotterys)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
                    </li>
                @endif

                @if ($offset + $limit == $totalLotterys || $offset + $limit > $totalLotterys)
                    <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);"
                            onclick="lotterys({{ $offset + $limit }},{{ $pageNumber + 1 }})" aria-label="Next">
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
            $("#lottery-table").DataTable({
                "responsive": false,
                "lengthChange": false,
                "searching": true,
                "paging": false,
                "autoWidth": false,
                language: {
                    search: "",
                    searchPlaceholder: "Search",
                },
                "buttons": ["excel", "pdf", "print", "colvis"],
                columnDefs: [{
                    targets: -3,
                    visible: false
                }, {
                    targets: -4,
                    visible: false
                }, {
                    targets: -5,
                    visible: false
                }, {
                    targets: -2,
                    visible: false
                }]
            }).buttons().container().appendTo('#lottery-table_wrapper .col-md-6:eq(0)');
        });
    </script>
{{-- @endif --}}
