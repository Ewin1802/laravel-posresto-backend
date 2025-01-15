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
                        <i class="fas fa-fire"></i><span>User</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('produ*') ? 'active' : '' }}">
                    <a href="{{ route('products.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Product</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('categories*') ? 'active' : '' }}">
                    <a href="{{ route('categories.index') }}" class="nav-link">
                        <i class="fas fa-fire"></i><span>Category</span>
                    </a>
                </li>
                {{-- <ul class="dropdown-menu">
                    <li class=''>
                        <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                    </li>
                </ul>

                <ul class="dropdown-menu">
                    <li class=''>
                        <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                    </li>
                </ul>

                <ul class="dropdown-menu">
                    <li class=''>
                        <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                    </li>
                </ul> --}}

            </li>

</div>
