<nav class="main-header navbar navbar-expand-lg navbar-dark navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        @if (Auth::user()->role == 'admin' ||
                Auth::user()->role == 'general_manager' ||
                Auth::user()->role == 'operation_manager' ||
                Auth::user()->role == 'marketing_manager' ||
                Auth::user()->role == 'assistant' ||
                Auth::user()->role == 'finance' ||
                Auth::user()->role == 'it')
            @php
                // $members = App\Models\Member::get();
                // $membersCount = App\Models\Member::count();
                $members = App\Models\Member::where('status', '=', 'Pending')->limit(10)->get();
                $membersCount = App\Models\Member::where('status', '=', 'Pending')->count();
                // dd($membersCount);
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell" style="font-size: 25px"></i>
                    <span class="badge badge-warning navbar-badge" style="font-size: 10px"></span>
                </a>
                <div id='dropdownId' class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">{{ $membersCount }} unapproved member<?php echo $membersCount > 1 ? 's' : ''; ?></span>
                    <div class="dropdown-divider"></div>
                    <?php
                    foreach ($members as $key => $member) { ?>
                    <!-- Notification Items -->
                    <a href="{{ route('showPendingMembers') }}" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> <?php echo $member->full_name; ?> has joined
                        <span class="float-right text-muted text-sm">
                            {{ Carbon\Carbon::parse($member->created_at)->diffForHumans() }}</span>
                    </a>
                    <?php
                    }
                    ?>
                    <!-- Add more notification items here -->
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('showPendingMembers') }}" class="dropdown-item dropdown-footer">See All Members</a>
                </div>
            </li>

            <li class="nav-item dropdown responsive">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
                <div class=" text-dark bg-light dropdown-menu dropdown-menu-lg dropdown-menu-right"
                    style="width: 500px">

                    <a class="nav-link dropdown-item" href="{{ url('reports/memberFilter') }}">
                        <i class="fas fa-chart-bar"></i>
                        Member Report
                    </a>
                    <a class="nav-link dropdown-item" href="{{ url('reports/memberFilterByEqubType') }}">
                        <i class="fas fa-chart-bar"></i>
                        Member Report By Equb Type
                    </a>
                    @if (Auth::user()->role != 'marketing_manager')
                        <a class="nav-link dropdown-item" href="{{ url('reports/collectedByFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            Collected by Report
                        </a>

                        <a class="nav-link dropdown-item" href="{{ url('reports/equbFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            Equb Report
                        </a>


                        <a class="nav-link dropdown-item" href="{{ url('reports/lotteryFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            Paid Lotteries Report
                        </a>
                        <a class="nav-link dropdown-item" href="{{ url('reports/unPaidLotteryFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            UnPaid Lotteries Report
                        </a>
                        <a class="nav-link dropdown-item" href="{{ url('reports/unPaidLotteryByDateFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            UnPaid Lotteries By Lottery Date Report
                        </a>
                        <a class="nav-link dropdown-item" href="{{ url('reports/reservedLotteryDatesFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            Reserved Lottery Dates Report
                        </a>
                        <a class="nav-link dropdown-item" href="{{ url('reports/paymentFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            Payments Report
                        </a>
                        <a class="nav-link dropdown-item" href="{{ url('reports/unPaidFilter') }}">
                            <i class="fas fa-chart-bar"></i>
                            Unpaid Payment Report
                        </a>
                        <a class="nav-link dropdown-item" href="{{ url('reports/filterEqubEndDates') }}">
                            <i class="fas fa-chart-bar"></i>
                            Filter Equbs By End Date Report
                        </a>
                    @endif
                </div>

            </li>
        @endif
    </ul>
</nav>
<script src="{{ url('plugins/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {

        // Event handler for clicking the notification bell icon
        $('.fa-bell').on('click', function() {
            $('#dropdownId').empty()
            // Make an AJAX request to fetch the updated content for the dropdown menu
            $.ajax({
                url: '/member/getPending', // Replace with the appropriate URL to fetch the updated content
                method: 'GET',
                success: function(data) {
                    // Build the HTML content for the dropdown menu
                    var dropdownContent =
                        '<span class="dropdown-header">  unapproved member<?php echo $membersCount > 1 ? 's' : ''; ?></span>';
                    dropdownContent +=
                        '<div class="dropdown-divider"></div>';
                    // Loop through the data and build the notification items
                    for (var i = 0; i < data.length; i++) {
                        dropdownContent +=
                            '<a href="{{ route('showMember') }}" class="dropdown-item">';
                        dropdownContent +=
                            '<i class="fas fa-envelope mr-2"></i> ' +
                            data[i].full_name +
                            ' has joined';
                        dropdownContent +=
                            '<span class="float-right text-muted text-sm">' +
                            formatDate(data[i].created_at) + '</span>';
                        dropdownContent += '</a>';
                    }

                    // Add more notification items if needed
                    dropdownContent +=
                        '<div class="dropdown-divider"></div>';
                    dropdownContent +=
                        '<a href="{{ route('showMember') }}" class="dropdown-item dropdown-footer">See All Members</a>';
                    // Replace the content of the dropdown menu with the updated content
                    $('#dropdownId').html(dropdownContent);
                    updateNotificationCount();
                },
                error: function(xhr, status, error) {
                    // console.error(
                    //     'Error fetching updated dropdown content:',
                    //     error);
                }
            });
        });
        updateNotificationCount();
    });
    // Function to update the notification count
    function updateNotificationCount() {
        $.ajax({
            url: '/member/countPendingMembers', // Replace with the appropriate URL to fetch the updated notification count
            method: 'GET',
            success: function(count) {
                // Update the count in the badge
                $('.navbar-badge').text(count);
                $('#dropdownId .dropdown-header').text(count + ' unapproved member' + (count > 1 ? 's' :
                    ''));
            },
            error: function(xhr, status, error) {
                // console.error('Error updating notification count:', error);
            }
        });
    }
    setInterval(updateNotificationCount, 10000);

    // Function to format the date using Carbon
    function formatDate(dateString) {
        var date = new Date(dateString);
        var formattedDate = moment(date).fromNow();
        return formattedDate;
    }
</script>
