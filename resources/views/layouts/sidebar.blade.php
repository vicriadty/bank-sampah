<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-recycle"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Bank Sampah</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading mt-3">
        Manajemen Data
    </div>
    <!-- Divider -->
    <hr class="sidebar-divider">
    <!-- Nav Item - Tables -->
    <li class="nav-item {{ request()->routeIs('admin.nasabah.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.nasabah.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Nasabah</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.pengepul.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.pengepul.index') }}">
            <i class="fas fa-fw fa-truck"></i>
            <span>Pengepul</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.stok-sampah.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.stok-sampah.index') }}">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Stok Sampah</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.sampah.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.sampah.index') }}">
            <i class="fas fa-fw fa-trash"></i>
            <span>Sampah</span></a>
    </li>
    <!-- Heading -->
    <div class="sidebar-heading mt-3">
        Transaksi
    </div>
    <!-- Divider -->
    <hr class="sidebar-divider">
    <!-- Nav Item - Tables -->
    <li class="nav-item {{ request()->routeIs('admin.setoran.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.setoran.index') }}">
            <i class="fas fa-fw fa-upload"></i>
            <span>Setor Sampah</span></a>
    </li>
    <!-- Nav Item - Tables -->
    <li class="nav-item {{ request()->routeIs('admin.tarik-saldo.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.tarik-saldo.index') }}">
            <i class="fas fa-fw fa-download"></i>
            <span>Tarik Saldo</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.penjualan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.penjualan.index') }}">
            <i class="fas fa-fw fa-cash-register"></i>
            <span>Penjualan Sampah</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
