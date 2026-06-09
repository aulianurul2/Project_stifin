<!-- Sidebar -->
<div class="sidebar" data-background-color="dark2">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="green">
            <a href="{{ route('dashboard') }}" class="logo">
                <img
                    src="assets/img/kaiadmin/logo_light.png"
                    alt="navbar brand"
                    class="navbar-brand"
                    height="100"
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
                <li class="nav-item {{ request()->routeIs('kelola-konten.*') ? 'active' : '' }}">
                    <a href="{{ route('kelola-konten.index') }}">
                        <i class="fas fa-laptop"></i>
                        <p>Kelola Konten</p>
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
                <li class="nav-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                    <a href="{{ route('profil.index') }}">
                        <i class="fas fa-user-cog"></i>
                        <p>Edit Profil</p>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Tombol logout — trigger modal konfirmasi --}}
                    <a href="#"
                       onclick="document.getElementById('modalLogout').classList.add('show'); document.getElementById('modalLogout').style.display='flex';"
                       style="color: #f87171 !important;">
                        <i class="fas fa-sign-out-alt" style="color: #f87171 !important;"></i>
                        <p style="color: #f87171 !important;">Logout</p>
                    </a>
                    {{-- Form logout tersembunyi, disubmit oleh modal --}}
                    <form id="formLogout" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->


{{-- ══════════════════════════════════════════════
     MODAL KONFIRMASI LOGOUT
══════════════════════════════════════════════ --}}
<div id="modalLogout"
     style="display:none; position:fixed; inset:0; z-index:9999;
            align-items:center; justify-content:center;
            background:rgba(0,0,0,.45); backdrop-filter:blur(3px);">

    <div style="background:#fff; border-radius:20px; overflow:hidden;
                box-shadow:0 24px 80px rgba(0,0,0,.22);
                width:100%; max-width:380px; margin:16px;">

        {{-- Header merah --}}
        <div style="background:linear-gradient(135deg,#ef5350 0%,#b71c1c 100%);
                    padding:28px 28px 20px; text-align:center; position:relative; overflow:hidden;">
            {{-- Dekorasi lingkaran --}}
            <div style="position:absolute;top:-40px;right:-40px;width:150px;height:150px;
                        background:rgba(255,255,255,.07);border-radius:50%;"></div>
            <div style="position:absolute;bottom:-50px;left:-20px;width:120px;height:120px;
                        background:rgba(255,255,255,.05);border-radius:50%;"></div>

            <div style="position:relative;z-index:2;">
                {{-- Ikon --}}
                <div style="width:64px;height:64px;background:rgba(255,255,255,.2);
                            border-radius:50%;display:flex;align-items:center;justify-content:center;
                            margin:0 auto 14px;backdrop-filter:blur(4px);">
                    <i class="fas fa-sign-out-alt" style="font-size:1.8rem;color:#fff;"></i>
                </div>
                <p style="color:#fff;font-size:1.15rem;font-weight:800;margin:0 0 4px;">Konfirmasi Logout</p>
                <p style="color:rgba(255,255,255,.8);font-size:.82rem;margin:0;">Anda yakin ingin keluar dari sistem?</p>
            </div>
        </div>

        {{-- Body --}}
        <div style="padding:20px 28px 8px;">
            <div style="background:#fff5f5;border:1px solid #ffcdd2;border-radius:10px;
                        padding:10px 14px;font-size:.82rem;color:#b71c1c;
                        display:flex;align-items:flex-start;gap:8px;">
                <i class="fas fa-info-circle" style="margin-top:1px;flex-shrink:0;"></i>
                <span>Sesi Anda akan diakhiri dan Anda akan diarahkan ke halaman login.</span>
            </div>
        </div>

        {{-- Footer tombol --}}
        <div style="padding:16px 28px 24px;display:flex;gap:10px;justify-content:center;">
            <button type="button"
                    onclick="document.getElementById('modalLogout').style.display='none';"
                    style="flex:1;max-width:140px;padding:10px 20px;border-radius:10px;
                           font-weight:600;font-size:.875rem;border:1.5px solid #dee2e6;
                           background:#fff;color:#495057;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.background='#f8f9fa'"
                    onmouseout="this.style.background='#fff'">
                Batal
            </button>
            <button type="button"
                    onclick="document.getElementById('formLogout').submit();"
                    style="flex:1;max-width:180px;padding:10px 20px;border-radius:10px;
                           font-weight:600;font-size:.875rem;border:none;
                           background:linear-gradient(135deg,#ef5350 0%,#b71c1c 100%);
                           color:#fff;cursor:pointer;transition:all .2s;
                           box-shadow:0 4px 12px rgba(183,28,28,.3);"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 18px rgba(183,28,28,.4)'"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(183,28,28,.3)'">
                <i class="fas fa-sign-out-alt me-1"></i> Ya, Logout
            </button>
        </div>

    </div>
</div>

<script>
    // Tutup modal jika klik di luar area konten
    document.getElementById('modalLogout').addEventListener('click', function (e) {
        if (e.target === this) this.style.display = 'none';
    });
    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.getElementById('modalLogout').style.display = 'none';
        }
    });
</script>