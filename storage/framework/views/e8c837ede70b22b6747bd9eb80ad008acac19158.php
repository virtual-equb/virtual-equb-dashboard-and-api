  <?php if(Auth::user()->role == 'admin' ||
          Auth::user()->role == 'general_manager' ||
          Auth::user()->role == 'operation_manager' ||
          Auth::user()->role == 'it'): ?>
     <div class="modal fade" id="myModal3" role="dialog">
         <div class="modal-dialog">
             <div class="modal-content">
                 <form role="form" method="post" class="form-horizontal form-group"
                     action="<?php echo e(route('registerPayment')); ?>" enctype="multipart/form-data" id="addpayment">
                     <?php echo e(csrf_field()); ?>

                     <div class="modal-header">
                         <h4 class="modal-title">Payment</h4>
                         <button type="button" class="close" data-dismiss="modal">&times;</button>
                     </div>
                     <div class="modal-body">
                         <div class="col-sm-12">
                             <input type="hidden" id='member_payment_id' name="member_id" value="">
                             <input type="hidden" id='equb_payment_id' name="equb_id" value="">
                             <input type="hidden" id='equb_amount' name="equb_amount" value="">
                             <div class="form-group required" id="addGender">
                                 <label class="control-label">Payment Type</label>
                                 <select class="form-control select2" id="payment_type" name="payment_type"
                                     placeholder="payment type" autocomplete="off" required="true" required>
                                     <option value="">Choose..</option>
                                     <option value="cash">Cash</option>
                                     <option value="check">Check</option>
                                     <option value="bank transfer">Bank Transfer</option>
                                     <option value="other">Other</option>
                                 </select>
                             </div>
                             <div class="form-group required">
                                 <label class="control-label">Amount</label>
                                 <input type="number" class="form-control" id="amount"
                                     name="amount"placeholder="Amount" required onkeyup="changeCreadit()">
                             </div>
                             <div class="form-group required">
                                 <label class="control-label">Credit</label>
                                 <input type="number" class="form-control" id="creadit"
                                     name="creadit"placeholder="Creadit" required readonly>
                             </div>
                             <div class="form-group">
                                 <label class="control-label">Remark</label>
                                 <textarea class="form-control" id="remark" name="remark" placeholder="remark"></textarea>
                             </div>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="submit" class="btn btn-primary" id="addPaymentBtn">Save</button>
                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 <?php endif; ?>
<?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/payment/addPayment.blade.php ENDPATH**/ ?>