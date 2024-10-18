@if (Auth::user()->role == 'member')
 <table id="payment-list-table_in_tab" class="table table-bordered table-striped " >      {{-- <thead>Payment</thead> --}}
    <thead> 
        <tr>
            <td colspan="2">
                <div class="row" style="padding-left: 40px;">  
                  <div class="col">
                     <h6>Full name </h6>
                     </div>
                    <div class="col">
                     {{$member->full_name}} <br>
                    </div>
                    </div>
                    <div class="row" style="padding-left: 40px;"> 
                    <div class="col">
                      <h6>Phone </h6>
                     </div>
                    <div class="col">
                     {{$member->phone}}
                    </div>
                   </div>
                  
            </td>
                 <td colspan="3">
                <div class="row" style="padding-left: 40px;">  
                        <div class="col">
                     <h6>Gender </h6>
                     </div>
                    <div class="col">
                     {{$member->gender}} <br>
                    </div>
                    </div>
                    <div class="row" style="padding-left: 40px;"> 
                    <div class="col">
                      <h6>Equb Type </h6>
                     </div>
                    <div class="col">
                     {{$equb->equbType->name}}
                    </div>
                   </div>
                  
            </td>
        </tr>
        <tr>
           <!--  <th>Member</th>
            <th>Equb Type</th> -->
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
        @foreach($payments as $key => $payment)
                <td>{{$key+1}}</td>
                <td> {{$payment->payment_type}}</td>
                <td> {{$payment->amount}}</td>
                <td> {{$payment->status}}</td>   
                <td> {{$payment->created_at}}</td>             
                <td>      
                     <div class='dropdown'>
                            <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button' data-toggle='dropdown'>Menu<span class='caret'></span></button>
                            <ul class='dropdown-menu p-4'>
                                <li>
                               <a href="javascript:void(0);" class="text-secondary btn btn-link"  onclick="openPaymentEditModal({{$payment}})" style="margin-right:10px;"><span class="fa fa-edit"> </span> Edit</a>
                                <li>
                                   <a href="javascript:void(0);" class="text-secondary btn btn-link" onclick="openDeletePaymentModal({{$payment}})"><i class="fas fa-plus-circle"></i> Delete</a>
                                </li>      
                            </ul>
                     </div>
                </td>
        </tr>
        @endforeach
    </tbody>
       <tr>
           <td>Total</td>
           <td colspan="4">{{$total}}</td>
       </tr>
    </table> 
    @endif 
