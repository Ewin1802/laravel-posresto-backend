<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">ARCH Manajemen</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">ARCH</a>
        </div>
        <ul class="sidebar-menu">

            <li class="nav-item dropdown">
                {{-- <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a> --}}
                {{-- <ul class="dropdown-menu">
                    <li class='{{ Request::is('dashboard-general-dashboard') ? 'active' : '' }}'>
                        <a class="nav-link" href="{{ url('dashboard-general-dashboard') }}">General Dashboard</a>
                    </li>
                </ul> --}}
                <li class="nav-item {{ Request::is('user*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Pengguna</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('produ*') ? 'active' : '' }}">
                    <a href="{{ route('products.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Produk</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('categories*') ? 'active' : '' }}">
                    <a href="{{ route('categories.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Kategori</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('discounts*') ? 'active' : '' }}">
                    <a href="{{ route('discounts.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Diskon</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('orde*') ? 'active' : '' }}">
                    <a href="{{ route('order_reports.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Laporan Fulus</span>
                    </a>
                </li>



            </li>

</div>
