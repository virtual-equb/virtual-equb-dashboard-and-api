<nav class="main-header navbar navbar-expand-lg navbar-dark navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        

            <?php
                $members = App\Models\Member::where('status', '=', 'Pending')->limit(10)->get();
                $membersCount = App\Models\Member::where('status', '=', 'Pending')->count();
            ?>

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell" style="font-size: 25px"></i>
                    <span class="badge badge-warning navbar-badge" style="font-size: 10px"></span>
                </a>
                <div id="dropdownId" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header"><?php echo e($membersCount); ?> unapproved member<?php echo e($membersCount > 1 ? 's' : ''); ?></span>
                    <div class="dropdown-divider"></div>
                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('showPendingMembers')); ?>" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> <?php echo e($member->full_name); ?> has joined
                        <span class="float-right text-muted text-sm"><?php echo e(Carbon\Carbon::parse($member->created_at)->diffForHumans()); ?></span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo e(route('showPendingMembers')); ?>" class="dropdown-item dropdown-footer">See All Members</a>
                </div>
            </li>

            <li class="nav-item dropdown responsive">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
                <div class="text-dark bg-light dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 500px">
                    <a class="nav-link dropdown-item" href="<?php echo e(url('reports/memberFilter')); ?>">
                        <i class="fas fa-chart-bar"></i> Member Report
                    </a>
                    <a class="nav-link dropdown-item" href="<?php echo e(url('reports/memberFilterByEqubType')); ?>">
                        <i class="fas fa-chart-bar"></i> Member Report By Equb Type
                    </a>

                    <?php if(!optional(Auth::user()->roles)->contains('name', 'marketing_manager')): ?>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/collectedByFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> Collected by Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/equbFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> Equb Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/lotteryFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> Paid Lotteries Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/unPaidLotteryFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> UnPaid Lotteries Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/unPaidLotteryByDateFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> UnPaid Lotteries By Lottery Date Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/reservedLotteryDatesFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> Reserved Lottery Dates Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/paymentFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> Payments Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/unPaidFilter')); ?>">
                            <i class="fas fa-chart-bar"></i> Unpaid Payment Report
                        </a>
                        <a class="nav-link dropdown-item" href="<?php echo e(url('reports/filterEqubEndDates')); ?>">
                            <i class="fas fa-chart-bar"></i> Filter Equbs By End Date Report
                        </a>
                    <?php endif; ?>
                </div>
            </li>
        
    </ul>
</nav>

<script src="<?php echo e(url('plugins/jquery/jquery.min.js')); ?>"></script>
<script>
    $(document).ready(function() {

        // Event handler for clicking the notification bell icon
        $('.fa-bell').on('click', function() {
            $('#dropdownId').empty();
            // Make an AJAX request to fetch the updated content for the dropdown menu
            $.ajax({
                url: '/member/getPending', // Replace with the appropriate URL to fetch the updated content
                method: 'GET',
                success: function(data) {
                    // Build the HTML content for the dropdown menu
                    var dropdownContent = '<span class="dropdown-header">' + data.length + ' unapproved member' + (data.length > 1 ? 's' : '') + '</span>';
                    dropdownContent += '<div class="dropdown-divider"></div>';
                    // Loop through the data and build the notification items
                    for (var i = 0; i < data.length; i++) {
                        dropdownContent += '<a href="<?php echo e(route('showMember')); ?>" class="dropdown-item">';
                        dropdownContent += '<i class="fas fa-envelope mr-2"></i> ' + data[i].full_name + ' has joined';
                        dropdownContent += '<span class="float-right text-muted text-sm">' + formatDate(data[i].created_at) + '</span>';
                        dropdownContent += '</a>';
                    }
                    // Add more notification items if needed
                    dropdownContent += '<div class="dropdown-divider"></div>';
                    dropdownContent += '<a href="<?php echo e(route('showMember')); ?>" class="dropdown-item dropdown-footer">See All Members</a>';
                    // Replace the content of the dropdown menu with the updated content
                    $('#dropdownId').html(dropdownContent);
                    updateNotificationCount();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching updated dropdown content:', error);
                }
            });
        });

        // Function to update the notification count
        function updateNotificationCount() {
            $.ajax({
                url: '/member/countPendingMembers', // Replace with the appropriate URL to fetch the updated notification count
                method: 'GET',
                success: function(count) {
                    $('.navbar-badge').text(count);
                    $('#dropdownId .dropdown-header').text(count + ' unapproved member' + (count > 1 ? 's' : ''));
                },
                error: function(xhr, status, error) {
                    console.error('Error updating notification count:', error);
                }
            });
        }
        setInterval(updateNotificationCount, 10000);

        // Function to format the date using moment.js
        function formatDate(dateString) {
            var date = new Date(dateString);
            return moment(date).fromNow();
        }
    });
</script><?php /**PATH D:\virtual Equb\virtual-backend\resources\views/partials/navbar.blade.php ENDPATH**/ ?>