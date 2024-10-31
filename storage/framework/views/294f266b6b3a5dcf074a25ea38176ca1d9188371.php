       <?php if(Auth::user()->role == 'admin' ||
               Auth::user()->role == 'general_manager' ||
               Auth::user()->role == 'operation_manager'): ?>
           <div class="modal fade" id="lotteryModal" role="dialog">
               <div class="modal-dialog">
                   <div class="modal-content">
                       <form role="form" method="post" class="form-horizontal form-group"
                           action="<?php echo e(route('registerEqubTaker')); ?>" enctype="multipart/form-data" id="addLottery">
                           <?php echo e(csrf_field()); ?>

                           <div class="modal-header">
                               <h4 class="modal-title">Lottery payment</h4>
                               <button type="button" class="close" data-dismiss="modal">&times;</button>
                           </div>
                           <div class="modal-body">
                               <div class="col-sm-12">
                                   <input type="hidden" id='member_lottery_id' name="lottey_member_id" value="">
                                   <input type="hidden" id='equb_lottery_id' name="lottery_equb_id" value="">

                                   <div class="form-group required" id="addGender">
                                       <label class="control-label">Payment Type</label>
                                       <select class="form-control select2" id="payment_type" name="payment_type"
                                           placeholder="payment type" autocomplete="off" required>
                                           <option value="">Choose..</option>
                                           <option value="cash">Cash</option>
                                           <option value="check">Check</option>
                                           <option value="bank transfer">Bank Transfer</option>
                                           <option value="other">Other</option>
                                       </select>
                                   </div>

                                   <div class="form-group required">
                                       <label class="control-label">Lottery Amount</label>
                                       <input type="number" class="form-control" id="lottery_amount"
                                           name="amount"placeholder="Amount" required>
                                   </div>
                                   
                                   <div class="form-group ">
                                       <label class="control-label">Cheque Amount</label>
                                       <input type="number" class="form-control" id="cheque_amount"
                                           name="cheque_amount"placeholder="cheque amount">
                                   </div>
                                   <div class="form-group ">
                                       <label class="control-label">Cheque Bank Name</label>
                                       <input type="text" class="form-control" id="cheque_bank_name"
                                           name="cheque_bank_name"placeholder="cheque bank name">
                                   </div>
                                   <div class="form-group">
                                       <label for="cheque_description" class="control-label">Description</label>
                                       <textarea id="cheque_description" name="cheque_description" class="form-control" placeholder="Description"></textarea>
                                   </div>

                               </div>
                           </div>
                           <div class="modal-footer">
                               <button type="submit" class="btn btn-primary" id="addLotteryBtn">Save</button>
                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                           </div>
                       </form>
                   </div>

               </div>
           </div>
       <?php endif; ?>
<?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/lottery/addLottery.blade.php ENDPATH**/ ?>