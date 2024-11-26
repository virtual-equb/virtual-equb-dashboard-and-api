@can('view lottery_winner')
    <table id="payment-list-table_in_tab" class="table table-bordered table-striped ">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Equb Type</th>
            </tr>
            <tr>
                <td>{{ $member->full_name }}</td>
                <td>{{ $member->phone }}</td>
                <td>{{ $member->gender }}</td>
                <td>{{ $equb->equbType->name }}</td>
            </tr>

            <tr>
                <th>No</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>status</th>
                <th>payment date</th>
                <th style="width: 50px">Action</th>

            </tr>
        </thead>

        <tbody>
            <tr>
                @foreach ($payments as $key => $payment)
                    <td>{{ $key + 1 }}</td>
                    <td> {{ $payment->payment_type }}</td>
                    <td> {{ $payment->amount }}</td>
                    <td> {{ $payment->status }}</td>
                    <td> {{ $payment->created_at }}</td>
                    <td>
                        <div class='dropdown'>
                            <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                data-toggle='dropdown'>Menu<span class='caret'></span></button>
                            <ul class='dropdown-menu p-4'>
                                <li>
                                    <a href="javascript:void(0);" class="text-secondary btn btn-link"
                                        onclick="openPaymentEditModal({{ $payment }})"
                                        style="margin-right:10px;"><span class="fa fa-edit"> </span> Edit</a>
                                <li>
                                    <a href="javascript:void(0);" class="text-secondary btn btn-link"
                                        onclick="openDeletePaymentModal({{ $payment }})"><i
                                            class="fas fa-plus-circle"></i> Delete</a>
                                </li>
                            </ul>
                        </div>
                    </td>
            </tr>
@endforeach
</tbody>
<tr>
    <td>Total</td>
    <td colspan="4">{{ $total }}</td>
</tr>
</table>
@endcan
