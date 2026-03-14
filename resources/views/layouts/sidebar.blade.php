<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-recycle"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Bank Sampah</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="/dashboard">
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
    <li class="nav-item {{ request()->is('nasabah') ? 'active' : '' }}">
        <a class="nav-link" href="/nasabah">
            <i class="fas fa-fw fa-users"></i>
            <span>Nasabah</span></a>
    </li>
    <li class="nav-item {{ request()->is('pengepul') ? 'active' : '' }}">
        <a class="nav-link" href="/pengepul">
            <i class="fas fa-fw fa-truck"></i>
            <span>Pengepul</span></a>
    </li>
    <li class="nav-item {{ request()->is('stok-sampah') ? 'active' : '' }}">
        <a class="nav-link" href="/stok-sampah">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Stok Sampah</span></a>
    </li>
    <li class="nav-item {{ request()->is('sampah') ? 'active' : '' }}">
        <a class="nav-link" href="/sampah">
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
    <li class="nav-item {{ request()->is('setor-sampah') ? 'active' : '' }}">
        <a class="nav-link" href="/setor-sampah">
            <i class="fas fa-fw fa-upload"></i>
            <span>Setor Sampah</span></a>
    </li>
    <!-- Nav Item - Tables -->
    <li class="nav-item {{ request()->is('tarik-saldo') ? 'active' : '' }}">
        <a class="nav-link" href="/tarik-saldo">
            <i class="fas fa-fw fa-download"></i>
            <span>Tarik Saldo</span></a>
    </li>
    <li class="nav-item {{ request()->is('penjualan-sampah') ? 'active' : '' }}">
        <a class="nav-link" href="/penjualan-sampah">
            <i class="fas fa-fw fa-cash-register"></i>
            <span>Penjualan Sampah</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->


</ul>
