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
                <li class="nav-item" id="settingNava">
                    <a href="#" class="nav-link" id="dashboard">
                        <i class="nav-icon fas fa-indent"></i>
                        <p>
                            Dashboard
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link" id="mainDash">
                                <i class="far fa-circle nav-icon ml-2"></i>
                                <p>Main Dashboard</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview ml-2">
                        @foreach (App\Models\MainEqub::all() as $equbType)
                            <li class="nav-item">
                                <a href="{{ route('viewMainEqub', $equbType->id) }}" class="nav-link" id="{{ $equbType->id }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ $equbType->name }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item" id="settingNavm">
                    <a href="#" class="nav-link" id="mem">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Members
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('showMember') }}" class="nav-link" id="nav-mem">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Members</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('showPendingMembers') }}" class="nav-link" id="pendingMem">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Members</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" id="settingNavp">
                    <a href="#" class="nav-link" id="pay">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Payments
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview ml-2">
                        <li class="nav-item">
                            <a href="{{ route('showAllPendingPayments') }}" class="nav-link" id="pendingPayments">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pending Payments</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('mainEqubs.index') }}" class="nav-link" id="city">
                        <i class="nav-icon fa fa-server"></i>
                        <p>Main Equbs</p>
                    </a>
                </li>
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('showEqubType') }}" class="nav-link" id="city">
                        <i class="nav-icon fa fa-network-wired"></i>
                        <p>Equb Type</p>
                    </a>
                </li>
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('showRejectedDate') }}" class="nav-link" id="offDate">
                        <i class="nav-icon fas fa-calendar-minus"></i>
                        <p>Off Date</p>
                    </a>
                </li>
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('showNotifations') }}" class="nav-link" id="notification">
                        <i class="nav-icon fa fa-bell"></i>
                        <p>Notification</p>
                    </a>
                </li>
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('cities.index') }}" class="nav-link" id="city">
                        <i class="nav-icon fas fa-calendar-minus"></i>
                        <p>City</p>
                    </a>
                </li>
                <li class="nav-item" id="nav-ety">
                    <a href="{{ route('user') }}" class="nav-link" id="adminNav">
                        <i class="nav-icon far fa-user"></i>
                        <p>User</p>
                    </a>
                </li>
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
                <li class="nav-item">
                    <a href="" onclick="$('#logout').submit(); return false;" class="nav-link" id="adminNav">
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