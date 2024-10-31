<?php if(Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'customer_service' ||
        Auth::user()->role == 'it'): ?>
    <div class="table-responsive">
        <div class="modal fade" id="editEqubTypeModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form role="form" method="post" class="form-horizontal form-group" action="" id="updateEqub">
                        <?php echo e(csrf_field()); ?>

                        <input type="hidden" id='update_equb_id' name="equb_id" value="">
                        <input type="hidden" id='update_member_id' name="member_id" value="">
                        <input type="hidden" id='equb_type' name="equb_type" value="">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Equb</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="col-sm-12">
                                <div class="form-group required">
                                    <label class="control-label">Equb Type</label>
                                    <select class="form-control select2" id="update_equb_type" name="equb_type_id"
                                        placeholder="Equb Type" required onchange="checkTimelineForEqubUpdate()">
                                        <option value="">choose...</option>
                                        <?php $__currentLoopData = $equbTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equbType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option data-info="<?php echo e($equbType->type); ?>"
                                                data-startdate="<?php echo e($equbType->start_date); ?>"
                                                data-enddate="<?php echo e($equbType->end_date); ?>"
                                                data-rote="<?php echo e($equbType->rote); ?>" data-quota="<?php echo e($equbType->quota); ?>"
                                                value="<?php echo e($equbType->id); ?>">
                                                <?php echo e($equbType->name); ?> round
                                                <?php echo e($equbType->round); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                </div>
                                <div class="form-group required">
                                    <label class="control-label">Start Date</label>
                                    <input type="text" onchange="getExpectedTotalForUpdate()" class="form-control"
                                        id="update_start_date" name="start_date" placeholder="Start Date"
                                        autocomplete="off" required>
                                </div>
                                <div id="update_timeline_div" class="form-group required">
                                    <label class="control-label">Timeline</label>
                                    <select class="form-control select2" id="update_timeline" name="timeline"
                                        placeholder="Timeline">
                                        <option value="">Choose...</option>
                                        <option value="105" data-info="105">105 days</option>
                                        <option value="210" data-info="210">210 days...</option>
                                        <option value="315" data-info="315">315 days...</option>
                                        <option value="420" data-info="420">420 days...</option>

                                        <option value="350" data-info="350">50 Weeks</option>
                                        <option value="700" data-info="700">100 Weeks</option>
                                        <option value="1050" data-info="1050">150 Weeks</option>

                                        <option value="365" data-info="365">12 Months</option>
                                        <option value="730" data-info="730">24 Months</option>
                                        <option value="1095" data-info="1095">36 Months</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label">End Date</label>
                                    <input type="text" class="form-control disabled" id="update_end_date"
                                        name="end_date"placeholder="End date" readonly>
                                    
                                </div>
                                <div id="update_equb_lottery_date_div" class="form-row">
                                    <div class="form-group required col-md-9">
                                        <label for="lottery_date" class="control-label">Lottery Date</label>
                                        <input type="text" class="form-control" id="update_lottery_date"
                                            name="lottery_date" placeholder="Lottery Date" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="check" class="control-label">&nbsp;</label>
                                        <input type="button" class="form-control btn btn-primary" value="Check"
                                            onclick="validateFormForEqubUpdate()">
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label">Amount</label>
                                    <input type="number" onkeyup="getExpectedTotalForUpdate()" class="form-control"
                                        id="update_amount" name="amount"placeholder="Amount" required>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label">Expected Total</label>
                                    <input type="number" class="form-control" id="update_total_amount"
                                        name="total_amount"placeholder="total equb mount" required readonly
                                        min="1">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary mr -2" id="updateEqubBtn">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/equb/editEqub.blade.php ENDPATH**/ ?>