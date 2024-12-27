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
                <tr>
                    <td>{{ $key + 1 + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                    <td>{{ $item->full_name }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ $item->payment_type }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                    <td>{{ number_format($item->credit, 2) }}</td>
                    <td>{{ number_format($item->balance, 2) }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('payments.edit', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('payments.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Pagination Links -->
    <div class="pagination-wrapper">
        {{ $payments->links() }}
    </div>
</div>