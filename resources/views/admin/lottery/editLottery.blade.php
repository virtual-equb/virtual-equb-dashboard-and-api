@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'it')
    <div class="modal fade" id="editLotteryPaymentModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action=""
                    id="updateLotteryPayment">
                    {{ csrf_field() }}
                    @method('put')
                    <input type="hidden" id='lottery_id' name="id" value="">
                    <input type="hidden" id='update_equb_id' name="equb_id" value="">
                    <input type="hidden" id='update_member_id' name="member_id" value="">

                    <div class="modal-header">
                        <h4 class="modal-title">Edit lottery payment</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="col-sm-12">
                            <!-- text input -->
                            <div class="form-group required">
                                <label class="control-label">Payment Type</label>
                                <select class="form-control" id="update_lottery_payment_type"
                                    name="update_lottery_payment_type">
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="bank transfer">Bank Transfer</option>
                                    <option value="other">Other</option>

                                </select>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Amount</label>
                                <input type="number" class="form-control" id="update_lottery_amount"
                                    name="update_lottery_amount"placeholder="Amount" required>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Status</label>
                                <select class="form-control select2" id="update_lottery_status"
                                    name="update_lottery_status" placeholder="Status" required>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label class="control-label">Cheque Amount</label>
                                <input type="number" class="form-control" id="update_lottery_cheque_amount"
                                    name="update_lottery_cheque_amount"placeholder="cheque amount">
                            </div>
                            <div class="form-group ">
                                <label class="control-label">Cheque Bank Name</label>
                                <input type="text" class="form-control" id="update_lottery_cheque_bank_name"
                                    name="update_lottery_cheque_bank_name"placeholder="cheque bank name">
                            </div>
                            <div class="form-group">
                                <label for="update_lottery_cheque_description" class="control-label">Description</label>
                                <textarea id="update_lottery_cheque_description" name="update_lottery_cheque_description" class="form-control"
                                    placeholder="Description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2" id="updateLotteryBtn">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endif
