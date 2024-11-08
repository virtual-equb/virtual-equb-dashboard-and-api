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
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard Section -->
                <li class="nav-item" id="settingNava">
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
<<<<<<< HEAD

                <!-- Members Section -->
                <li class="nav-item">
                    <a href="#" class="nav-link" id="membersLink" onclick="setActive('membersLink')">
=======
                @can('view member')
                <li class="nav-item" id="settingNavm">
                    <a href="#" class="nav-link" id="mem">
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
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
<<<<<<< HEAD

                <!-- Payments Section -->
                <li class="nav-item">
                    <a href="#" class="nav-link" id="paymentsLink" onclick="setActive('paymentsLink')">
                        <i class="nav-icon fas fa-credit-card"></i>
=======
                @endcan
                @can('view payment')
                <li class="nav-item" id="settingNavp">
                    <a href="#" class="nav-link" id="pay">
                        <i class="nav-icon fas fa-users"></i>
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                        <p>
                            Payments
                            <i class="fas fa-angle-left right"></i>
                        </p>
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
<<<<<<< HEAD

                <!-- Main Equbs Section -->
                <li class="nav-item">
                    <a href="{{ route('mainEqubs.index') }}" class="nav-link" id="mainEqubs" onclick="setActive('mainEqubs')">
=======
                @endcan
                @can('view main_equb')
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('mainEqubs.index') }}" class="nav-link" id="city">
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                        <i class="nav-icon fa fa-server"></i>
                        <p>Main Equbs</p>
                    </a>
                </li>
<<<<<<< HEAD

                <!-- Equb Type Section -->
                <li class="nav-item">
                    <a href="{{ route('showEqubType') }}" class="nav-link" id="showEqubType" onclick="setActive('showEqubType')">
=======
                @endcan
                @can('view equb_type')
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('showEqubType') }}" class="nav-link" id="city">
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                        <i class="nav-icon fa fa-network-wired"></i>
                        <p>Equb Type</p>
                    </a>
                </li>
<<<<<<< HEAD

                <!-- Off Date Section -->
                <li class="nav-item">
                    <a href="{{ route('showRejectedDate') }}" class="nav-link" id="offDate" onclick="setActive('offDate')">
=======
                @endcan
                @can('view rejected_date ')
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('showRejectedDate') }}" class="nav-link" id="offDate">
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                        <i class="nav-icon fas fa-calendar-minus"></i>
                        <p>Off Date</p>
                    </a>
                </li>
<<<<<<< HEAD

                <!-- Notifications Section -->
                <li class="nav-item">
                    <a href="{{ route('showNotifations') }}" class="nav-link" id="notification" onclick="setActive('notification')">
=======
                @endcan
                @can('view notification')
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('showNotifations') }}" class="nav-link" id="notification">
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                        <i class="nav-icon fa fa-bell"></i>
                        <p>Notification</p>
                    </a>
                </li>
<<<<<<< HEAD

                <!-- User Section -->
                <li class="nav-item">
                    <a href="{{ route('user') }}" class="nav-link" id="adminNav" onclick="setActive('adminNav')">
=======
                @endcan
                @can('view city')
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('cities.index') }}" class="nav-link" id="city">
                        <i class="nav-icon fas fa-calendar-minus"></i>
                        <p>City</p>
                    </a>
                </li>
                @endcan
                @can('view user')
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('user') }}" class="nav-link" id="adminNav">
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                        <i class="nav-icon far fa-user"></i>
                        <p>User</p>
                    </a>
                </li>
<<<<<<< HEAD

                <!-- Locations Section -->
<!-- Locations Section -->
<li class="nav-item">
    <a href="#" class="nav-link {{ '' }}" id="locationsLink" onclick="setActive('locationsLink')">
        <i class="nav-icon fas fa-map-marker-alt"></i>
        <p>
            Locations
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview ml-2" style="{{ request()->is('cities*') ? 'display: block;' : 'display: none;' }}">
        <li class="nav-item">
            <a href="{{ route('cities.index') }}" class="nav-link {{ request()->is('cities') ? 'active' : '' }}" id="cityLink" onclick="setActive('cityLink')">
                <i class="nav-icon fas fa-city"></i>
                <p>City</p>
            </a>
        </li>
    </ul>
</li>
<!-- Settings Section -->
<li class="nav-item">
    <a href="#" class="nav-link {{ request()->is('permission') && request()->is('roles') ? 'active' : '' }}" id="settingsLink" onclick="setActive('settingsLink')">
        <i class="nav-icon fas fa-cog"></i>
        <p>
            Settings
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview ml-2" style="{{ request()->is('permission') || request()->is('roles') ? 'display: block;' : 'display: none;' }}">
        <li class="nav-item">
            <a href="{{ url('settings/permission') }}" class="nav-link {{ request()->is('permission') ? 'active' : '' }}" id="nav-permissions" onclick="setActive('nav-permissions')">
                <i class="far fa-circle nav-icon"></i>
                <p>Permissions</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ url('roles') }}" class="nav-link {{ request()->is('roles') ? 'active' : '' }}" id="nav-roles" onclick="setActive('nav-roles')">
                <i class="far fa-circle nav-icon"></i>
                <p>Roles</p>
            </a>
        </li>
    </ul>
</li>

          

                <!-- Logout Section -->
=======
                @endcan
                <li class="nav-item" id="settingNavm">
                    <a href="#" class="nav-link" id="settingsLink">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Settings
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('permissions.index') }}" class="nav-link" id="nav-permissions">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="" class="nav-link" id="nav-languages">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Language</p>
                            </a>
                        </li>
                    </ul>
                </li>
>>>>>>> 4462aa37440f29ff3a81e81ef7ed46382d4c87cb
                <li class="nav-item">
                    <a href="#" onclick="$('#logout').submit(); return false;" class="nav-link" id="logoutLink">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" id="logout" method="post" style="display: none">
                        @csrf
                        <button type="submit" class="btn btn-secondary nav-link text-white text-align-start">
                            Logout
                        </button>
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