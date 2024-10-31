
    <div class="modal fade" id="addNotificationModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group"
                    action="<?php echo e(route('sendNotifation')); ?>" enctype="multipart/form-data" id="addNotification">
                    <?php echo e(csrf_field()); ?>

                    <div class="modal-header">
                        <h4 class="modal-title">Send Notification</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <!-- text input -->
                            <div class="form-group">
                                <label class="control-label">Send WIth</label>
                                <select class="form-control select2"id="method" name="method" placeholder="Equb Type">
                                    <option value="both">Both</option>
                                    <option value="sms">SMS</option>
                                    <option value="notification">Push Notification</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Equb Type</label>
                                <select class="form-control select2"id="equb_type_id" name="equb_type_id"
                                    placeholder="Equb Type">
                                    <option value="all">All</option>
                                    <?php $__currentLoopData = $equbTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equbType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option data-info="<?php echo e($equbType->type); ?>" value="<?php echo e($equbType->id); ?>">
                                            <?php echo e($equbType->name); ?> round <?php echo e($equbType->round); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Enter title" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Body</label>
                                <textarea rows="7" id="body" name="body" class="form-control" placeholder="Enter body"></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div class="modal fade" id="sendNotificationModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group"
                    action="<?php echo e(route('sendIndividualNotifation')); ?>" enctype="multipart/form-data" id="addNotification">
                    <?php echo e(csrf_field()); ?>

                    <div class="modal-header">
                        <h4 class="modal-title">Send Notification</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <!-- text input -->
                            <input type="hidden" id='m_phone' name="m_phone" value="">
                            <div class="form-group">
                                <label class="control-label">Send WIth</label>
                                <select class="form-control select2"id="send_method" name="send_method" placeholder="Equb Type">
                                    <option value="both">Both</option>
                                    <option value="sms">SMS</option>
                                    <option value="notification">Push Notification</option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Title </label>
                                <input type="text" class="form-control" id="send_title" name="send_title"
                                    placeholder="Enter title" required>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Body </label>
                                <textarea rows="7" id="send_body" name="send_body" class="form-control" placeholder="Enter body" required></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

<?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/notification/sendNotification.blade.php ENDPATH**/ ?>