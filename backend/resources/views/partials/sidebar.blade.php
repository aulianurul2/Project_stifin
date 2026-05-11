<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                <img
                    src="assets/img/kaiadmin/logo_light.svg"
                    alt="navbar brand"
                    class="navbar-brand"
                    height="20"
                />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu</h4>
                </li>
                <li class="nav-item {{ request()->routeIs('kelola-klien') ? 'active' : '' }}">
                    <a href="{{ route('kelola-klien') }}">
                        <i class="fas fa-users"></i>
                        <p>Kelola Klien</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('pendaftaran-tes') ? 'active' : '' }}">
                    <a href="{{ route('pendaftaran-tes') }}">
                        <i class="fas fa-edit"></i>
                        <p>Pendaftaran Tes</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('jadwal-tes') ? 'active' : '' }}">
                    <a href="{{ route('jadwal-tes') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Jadwal Tes</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('hasil-tes') ? 'active' : '' }}">
                    <a href="{{ route('hasil-tes') }}">
                        <i class="fas fa-file-medical"></i>
                        <p>Hasil Tes</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <a href="{{ route('laporan.index') }}">
                        <i class="fas fa-chart-bar"></i>
                        <p>Laporan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link" style="color: #f87171;">
                            <i class="fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
