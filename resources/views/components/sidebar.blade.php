<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">ARCH Manajemen</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">ARCH</a>
        </div>
        <ul class="sidebar-menu">


            <li class="nav-item dropdown {{ Request::is('user*', 'categories*', 'produ*', 'discounts*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-file-alt"></i>  <!-- Ubah ikon ke file-alt -->
                    <span>Data Umum</span>
                </a>
                <ul class="dropdown-menu">

                    <li class="nav-item {{ Request::is('user*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="nav-link">
                            <i class="fas fa-fire"></i><span>Pengguna</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('categories*') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}" class="nav-link">
                            <i class="fas fa-fire"></i><span>Kategori</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('produ*') ? 'active' : '' }}">
                        <a href="{{ route('products.index') }}" class="nav-link">
                            <i class="fas fa-fire"></i><span>Produk</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('discounts*') ? 'active' : '' }}">
                        <a href="{{ route('discounts.index') }}" class="nav-link">
                            <i class="fas fa-fire"></i><span>Diskon</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ Request::is('bahan*','inventories*','inventory_out*',) ? 'active' : '' }}">
                {{-- <a href="javascript:void(0);" class="nav-link has-dropdown">
                    <i class="fas fa-tasks"></i>
                    <span>Persediaan</span>
                </a> --}}
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-cogs"></i>  <!-- Ubah ikon ke file-alt -->
                    <span>Data Persediaan</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('bahan*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('bahans.index') }}">
                            <i class="fas fa-fire"></i><span>Nama Bahan</span>
                        </a>
                    </li>
                </ul>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('inventories*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('inventories.index') }}">
                            <i class="fas fa-fire"></i><span>Persediaan Masuk</span>
                        </a>
                    </li>
                </ul>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('inventory_out*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('inventory_out.index') }}">
                            <i class="fas fa-fire"></i><span>Persediaan Keluar</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ Request::is('orde*', 'top*','inventory-reports*', 'laporan-keuangan*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-chart-bar"></i> <!-- Ubah ikon ke chart-bar -->
                    <span>Laporan</span>
                </a>
                <ul class="dropdown-menu">

                    <li class="nav-item {{ Request::is('orde*') ? 'active' : '' }}">
                        <a href="{{ route('order_reports.index') }}" class="nav-link">
                            <i class="fas fa-coins"></i><span>Laporan Order</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('top*') ? 'active' : '' }}">
                        <a href="{{ route('top.products') }}" class="nav-link">
                            <i class="fas fa-boxes-stacked"></i><span>Laporan Produk</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('inventory-reports*') ? 'active' : '' }}">
                        <a href="{{ route('inventory.reports') }}" class="nav-link">
                            <i class="fas fa-receipt"></i><span>Laporan Persediaan</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('laporan-keuangan*') ? 'active' : '' }}">
                        <a href="{{ route('financial.report') }}" class="nav-link">
                            <i class="fas fa-chart-line"></i> <span>Analisa Keuangan</span>
                        </a>
                    </li>

                </ul>
            </li>



</div>
