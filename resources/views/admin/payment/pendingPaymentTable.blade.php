<div class="table-responsive">
    <table id="payment-list-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Credit</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Registered At</th>
                <th style="width: 60px">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $key => $item)
                <tr id="trm{{ $item['id'] }}">
                    <td>{{ $offset + $key + 1 }}</td>
                    <td>{{ $item->member->full_name }}</td>
                    <td>{{ $item->member->phone }}</td>
                    <td>{{ $item->payment_type }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                    <td>{{ number_format($item->credit, 2) }}</td>
                    <td>{{ number_format($item->balance, 2) }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('M-j-Y') }}</td>

                    @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant')
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                    Menu <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    @if (Auth::user()->role != 'finance')
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary" onclick="openPaymentEditModal({{ $item }})">
                                                <span class="fa fa-edit"></span> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary" onclick="openDeletePaymentModal({{ $item }})">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="text-secondary" onclick="showPaymentProofModal({{ $item }})">
                                                <i class="fas fa-image"></i> Show Proof
                                            </a>
                                        </li>
                                        @if ($item->status == 'pending' || $item->status == 'unpaid')
                                            <li>
                                                <a href="javascript:void(0);" class="text-secondary" onclick="approvePayment({{ $item }})">
                                                    <i class="fas fa-check"></i> Approve
                                                </a>
                                            </li>
                                            @if ($item->status == 'pending')
                                                <li>
                                                    <a href="javascript:void(0);" class="text-secondary" onclick="rejectPayment({{ $item }})">
                                                        <i class="fas fa-times"></i> Reject
                                                    </a>
                                                </li>
                                            @endif
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

    <!-- Delete Confirmation Modal -->
    <form action="" method="post" id="deleteMember">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Member</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input id="id" name="id" type="hidden" value="">
                        <p class="text-center">Are you sure you want to delete this member?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Status Update Modal -->
    <form action="" method="post" id="updateStatus">
        @csrf
        @method('PUT')
        <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Member Type Status</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input id="member_id" name="member_id" type="hidden" value="">
                        <p class="text-center">Are you sure you want to update status?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Pagination -->
    <div class="justify-content-end">
        <nav aria-label="Page navigation" id="paginationDiv">
            <ul class="pagination">
                <li class="page-item {{ $offset == 0 ? 'disabled' : '' }}">
                    <a class="page-link" href="javascript:void(0);" onclick="pendingMembers({{ $offset - $limit }}, {{ $pageNumber - 1 }})" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                @for ($i = -3; $i <= 3; $i++)
                    @if ($pageNumber + $i > 0 && $offset + $i * $limit < $totalPayments)
                        <li class="page-item {{ $i == 0 ? 'active' : '' }}">
                            <a class="page-link" href="javascript:void(0);" onclick="pendingMembers({{ $offset + $i * $limit }}, {{ $pageNumber + $i }})">{{ $pageNumber + $i }}</a>
                        </li>
                    @endif
                @endfor

                <li class="page-item {{ $offset + $limit >= $totalPayments ? 'disabled' : '' }}">
                    <a class="page-link" href="javascript:void(0);" onclick="pendingMembers({{ $offset + $limit }}, {{ $pageNumber + 1 }})" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<script>
    $(function() {
        $("#payment-list-table").DataTable({
            "responsive": false,
            "lengthChange": false,
            "searching": false,
            "paging": false,
            "autoWidth": false,
            language: {
                search: "",
                searchPlaceholder: "Search",
            },
            "buttons": ["excel", "pdf", "print", "colvis"]
        });
    });
</script>