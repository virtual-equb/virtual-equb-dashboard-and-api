  {{-- @if (Auth::user()->role == 'admin' ||
          Auth::user()->role == 'general_manager' ||
          Auth::user()->role == 'operation_manager' ||
          Auth::user()->role == 'customer_service' ||
          Auth::user()->role == 'assistant' ||
          Auth::user()->role == 'finance' ||
          Auth::user()->role == 'it') --}}
      <table id="payment-list-table_in_member" class="table table-bordered table-striped"> {{-- <thead>Payment</thead> --}}
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
                  <th style="width: 50px">Action</th>

              </tr>
          </thead>

          <tbody>
              <tr>
                  @foreach ($equb->equbTakers as $key => $equbTaker)
                      <td>{{ $key + 1 }}</td>
                      <td> {{ $equbTaker->payment_type }}</td>
                      <td> {{ number_format($equbTaker->amount) }}</td>
                      <td> {{ number_format($equbTaker->remaining_amount) }}</td>
                      <td> {{ number_format($equbTaker->cheque_amount) }}</td>
                      <td> {{ $equbTaker->cheque_bank_name }}</td>
                      <td> {{ $equbTaker->cheque_description }}</td>
                      <td> {{ $equbTaker->status }}</td>
                      <td> {{ $equbTaker->paid_by }}</td>
                      <td>
                          <?php
                          $toCreatedAt = new DateTime($equbTaker['created_at']);
                          $createdDate = $toCreatedAt->format('M-j-Y');
                          echo $createdDate; ?>
                      </td>
                      @if (Auth::user()->role != 'operation_manager' && Auth::user()->role != 'assistant' && Auth::user()->role != 'finance')
                          <td>
                              <div class='dropdown'>
                                  <div class="table-responsive">
                                      <button class='btn btn-secondary btn-sm btn-flat dropdown-toggle' type='button'
                                          data-toggle='dropdown'
                                          onclick="openLotteryPaymentMenu({{ $equbTaker }})">Menu<span
                                              class='caret'></span></button>
                                      <ul class='dropdown-menu p-4'>
                                          @if ($equbTaker->status === 'pending')
                                              <li>
                                                  <button href="javascript:void(0);"
                                                      class="text-secondary btn btn-flat}" id="lotteryApprove"
                                                      onclick="openApproveLotteryModal({{ $equbTaker }})"><i
                                                          class="fas fa-check"></i> Approve</button>
                                              </li>
                                          @endif
                                          @if ($equbTaker->status === 'approved')
                                              <li>
                                                  <button href="javascript:void(0);"
                                                      class="text-secondary btn btn-flat {{ $equb->status == 'Deactive' ? 'disabled' : '' }}"
                                                      id="lotteryPay"
                                                      onclick="openPayLotteryModal({{ $equbTaker }})"><i
                                                          class="fas fa-money-bill"></i> Pay</button>
                                              </li>
                                          @endif
                                          <li>
                                              <button href="javascript:void(0);"
                                                  class="text-secondary btn btn-flat {{ $equb->status == 'Deactive' ? 'disabled' : '' }}"
                                                  id="lotteryEdit"
                                                  onclick="openLotteryPaymentEditModal({{ $equbTaker }})"><span
                                                      class="fa fa-edit"> </span> Edit</button>
                                          </li>
                                          <li>
                                              <button href="javascript:void(0);"
                                                  class="text-secondary btn btn-flat {{ $equb->status == 'Deactive' ? 'disabled' : '' }}"
                                                  id="lotteryDelete"
                                                  onclick="openDeleteLotteryModal({{ $equbTaker }})"><i
                                                      class="fas fa-trash-alt"></i> Delete</button>
                                          </li>
                                      </ul>
                                  </div>
                          </td>
                      @endif
              </tr>
  @endforeach
  </tbody>
  </table>
  {{-- @endif --}}
