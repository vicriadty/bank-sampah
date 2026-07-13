<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('nasabah.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-recycle"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Bank Sampah</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('nasabah.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nasabah.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading mt-3">
        Riwayat Transaksi
    </div>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('nasabah.riwayat-transaksi') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nasabah.riwayat-transaksi') }}">
            <i class="fas fa-fw fa-upload"></i>
            <span>Setoran</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('nasabah.riwayat-konversi') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nasabah.riwayat-konversi') }}">
            <i class="fas fa-fw fa-coins"></i>
            <span>Konversi Emas</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading mt-3">
        Informasi
    </div>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('nasabah.info-saldo') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nasabah.info-saldo') }}">
            <i class="fas fa-fw fa-wallet"></i>
            <span>Dompet</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('nasabah.data-sampah') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nasabah.data-sampah') }}">
            <i class="fas fa-fw fa-trash"></i>
            <span>Data Sampah</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading mt-3">
        Pengaturan
    </div>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('nasabah.profile') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('nasabah.profile') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Profil Nasabah</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
