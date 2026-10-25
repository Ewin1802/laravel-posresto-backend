@php
    $user = auth()->user();
    $isAdminNav = $user->role === 'admin';
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

    // Daftar untuk pencarian cepat (Ctrl + K): halaman dan aksi.
    $cmdItems = [['group' => 'Halaman', 'label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'url' => route('home'), 'hint' => 'Ringkasan hari ini']];
    if ($isAdminNav) {
        $pages = [
            ['Produk', 'fa-mug-hot', 'products.index', 'Data umum'],
            ['Kategori', 'fa-layer-group', 'categories.index', 'Data umum'],
            ['Diskon', 'fa-percent', 'discounts.index', 'Data umum'],
            ['Pengguna', 'fa-user-group', 'users.index', 'Data umum'],
            ['Pengeluaran', 'fa-wallet', 'expenses.index', 'Keuangan biaya belanja'],
            ['Laporan penjualan', 'fa-receipt', 'order_reports.index', 'Laporan'],
            ['Produk terlaris', 'fa-ranking-star', 'top.products', 'Laporan'],
            ['Analisa keuangan', 'fa-chart-line', 'financial.report', 'Laporan'],
        ];
        foreach ($pages as [$l, $i, $r, $h]) {
            $cmdItems[] = ['group' => 'Halaman', 'label' => $l, 'icon' => $i, 'url' => route($r), 'hint' => $h];
        }
        $quick = [
            ['Tambah produk', 'fa-plus', 'products.create'],
            ['Catat pengeluaran', 'fa-wallet', 'expenses.create'],
            ['Tambah kategori', 'fa-folder-plus', 'categories.create'],
            ['Tambah diskon', 'fa-tag', 'discounts.create'],
            ['Tambah pengguna', 'fa-user-plus', 'users.create'],
        ];
        foreach ($quick as [$l, $i, $r]) {
            $cmdItems[] = ['group' => 'Aksi cepat', 'label' => $l, 'icon' => $i, 'url' => route($r), 'hint' => ''];
        }
        $today = now()->toDateString();
        $cmdItems[] = ['group' => 'Aksi cepat', 'label' => 'Penjualan hari ini', 'icon' => 'fa-calendar-day', 'url' => route('order_reports.index', ['start_date' => $today, 'end_date' => $today]), 'hint' => 'Laporan'];
    }
    $cmdItems[] = ['group' => 'Lainnya', 'label' => 'Lihat halaman publik', 'icon' => 'fa-store', 'url' => route('landing'), 'hint' => 'Tab baru', 'external' => true];
@endphp

<header class="topbar">
    <div class="topbar__bar">
        <button type="button" class="topbar__toggle" data-sidebar-toggle aria-label="Buka menu" aria-controls="sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <button type="button" class="topbar__collapse" data-sidebar-collapse aria-controls="sidebar"
            aria-expanded="true" aria-label="Ciutkan sidebar" title="Ciutkan / lebarkan sidebar (Ctrl + B)">
            <span class="hamb" aria-hidden="true"><i></i><i></i><i></i></span>
        </button>

        <nav class="topbar__crumbs" aria-label="Lokasi halaman">
            <a href="{{ route('home') }}" class="crumb-home" aria-label="Dashboard"><i class="fas fa-house"></i></a>
            @hasSection('crumb')
                <i class="fas fa-chevron-right sep" aria-hidden="true"></i><span class="crumb">@yield('crumb')</span>
            @endif
            <i class="fas fa-chevron-right sep" aria-hidden="true"></i><span class="crumb is-current" aria-current="page">@yield('title')</span>
        </nav>

        <div class="topbar__right">
            <div class="clock" aria-label="Waktu sekarang">
                <span class="clock__dot" aria-hidden="true"></span>
                <span class="clock__time" data-clock-time>{{ now()->format('H:i') }}</span>
                <span class="clock__date" data-clock-date>{{ now()->translatedFormat('D, d M') }} · WITA</span>
            </div>

            @if ($isAdminNav)
                @php $renew = \App\Support\CloudReminder::info(); @endphp
                <div class="dropdown">
                    <button type="button" class="renew-pill renew-pill--{{ $renew['level'] }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        aria-label="Perpanjangan server: {{ $renew['label'] }} lagi" title="Perpanjangan cloud server">
                        <i class="fas fa-server"></i>
                        <span class="renew-pill__text">{{ $renew['label'] }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right menu-x renew-menu">
                        <div class="renew-menu__head renew-menu__head--{{ $renew['level'] }}">
                            <span>Perpanjangan cloud server</span>
                            <strong>{{ $renew['days'] === 0 ? 'Jatuh tempo hari ini' : $renew['days'] . ' hari lagi' }}</strong>
                            <small>{{ $renew['due']->translatedFormat('l, d F Y') }} · Rp {{ number_format($renew['amount'], 0, ',', '.') }}/tahun</small>
                            <div class="renew__meter"><span style="width: {{ $renew['progress'] }}%"></span></div>
                        </div>
                        <a class="dropdown-item" href="{{ route('expenses.create', ['category' => 'listrik_air']) }}"><span class="menu-x__ic"><i class="fas fa-check"></i></span><span><b>Sudah bayar? Catat</b><small>Masuk ke Pengeluaran</small></span></a>
                    </div>
                </div>

                <div class="dropdown">
                    <button type="button" class="icon-btn icon-btn--red" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Buat baru" title="Buat baru">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right menu-x">
                        <div class="menu-x__title">Buat baru</div>
                        <a class="dropdown-item" href="{{ route('products.create') }}"><span class="menu-x__ic"><i class="fas fa-mug-hot"></i></span><span><b>Produk</b><small>Menu baru di aplikasi kasir</small></span></a>
                        <a class="dropdown-item" href="{{ route('expenses.create') }}"><span class="menu-x__ic"><i class="fas fa-wallet"></i></span><span><b>Pengeluaran</b><small>Belanja, gaji, listrik, dan lainnya</small></span></a>
                        <a class="dropdown-item" href="{{ route('users.create') }}"><span class="menu-x__ic"><i class="fas fa-user-plus"></i></span><span><b>Pengguna</b><small>Akun staf atau kasir</small></span></a>
                    </div>
                </div>
            @endif

            <a href="{{ route('landing') }}" class="icon-btn" target="_blank" rel="noopener" aria-label="Lihat halaman publik" title="Lihat halaman publik">
                <i class="fas fa-store"></i>
            </a>

            <div class="dropdown">
                <button type="button" class="user-chip" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Menu akun">
                    <span class="user-chip__avatar">{{ $initials ?: 'A' }}</span>
                    <span class="user-chip__text">
                        <span class="user-chip__name d-block">{{ $user->name }}</span>
                        <span class="user-chip__role d-block">{{ $user->role }}</span>
                    </span>
                    <i class="fas fa-chevron-down user-chip__caret" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right menu-x menu-x--account">
                    <div class="account-card">
                        <span class="account-card__avatar">{{ $initials ?: 'A' }}</span>
                        <div>
                            <strong>{{ $user->name }}</strong>
                            <span>{{ $user->email }}</span>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('home') }}"><span class="menu-x__ic"><i class="fas fa-gauge-high"></i></span><span><b>Dashboard</b></span></a>
                    <a class="dropdown-item" href="{{ route('landing') }}" target="_blank" rel="noopener"><span class="menu-x__ic"><i class="fas fa-store"></i></span><span><b>Halaman publik</b></span></a>
                    <button type="button" class="dropdown-item" data-cmdk-open><span class="menu-x__ic"><i class="fas fa-keyboard"></i></span><span><b>Pencarian cepat</b></span><kbd class="ml-auto"><span data-mod>Ctrl</span> K</kbd></button>
                    <div class="dropdown-divider"></div>
                    <button type="button" class="dropdown-item text-danger" onclick="document.getElementById('logout-form').submit()">
                        <span class="menu-x__ic"><i class="fas fa-arrow-right-from-bracket"></i></span><span><b>Keluar</b></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</header>

{{-- Pencarian cepat --}}
<div class="cmdk" id="cmdk" role="dialog" aria-modal="true" aria-label="Pencarian cepat" hidden>
    <div class="cmdk__backdrop" data-cmdk-close></div>
    <div class="cmdk__panel">
        <div class="cmdk__search">
            <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
            <input type="text" id="cmdkInput" placeholder="Ketik nama halaman atau aksi…" autocomplete="off"
                role="combobox" aria-expanded="true" aria-controls="cmdkList" aria-autocomplete="list">
            <kbd>Esc</kbd>
        </div>
        <div class="cmdk__list" id="cmdkList" role="listbox">
            @foreach (collect($cmdItems)->groupBy('group') as $group => $items)
                <div class="cmdk__group" data-cmdk-group>
                    <div class="cmdk__group-title">{{ $group }}</div>
                    @foreach ($items as $item)
                        <a href="{{ $item['url'] }}" class="cmdk__item" role="option" data-cmdk-item
                            data-search="{{ \Illuminate\Support\Str::lower($item['label'] . ' ' . $item['hint'] . ' ' . $group) }}"
                            @if (!empty($item['external'])) target="_blank" rel="noopener" @endif>
                            <span class="cmdk__ic"><i class="fas {{ $item['icon'] }}"></i></span>
                            <span class="cmdk__label">{{ $item['label'] }}</span>
                            @if ($item['hint'])
                                <span class="cmdk__hint">{{ $item['hint'] }}</span>
                            @endif
                            <i class="fas fa-arrow-turn-down cmdk__enter" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            @endforeach
            <div class="cmdk__empty" hidden>Tidak ada yang cocok. Coba kata lain, misalnya “laporan” atau “bahan”.</div>
        </div>
        <div class="cmdk__foot">
            <span><kbd>↑</kbd><kbd>↓</kbd> pilih</span>
            <span><kbd>Enter</kbd> buka</span>
            <span><kbd>Esc</kbd> tutup</span>
        </div>
    </div>
</div>
