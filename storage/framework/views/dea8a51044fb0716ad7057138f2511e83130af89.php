<?php if(Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'it'): ?>
    <div class="modal fade" id="editPaymentModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action="" id="updatePayment">
                    <?php echo e(csrf_field()); ?>

                    <?php echo method_field('put'); ?>
                    <input type="hidden" id='equb_id' name="id" value="">
                    <input type="hidden" id='update_member_id' name="member_id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Payment</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <label class="control-label">Payment Type</label>
                                <select class="form-control" id="update_payment_type" name="update_payment_type">
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="bank transfer">Bank Transfer</option>
                                    <option value="other">Other</option>

                                </select>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Amount</label>
                                <input type="number" class="form-control" id="update_payment_amount"
                                    name="update_amount" onkeyup="getCredit()" placeholder="Amount" required>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Credit</label>
                                <input type="number" class="form-control" id="update_payment_credit"
                                    name="update_creadit"placeholder="Credit" required readonly>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Remark</label>
                                <textarea class="form-control" id="update_payment_remark" name="update_payment_remark" placeholder="remark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2" id="updatePaymentBtn">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/payment/editPayment.blade.php ENDPATH**/ ?>