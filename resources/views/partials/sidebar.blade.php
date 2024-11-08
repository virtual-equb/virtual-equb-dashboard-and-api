<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ url('dist/img/PNG/VirtualEqubLogoIcon.png') }}" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: .8">
        <strong class="brand-text font-weight-light">Virtual Equb</strong>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                {{ Auth::user()->profile_photo_path }}
            </div>
            <div class="info">
                <a href="/user/profile" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Dashboard Section -->
                <li class="nav-item">
                    <a href="#" class="nav-link" id="dashboardLink" onclick="setActive('dashboardLink')">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link" id="mainDash" onclick="setActive('mainDash')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Main Dashboard</p>
                            </a>
                        </li>
                        @foreach (App\Models\MainEqub::all() as $equbType)
                            <li class="nav-item">
                                <a href="{{ route('viewMainEqub', $equbType->id) }}" class="nav-link" id="equb-{{ $equbType->id }}" onclick="setActive('equb-{{ $equbType->id }}')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ $equbType->name }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                @can('view member')
                <li class="nav-item">
                    <a href="#" class="nav-link" id="membersLink" onclick="setActive('membersLink')">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Members<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('showMember') }}" class="nav-link" id="nav-mem" onclick="setActive('nav-mem')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Members</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('showPendingMembers') }}" class="nav-link" id="pendingMem" onclick="setActive('pendingMem')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Members</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                @can('view payment')
                <li class="nav-item">
                    <a href="#" class="nav-link" id="paymentsLink" onclick="setActive('paymentsLink')">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payments<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('showAllPendingPayments') }}" class="nav-link" id="pendingPayments" onclick="setActive('pendingPayments')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Payments</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                @can('view main_equb')
                <li class="nav-item">
                    <a href="{{ route('mainEqubs.index') }}" class="nav-link" id="mainEqubs" onclick="setActive('mainEqubs')">
                        <i class="nav-icon fa fa-server"></i>
                        <p>Main Equbs</p>
                    </a>
                </li>
                @endcan

                @can('view equb_type')
                <li class="nav-item">
                    <a href="{{ route('showEqubType') }}" class="nav-link" id="showEqubType" onclick="setActive('showEqubType')">
                        <i class="nav-icon fa fa-network-wired"></i>
                        <p>Equb Type</p>
                    </a>
                </li>
                @endcan

                @can('view rejected_date')
                <li class="nav-item">
                    <a href="{{ route('showRejectedDate') }}" class="nav-link" id="offDate" onclick="setActive('offDate')">
                        <i class="nav-icon fas fa-calendar-minus"></i>
                        <p>Off Date</p>
                    </a>
                </li>
                @endcan

                @can('view notification')
                <li class="nav-item">
                    <a href="{{ route('showNotifations') }}" class="nav-link" id="notification" onclick="setActive('notification')">
                        <i class="nav-icon fa fa-bell"></i>
                        <p>Notification</p>
                    </a>
                </li>
                @endcan

                @can('view city')
                <li class="nav-item">
                    <a href="{{ route('cities.index') }}" class="nav-link" id="city">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>City</p>
                    </a>
                </li>
                @endcan

                @can('view user')
                <li class="nav-item">
                    <a href="{{ route('user') }}" class="nav-link" id="adminNav">
                        <i class="nav-icon far fa-user"></i>
                        <p>User</p>
                    </a>
                </li>
                @endcan

                <li class="nav-item">
                    <a href="#" onclick="$('#logout').submit(); return false;" class="nav-link" id="logoutLink">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form action="{{ route('logout') }}" id="logout" method="post" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    function setActive(id) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.getElementById(id).classList.add('active');
    }
</script>