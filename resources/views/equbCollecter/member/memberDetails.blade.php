@if (Auth::user()->role == 'equb_collector')
<table id="equb-list-table" class="table table-bordered table-striped" >      {{-- <thead>equb</thead> --}}
    <thead>
        <tr>
            <th></th>
            <th>No</th>
            <th>Equb Type</th>
            <th>Amount in Birr</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Payment</th>
            <th>Remaining Payment</th>
            <th>Expected Total</th>
            <th>Lottery Date</th>
            <th>Remaining Date</th>
            <th>Status</th>
            <th style="width: 50px;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($member->equbs as $key => $equb)
             <tr id="tre{{ $equb['id'] }}">
                <?php
                  $totalPpayment = App\Models\Payment::where('equb_id',$equb['id'])->where('status','paid')->sum('amount');
                  $totalEqubAmount = App\Models\Equb::where('status','Active')->select('total_amount')->where('id',$equb['id'])->pluck('total_amount')->first();
                  $remainingPayment =  $totalEqubAmount - $totalPpayment;
                  $lotteryDate = App\Models\Equb::where('status','Active')->where('id',$equb['id'])->pluck('lottery_date')->first();
                  $lotteryDate=explode(',',$lotteryDate);
                  $date = date('Y-m-d');
                  $lotteryDate=$lotteryDate[0];
                  $date1 = new DateTime($date);
                  $date2 = new DateTime($lotteryDate);
                  if($date2>$date1){
                    $interval = $date2->diff($date1);
                    $interval=$interval->days;
                  }elseif($date2==$date1){
                    $interval=0;
                  }else{
                    $interval="passed";
                  }
                ?>
                <td class="details-control_payment" id="{{ $equb['id'] }}"></td>
                <td>{{$key+1}}</td>
                <td>
                    <a href="javascript:void(0);" onclick="openPaymentTab({{$equb}})">{{$equb->equbType->name}} round {{$equb->equbType->round}}</a>
                </td>
                <td> {{number_format($equb->amount)}}</td>
                <td>
                    <?php
                    $toCreatedAt= new DateTime($equb['start_date']);
                    $createdDate = $toCreatedAt->format("M-j-Y");
                    echo $createdDate;?>
                </td>
                <td>
                    <?php
                    $toCreatedAt= new DateTime($equb['end_date']);
                    $createdDate = $toCreatedAt->format("M-j-Y");
                    echo $createdDate;?>
                </td>
                <td> {{number_format($totalPpayment)}}</td>
                <td> {{number_format($remainingPayment)}}</td>
                <td> {{number_format($equb->total_amount)}}</td>

                <td>
                    <?php
                    foreach(explode(',', $equb->lottery_date) as $lottery_date){
                     $toCreatedAt= new DateTime($lottery_date);
                     $createdDate = $toCreatedAt->format("M-j-Y");
                     echo $createdDate;
                     echo "<br>";
                    }
                    ?>
                </td>
                <td>{{$interval}}</td>
                <td> {{$equb->status}}</td>
                <?php
                      $equbTakers = $equb->equb_takers;
                      if (!empty($equbTakers)) {
                          $indexE = sizeof($equbTakers)-1;
                      }else{
                        $indexE =0;
                      }
                      $expectedTotal = $equb->total_amount;
                      $p = $equb->payments;
                      if (!empty($p)) {
                          $indexP = sizeof($p);
                      }else{
                        $indexP=0;
                      }

                      $sum =0;
                      for($i = 0; $i < $indexP; $i++) {
                        if($p[$i]->status == 'paid' || $p[$i]->status == 'pending'){
                         $sum = $sum + $p[$i]->amount;
                       }
                      }
                      $equbTakers=$equb->equb_takers;
                      if(!empty($equbTakers)){
                     $remainingAmount=$equb->equb_takers[$indexE]->remaining_amount;
                     }else{
                       $remainingAmount = $equb->total_amount;
                     }

                ?>
                <td>
                   <a href="javascript:void(0);" class="btn btn-secondary {{$member->status != 'Active' ? 'disabled' : ($equb->status == 'Deactive' ? 'disabled' : (($sum >= $expectedTotal) ? 'disabled' : '' ))}}" onclick="openPaymentModal({{$equb}})" id="paymentButton"><i class="fas fa-plus-circle"></i> Payment</a>
                </td>
                </tr>
        @endforeach
    </tbody>
    <tfoot>
    </tfoot>
  </table>
<div class="modal modal-danger fade" id="aaaa" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <p class="modal-title" id="exampleModalLabel">Update equb type status</p>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form action="" method="post" id="bbbb">
                    <div class="modal-body">
                      @csrf
                      @method('PUT')
                      <p class="text-center">Are you sure you want to update status?</p>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-sm btn-danger">update</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  </div>
                  </form>
              </div>
          </div>
      </div>
  <script type="text/javascript">
        function equbStatusChange(item){
          $('#aaaa').modal('show');
          $('#bbbb').attr('action',  "{{ url('member/equbStatus-update') }}"  + '/' +item.id);
          }
       $(function () {
        if ( $.fn.DataTable.isDataTable( '#equb-list-table' ) ) {
          $( '#equb-list-table' ).DataTable().destroy();
           }
        var table=$("#equb-list-table").DataTable({
          "responsive": false, "lengthChange": false,"searching": false, "autoWidth": false,"bSort": false,"bDestroy": true,
          language: {
            search: "",
            searchPlaceholder: "Search",}
        });
        table.buttons().container().appendTo('#equb-list-table_wrapper .col-md-6:eq(0)');
          $('#equb-list-table tbody').on('click', 'td.details-control_payment', function() {
              var tr = $(this).closest('tr');
              var inputId = $(this).prop("id");
              var row = table.row(tr);
              if (row.child.isShown()) {
                  row.child.hide();
                  tr.removeClass('shown');
              } else {
                row.child(loadHere).show();
              $.ajax({
                url: "{{ url('member/show-equb') }}"+'/'+inputId,
                type: 'get',
                    success: function(data){
                        row.child(data).show();
                        row.child.show();
                        tr.addClass('shown');

                   },
                    error: function(){
                    }
                });

           }

          });


      });
  </script>
  @endif

