@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $nav = [
        [
            'title' => null,
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'route' => 'home', 'match' => ['home'], 'admin' => false],
            ],
        ],
        [
            'title' => 'Data umum',
            'items' => [
                ['label' => 'Produk', 'icon' => 'fa-mug-hot', 'route' => 'products.index', 'match' => ['products', 'products/*']],
                ['label' => 'Kategori', 'icon' => 'fa-layer-group', 'route' => 'categories.index', 'match' => ['categories', 'categories/*']],
                ['label' => 'Diskon', 'icon' => 'fa-percent', 'route' => 'discounts.index', 'match' => ['discounts', 'discounts/*']],
                ['label' => 'Pengguna', 'icon' => 'fa-user-group', 'route' => 'users.index', 'match' => ['users', 'users/*']],
            ],
        ],
        [
            'title' => 'Keuangan',
            'items' => [
                ['label' => 'Pengeluaran', 'icon' => 'fa-wallet', 'route' => 'expenses.index', 'match' => ['expenses', 'expenses/*']],
            ],
        ],
        [
            'title' => 'Laporan',
            'items' => [
                ['label' => 'Laporan penjualan', 'icon' => 'fa-receipt', 'route' => 'order_reports.index', 'match' => ['order-reports*', 'order', 'order/*']],
                ['label' => 'Produk terlaris', 'icon' => 'fa-ranking-star', 'route' => 'top.products', 'match' => ['top-products*']],
                ['label' => 'Analisa keuangan', 'icon' => 'fa-chart-line', 'route' => 'financial.report', 'match' => ['laporan-keuangan*']],
            ],
        ],
    ];
@endphp

<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
    <a href="{{ route('home') }}" class="sidebar__brand">
        <img src="{{ asset('img/logo_arch_web.png') }}" alt="">
        <span class="sidebar__brand-text">
            <strong>ARCH</strong>
            <small>Manajemen resto</small>
        </span>
    </a>
    <button type="button" class="sidebar__close" data-sidebar-toggle aria-label="Tutup menu">
        <i class="fas fa-xmark"></i>
    </button>

    <nav class="sidebar__nav">
        @foreach ($nav as $group)
            @php
                $items = array_filter($group['items'], fn ($i) => ($i['admin'] ?? true) === false || $isAdmin);
            @endphp
            @if (count($items))
                <div class="nav-group">
                    @if ($group['title'])
                        <div class="nav-group__title">{{ $group['title'] }}</div>
                    @endif
                    @foreach ($items as $item)
                        @php $active = request()->is(...$item['match']); @endphp
                        <a href="{{ route($item['route']) }}" class="nav-link-x {{ $active ? 'is-active' : '' }}"
                            data-label="{{ $item['label'] }}" @if ($active) aria-current="page" @endif>
                            <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>
                            <span class="nav-link-x__label">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </nav>

    @php
        $sbUser = auth()->user();
        $sbInitials = collect(explode(' ', trim($sbUser->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    @endphp
    <div class="sidebar__foot">
        <span class="sidebar__avatar" aria-hidden="true">{{ $sbInitials ?: 'A' }}</span>
        <span class="sidebar__who">
            <strong>{{ $sbUser->name }}</strong>
            <small>{{ ucfirst($sbUser->role) }}</small>
        </span>
        <button type="button" class="sidebar__logout" data-label="Keluar" aria-label="Keluar"
            onclick="document.getElementById('logout-form').submit()">
            <i class="fas fa-arrow-right-from-bracket"></i>
        </button>
    </div>

</aside>
