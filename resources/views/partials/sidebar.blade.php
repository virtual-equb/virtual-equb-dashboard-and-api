<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ url('dist/img/PNG/VirtualEqubLogoIcon.png') }}" alt="AdminLTE Logo" class="brand-image elevation-3" style="opacity: .8">
        <strong class="brand-text font-weight-light">Virtual Equb</strong>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img 
                    class="img-circle elevation-2" 
                    src="{{ Auth::check() && Auth::user()->profile_photo_path 
                        ? asset(Auth::user()->profile_photo_path) 
                        :  asset('default-profile.png') }}" 
                    alt="Profile Photo"
                    width="48"
                    height="48"
                >
            </div>
            <div class="info ml-2">
                <strong class="d-block font-weight-bold text-white" style="word-wrap: break-word; white-space: normal;">
                    {{ Auth::check() ?Auth::user()->name : 'Guest User' }}
                </strong>
                <small class="text-light" style="word-wrap: break-word; white-space: normal;">
                    {{ Auth::user()->role ?? 'Not Assigned' }}
                </small>
            </div>
        </div>
        
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard Section -->
                @can('view dashboard')
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="dashboardLink" onclick="setActive('dashboardLink')">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ml-2">
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link" id="mainDash" onclick="setActive('mainDash')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Main Dashboard</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview ml-2">
                            {{-- @foreach (App\Models\MainEqub::all() as $equbType)
                                <li class="nav-item">
                                    <a href="#" class="nav-link" onclick="setActive('equb-{{ $mainEqub->id }}')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ $mainEqub->name }}<i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview ml-2">
                                        @if ($mainEqub->subEqub && $mainEqub->subEqub->count())
                                            @foreach ($mainEqub->subEqub as $equbType)
                                                <li class="nav-item">
                                                    <a href="{{ url('equbTypeDashboard/' . $equbType->id) }}" class="nav-link" id="{{ $equbType->id }}">
                                                        <i class="far fa-circle nav-icon"></i>
                                                        <p>{{ $equbType->name }}</p>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="nav-item">
                                                <p class="nav-link">No Equb Types available</p>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endforeach --}}
                            @foreach (App\Models\MainEqub::with('subEqub')->get() as $mainEqub)
                            <li class="nav-item">
                                <a href="#" class="nav-link" onclick="setActive('equb-{{ $mainEqub->id }}')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ $mainEqub->name }}<i class="fas fa-angle-left right"></i></p>
                                </a>
                                <ul class="nav nav-treeview ml-2">
                                    @if ($mainEqub->subEqub && $mainEqub->subEqub->count())
                                        @foreach ($mainEqub->subEqub as $equbType)
                                            <li class="nav-item">
                                                <a href="{{ url('equbTypeDashboard/' . $equbType->id) }}" class="nav-link" id="{{ $equbType->id }}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>{{ $equbType->name }}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="nav-item">
                                            <p class="nav-link">No Equb Types available</p>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endforeach
                        </ul>
                    </li>
                @endcan

                <!-- Members Section -->
                @can('view member')
                    <li class="nav-item">
                        <a href="#" class="nav-link" id="membersLink" onclick="setActive('membersLink')">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Members
                                <i class="fas fa-angle-left right"></i>
                            </p>
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

                <!-- Payment Section -->
                @can('view payment')
                    <li class="nav-item has-treeview {{ request()->is('payments*') || request()->is('equbTaker*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link" id="paymentsLink" onclick="setActive('paymentsLink')">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>
                                Payments
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ml-2" style="{{ request()->is('payments*') || request()->is('equbTaker*') ? 'display: block;' : 'display: none;' }}">
                            <li class="nav-item">
                                <a href="{{ route('showAllPendingPayments') }}" class="nav-link {{ request()->is('payments/show-all-pending-payment') ? 'active' : '' }}" id="showAllPendingPayments" onclick="setActive('showAllPendingPayments')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pending Payments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('showAllPaidPayments') }}" class="nav-link {{ request()->is('payments/show-all-paid-payment') ? 'active' : '' }}" id="showAllPaidPayments" onclick="setActive('showAllPaidPayments')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Paid Payments</p>
                                </a>
                            </li>
                            @can('view equb_taker')
                                <li class="nav-item">
                                    <a href="{{ route('showEqubTaker') }}" class="nav-link {{ request()->is('payments/equb-taker') ? 'active' : '' }}" id="showEqubTaker" onclick="setActive('showEqubTaker')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Equb Taker</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Main Equb Section -->
                @can('view main_equb')
                    <li class="nav-item">
                        <a href="{{ route('mainEqubs.index') }}" class="nav-link" id="mainEqubs" onclick="setActive('mainEqubs')">
                            <i class="nav-icon fa fa-server"></i>
                            <p>Main Equbs</p>
                        </a>
                    </li>
                @endcan

                <!-- Equb Type Section -->
                @can('view equb_type')
                    <li class="nav-item">
                        <a href="{{ route('showEqubType') }}" class="nav-link" id="showEqubType" onclick="setActive('showEqubType')">
                            <i class="nav-icon fa fa-network-wired"></i>
                            <p>Equb Type</p>
                        </a>
                    </li>
                @endcan

                <!-- Off Date Section -->
                @can('view rejected_date')
                    <li class="nav-item">
                        <a href="{{ route('showRejectedDate') }}" class="nav-link" id="offDate" onclick="setActive('offDate')">
                            <i class="nav-icon fas fa-calendar-minus"></i>
                            <p>Off Date</p>
                        </a>
                    </li>
                @endcan

                <!-- Notification Section -->
                @can('view notification')
                    <li class="nav-item">
                        <a href="{{ route('showNotifations') }}" class="nav-link " id="notification" onclick="setActive('notification')">
                            <i class="nav-icon fa fa-bell"></i>
                            <p>Notification</p>
                        </a>
                    </li>
                @endcan

                <!-- User Section -->
                @can('view user')
                    <li class="nav-item">
                        <a href="{{ route('user') }}" class="nav-link" id="adminNav" onclick="setActive('adminNav')">
                            <i class="nav-icon far fa-user"></i>
                            <p>User</p>
                        </a>
                    </li>
                @endcan

                <!-- City Section -->
                @can('view city')
                    <li class="nav-item has-treeview {{ request()->is('cities*') || request()->is('subcities*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link" id="locationsLink" onclick="setActive('locationsLink')">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>
                                Locations
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ml-2" style="{{ request()->is('cities*') || request()->is('subcities*') ? 'display: block;' : 'display: none;' }}">
                            <li class="nav-item">
                                <a href="{{ route('cities.index') }}" class="nav-link {{ request()->is('cities') ? 'active' : '' }}" id="cityLink" onclick="setActive('cityLink')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>City</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('subcities.index') }}" class="nav-link {{ request()->is('subcities') ? 'active' : '' }}" id="subcityLink" onclick="setActive('subcityLink')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sub City</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Setting Section -->
                @can('view permission')
                    <li class="nav-item has-treeview {{ request()->is('settings/permission*') || request()->is('roles*')  || request()->is('permission*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link" id="settingLink" onclick="setActive('settingLink')">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>
                                Settings
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview ml-2" style="{{ request()->is('settings/permission*') || request()->is('roles*')  || request()->is('permission*') ? 'display: block;' : 'display: none;' }}">
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->is('roles') ? 'active' : '' }}" id="roleLink" onclick="setActive('roleLink')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Role</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('permission.index') }}" class="nav-link {{ request()->is('permission') ? 'active' : '' }}" id="permissionLink" onclick="setActive('permissionLink')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Permission</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->is('settings/permission') ? 'active' : '' }}" id="roleAndPermissionLink" onclick="setActive('roleAndPermissionLink')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Roles & Permissions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
               
                <!-- Activity Log Section -->
                <li class="nav-item">
                    <a href="{{ route('showActivityLog') }}" class="nav-link" id="activity_log" onclick="setActive('activity_log')">
                        <i class="nav-icon fa fa-chart-line"></i>
                        <p>
                            Activity Log
                        </p>
                    </a>
                </li>
             
                <!-- Logout Section -->
                <li class="nav-item">
                    <a href="#" onclick="$('#logout').submit(); return false;" class="nav-link" id="logoutLink">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form action="{{ route('logout') }}" id="logout" method="post" style="display: none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    function setActive(id) {
        // Remove 'active' class from all nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Add 'active' class to the clicked link
        document.getElementById(id).classList.add('active');
    }
</script>