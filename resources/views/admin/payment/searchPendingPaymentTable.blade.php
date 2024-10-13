<div class="table-responsive">
    <table id="payment-list-table" class="table table-bordered table-striped" style="padding-bottom:100px">
        <thead>
            <tr>
                {{-- <th></th> --}}
                <th>No</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Credit</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Registered At </th>
                <th style="width: 60px">Action </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $key => $item)
                <?php
                //   $address = json_decode($item->address);
                //   dd($payments);
                ?>
                <tr id="trm{{ $item['id'] }}">
                    {{-- <td class="details-control_equb" id="{{ $item['id'] }}"></td> --}}
                    <td>{{ $offset + $key + 1 }}</td>
                    <th>{{ $item->member->full_name }}</th>
                    <th>{{ $item->member->phone }}</th>
                    <td>{{ $item->payment_type }}</td>
                    <td>{{ $item->amount }}</td>
                    <td>{{ $item->creadit }}</td>
                    <td>{{ $item->balance }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <?php
                        $toCreatedAt = new DateTime($item['created_at']);
                        $createdDate = $toCreatedAt->format('M-j-Y');
                        echo $createdDate; ?>
                    </td>
                    @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')
                        <td>
                            <div class='dropdown'>
                                <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                    data-toggle='dropdown'>Menu<span class='caret'></span></button>
                                <ul class='dropdown-menu p-4'>
                                    @if (Auth::user()->role != 'finance')
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="openPaymentEditModal({{ $item }})"
                                                id="editButton"><span class="fa fa-edit"> </span> Edit</a>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="openDeletePaymentModal({{ $item }})"
                                                id="deleteButton"><i class="fas fa-trash"></i> Delete</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                onclick="showPaymentProofModal({{ $item }})" id="showProof"><i
                                                    class="fas fa-image"></i> Show Proof</a>
                                        </li>
                                        @if ($item->status == 'pending')
                                            <li>
                                                <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                    onclick="approvePayment({{ $item }})" id="showProof"><i
                                                        class="fas fa-check"></i> Approve</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                    onclick="rejectPayment({{ $item }})" id="showProof"><i
                                                        class="fas fa-times"></i> Reject</a>
                                            </li>
                                        @elseif ($item->status == 'unpaid')
                                            <li>
                                                <a href="javascript:void(0);" class="text-secondary btn btn-flat"
                                                    onclick="approvePayment({{ $item }})" id="showProof"><i
                                                        class="fas fa-check"></i> Approve</a>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
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
                            onclick="loadMoreSearchMembers({{ $offset - $limit }},{{ $pageNumber - 1 }})"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                @endif
                @if ($pageNumber > 3)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="loadMoreSearchMembers({{ $offset - $limit * 3 }},{{ $pageNumber - 3 }})">{{ $pageNumber - 3 }}</a>
                    </li>
                @endif
                @if ($pageNumber > 2)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="loadMoreSearchMembers({{ $offset - $limit * 2 }},{{ $pageNumber - 2 }})">{{ $pageNumber - 2 }}</a>
                    </li>
                @endif
                @if ($pageNumber > 1)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="loadMoreSearchMembers({{ $offset - $limit }},{{ $pageNumber - 1 }})">{{ $pageNumber - 1 }}</a>
                    </li>
                @endif

                <li class="page-item active"> <a class="page-link">{{ $pageNumber }}
                        <span class="sr-only">(current)</span></a></li>

                @if ($offset + $limit < $totalPayments)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="loadMoreSearchMembers({{ $offset + $limit }},{{ $pageNumber + 1 }})">{{ $pageNumber + 1 }}</a>
                    </li>
                @endif
                @if ($offset + 2 * $limit < $totalPayments)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="loadMoreSearchMembers({{ $offset + $limit * 2 }},{{ $pageNumber + 2 }})">{{ $pageNumber + 2 }}</a>
                    </li>
                @endif
                @if ($offset + 3 * $limit < $totalPayments)
                    <li class="page-item"><a class="page-link" href="javascript:void(0);"
                            onclick="loadMoreSearchMembers({{ $offset + $limit * 3 }},{{ $pageNumber + 3 }})">{{ $pageNumber + 3 }}</a>
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
                            onclick="loadMoreSearchMembers({{ $offset + $limit }},{{ $pageNumber + 1 }})" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</div>
<script>
    $(function() {
        var table = $("#payment-list-table").DataTable({
            "responsive": false,
            "lengthChange": false,
            "searching": true,
            "paging": false,
            "autoWidth": false,
            language: {
                search: "",
                searchPlaceholder: "Search",
            },
            "buttons": ["excel", "pdf", "print", "colvis"]
        });
        table.buttons().container().appendTo('#payment-list-table_wrapper .col-md-6:eq(0)');
    });
</script>
