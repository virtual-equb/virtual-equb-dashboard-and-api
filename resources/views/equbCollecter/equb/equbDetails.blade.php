  @if (Auth::user()->role == 'equb_collector')
    <div class="table-responsive"> 
    <table id="payment-list-table_in_member" class="table table-bordered table-striped" >      {{-- <thead>Payment</thead> --}}
        <thead>
            <tr>
                <th>No</th>
                <th>Payment Type</th>
                <th>Lottery Amount</th>
                <th>Remaining Amount</th>
                <th>Cheque Amount</th>
                <th>Cheque Bank Name</th>
                <th>Cheque Description</th>
                <th>Status</th>
                <th>Paid By</th>
                <th>Payment Date</th>

            </tr>
        </thead>
        <tbody>
            <tr>
            @foreach($equb->equbTakers as $key => $equbTaker)
                    <td>{{$key+1}}</td>
                    <td> {{$equbTaker->payment_type}}</td>
                    <td> {{number_format($equbTaker->amount)}}</td>
                    <td> {{number_format($equbTaker->remaining_amount)}}</td>
                    <td> {{number_format($equbTaker->cheque_amount)}}</td> 
                    <td> {{$equbTaker->cheque_bank_name}}</td>
                    <td> {{$equbTaker->cheque_description}}</td>
                    <td> {{$equbTaker->status}}</td>  
                    <td> {{$equbTaker->paid_by}}</td>  
                    <td>
                        <?php
                        $toCreatedAt= new DateTime($equbTaker['created_at']);
                        $createdDate = $toCreatedAt->format("M-j-Y");
                        echo $createdDate;?>
                    </td>            
            </tr>
            @endforeach
        </tbody>
      </table>
      </div> 
   @endif   


