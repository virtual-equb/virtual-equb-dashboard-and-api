                  <table id="member-list-table" class="table table-bordered table-striped">
                  <thead >
                  <tr>
                    <th>Full Name</th>
                    <th>Lottery Amount</th>
                    <th>Lottery Date</th>
                  </tr>
                  </thead>
                     <tbody>
                       @foreach ($equbDetail as $key => $item)
                        <tr>
                           <th>{{ $item->member->full_name}}</th>
                           <th>{{ $item->total_amount}}</th>
                           <th>{{ $item->lottery_date}}</th>
                        @endforeach   
                        </tr>
                     </tbody>
                   </table> 
                  <table id="member-list-table" class="table table-bordered table-striped">
                  <thead >
                      <tr>
                        <th>Lottery Date -------- Expected Total</th>
                      </tr>
                  </thead>
                     <tbody>
                        @foreach ($ExpectedTotal as $key => $date)
                           <tr>
                             <th>{{ $date}}</th>
                           </tr>   
                        @endforeach   
                     </tbody>
                   </table> 
                    


