@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $hour = (int) now()->format('H');
    $greet = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));
    $firstName = explode(' ', trim(auth()->user()->name))[0];
    $pct = function ($now, $before) {
        if ($before <= 0) return null;
        return (($now - $before) / $before) * 100;
    };
@endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $greet }}, {{ $firstName }}</h1>
            <p class="page-head__lead">
                @if ($isAdmin)
                    Ini ringkasan penjualan dan persediaan ARCH per {{ now()->translatedFormat('H:i') }}.
                @else
                    Gunakan aplikasi kasir untuk mencatat transaksi. Menu laporan khusus untuk admin.
                @endif
            </p>
        </div>
        @if ($isAdmin)
            <div class="page-head__actions">
                <a href="{{ route('order_reports.index') }}" class="btn btn-light"><i class="fas fa-receipt"></i> Laporan penjualan</a>
                <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Produk baru</a>
            </div>
        @endif
    </div>

    @include('layouts.alert')

    @include('pages.partials.cloud-reminder')

    @if ($isAdmin)
        @php
            $dDay = $pct($today['net'], $yesterday['net']);
            $dMonth = $pct($month['net'], $prevMonth['net']);
        @endphp

        {{-- Signature: struk penjualan hari ini --}}
        <section class="receipt" aria-label="Penjualan hari ini">
            <div class="receipt__top">
                <div>
                    <div class="receipt__label">Penjualan bersih hari ini</div>
                    <div class="receipt__amount"><small>Rp</small>{{ $rp($today['net']) }}</div>
                    <div class="receipt__label">
                        @if (!is_null($dDay))
                            <span class="delta {{ $dDay >= 0 ? 'delta--up' : 'delta--down' }}">
                                <i class="fas fa-arrow-{{ $dDay >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($dDay), 1, ',', '.') }}%
                            </span>
                            dibanding kemarin (Rp {{ $rp($yesterday['net']) }})
                        @elseif ($yesterday['net'] <= 0 && $today['net'] > 0)
                            Kemarin belum ada penjualan.
                        @else
                            Belum ada transaksi tercatat hari ini.
                        @endif
                    </div>
                </div>
                <div class="receipt__date">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
            <hr class="receipt__rule">
            <div class="receipt__grid">
                <div><span>Transaksi</span><strong>{{ $rp($today['orders']) }}</strong></div>
                <div><span>Item terjual</span><strong>{{ $rp($today['items']) }}</strong></div>
                <div><span>Rata-rata per transaksi</span><strong>Rp {{ $rp($today['avg']) }}</strong></div>
                <div>
                    <span>Bulan ini</span>
                    <strong>Rp {{ $rp($month['net']) }}</strong>
                </div>
            </div>
        </section>

        <div class="row">
            <div class="col-lg-8">
                <section class="panel">
                    <div class="panel__head">
                        <div>
                            <h2 class="panel__title">Penjualan 14 hari terakhir</h2>
                            <p class="panel__sub">Penjualan bersih per hari. Batang merah adalah hari ini.</p>
                        </div>
                    </div>
                    <div class="panel__body">
                        <div class="chart-box"><canvas id="trendChart" aria-label="Grafik penjualan 14 hari"></canvas></div>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="panel">
                    <div class="panel__head">
                        <div>
                            <h2 class="panel__title">Bulan {{ now()->translatedFormat('F') }}</h2>
                            <p class="panel__sub">Tanggal 1–{{ now()->day }}, dibanding periode sama bulan lalu</p>
                        </div>
                    </div>
                    <div class="panel__body">
                        <div class="stat__value" style="margin-top:0"><small>Rp</small>{{ $rp($month['net']) }}</div>
                        <div class="stat__meta mb-3">
                            @if (!is_null($dMonth))
                                <span class="delta {{ $dMonth >= 0 ? 'delta--up' : 'delta--down' }}">
                                    {{ $dMonth >= 0 ? '+' : '−' }}{{ number_format(abs($dMonth), 1, ',', '.') }}%
                                </span>
                                dari Rp {{ $rp($prevMonth['net']) }}
                            @else
                                Belum ada pembanding bulan lalu.
                            @endif
                        </div>

                        @php $monthProfit = $month['net'] - $monthExpense; @endphp
                        <ul class="ledger mb-3">
                            <li><span>Transaksi</span><span class="num">{{ $rp($month['orders']) }}</span></li>
                            <li><span>Pengeluaran</span><span class="num text-red">− Rp {{ $rp($monthExpense) }}</span></li>
                            <li class="is-total">
                                <span>Laba bersih</span>
                                <span class="num {{ $monthProfit < 0 ? 'text-red' : 'text-sage' }}" style="font-size:20px">{{ $monthProfit < 0 ? '−' : '' }}Rp {{ $rp(abs($monthProfit)) }}</span>
                            </li>
                        </ul>
                        <a href="{{ route('financial.report') }}" class="btn btn-light btn-sm btn-block mb-4">Buka analisa keuangan</a>

                        <div class="cell-sub mb-2">Metode pembayaran</div>
                        @php $payTotal = max(1, $payments->sum('net')); @endphp
                        @forelse ($payments as $p)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between" style="font-size:13.5px">
                                    <span class="font-weight-bold text-capitalize">{{ $p->method ?: 'lainnya' }}</span>
                                    <span class="num">Rp {{ $rp($p->net) }} <span class="text-muted">· {{ $p->orders }}x</span></span>
                                </div>
                                <div class="meter {{ $p->method === 'cash' ? '' : 'meter--gold' }} mt-1"><span style="width: {{ max(2, ($p->net / $payTotal) * 100) }}%"></span></div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada transaksi bulan ini.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <section class="panel">
                    <div class="panel__head">
                        <div>
                            <h2 class="panel__title">Transaksi terbaru</h2>
                            <p class="panel__sub">Tujuh transaksi terakhir dari aplikasi kasir</p>
                        </div>
                        <a href="{{ route('order_reports.index') }}" class="btn btn-ghost btn-sm">Lihat semua</a>
                    </div>
                    @if ($recentOrders->isEmpty())
                        <div class="empty">
                            <div class="empty__icon"><i class="fas fa-receipt"></i></div>
                            <h3>Belum ada transaksi</h3>
                            <p>Transaksi dari aplikasi kasir akan muncul di sini.</p>
                        </div>
                    @else
                        <div class="table-scroll">
                            <table class="table-x">
                                <thead>
                                    <tr>
                                        <th>Pelanggan</th>
                                        <th>Kasir</th>
                                        <th>Metode</th>
                                        <th class="t-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentOrders as $o)
                                        <tr>
                                            <td>
                                                <div class="cell-main">{{ $o->customer_name ?: 'Pelanggan umum' }}</div>
                                                <div class="cell-sub">#{{ $o->id }} · {{ $o->created_at?->diffForHumans() }} · {{ $o->total_item }} item</div>
                                            </td>
                                            <td>{{ $o->nama_kasir }}</td>
                                            <td>
                                                @php $m = strtolower($o->payment_method); @endphp
                                                <span class="tag {{ $m === 'cash' ? 'tag--sage' : ($m === 'transfer' ? 'tag--blue' : '') }} text-capitalize">{{ $o->payment_method }}</span>
                                            </td>
                                            <td class="t-right num font-weight-bold">Rp {{ $rp($o->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>

            <div class="col-lg-4">
                <section class="panel">
                    <div class="panel__head">
                        <div>
                            <h2 class="panel__title">Menu terlaris</h2>
                            <p class="panel__sub">30 hari terakhir, berdasarkan jumlah terjual</p>
                        </div>
                        <a href="{{ route('top.products') }}" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                    <div class="panel__body pt-2 pb-2">
                        @php $maxQty = max(1, (int) $topProducts->max('qty')); @endphp
                        @if ($topProducts->isEmpty())
                            <p class="text-muted my-3">Belum ada data penjualan 30 hari terakhir.</p>
                        @else
                            <ol class="rank-list">
                                @foreach ($topProducts as $i => $tp)
                                    <li>
                                        <span class="rank">{{ $i + 1 }}</span>
                                        <span class="name" title="{{ $tp->name }}">{{ $tp->name }}</span>
                                        <span class="qty num">{{ $rp($tp->qty) }}<span class="text-muted font-weight-normal"> porsi</span></span>
                                        <div class="meter {{ $i === 0 ? 'meter--red' : '' }}"><span style="width: {{ ($tp->qty / $maxQty) * 100 }}%"></span></div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <section class="panel">
                    <div class="panel__head">
                        <div>
                            <h2 class="panel__title">Pengeluaran bulan ini</h2>
                            <p class="panel__sub">Pos biaya terbesar dan catatan terakhir</p>
                        </div>
                        <a href="{{ route('expenses.create') }}" class="btn btn-ghost btn-sm"><i class="fas fa-plus"></i> Catat</a>
                    </div>
                    @if ($expenseByCategory->isEmpty() && $recentExpenses->isEmpty())
                        <div class="empty" style="padding:36px 24px">
                            <div class="empty__icon"><i class="fas fa-wallet"></i></div>
                            <h3>Belum ada pengeluaran</h3>
                            <p>Catat belanja dan biaya operasional agar laba bersih bisa dihitung.</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pengeluaran</a>
                        </div>
                    @else
                        <div class="panel__body">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    @php $maxExp = max(1, (float) $expenseByCategory->max('total')); @endphp
                                    @forelse ($expenseByCategory as $row)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between" style="font-size:13.5px; gap:10px">
                                                <span><i class="fas {{ \App\Models\Expense::categoryIcon($row->category) }} text-muted mr-1"></i> {{ \App\Models\Expense::categoryLabel($row->category) }}</span>
                                                <span class="num font-weight-bold">Rp {{ $rp($row->total) }}</span>
                                            </div>
                                            <div class="meter {{ $loop->first ? 'meter--red' : '' }} mt-1"><span style="width: {{ max(2, $row->total / $maxExp * 100) }}%"></span></div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Belum ada pengeluaran bulan ini.</p>
                                    @endforelse
                                </div>
                                <div class="col-md-6">
                                    <ul class="ledger">
                                        @foreach ($recentExpenses as $e)
                                            <li>
                                                <span>
                                                    <span class="d-block" style="color:var(--ink); font-weight:600">{{ \Illuminate\Support\Str::limit($e->description, 34) }}</span>
                                                    <span class="cell-sub">{{ $e->expense_date->translatedFormat('d M') }} · {{ \App\Models\Expense::categoryLabel($e->category) }}</span>
                                                </span>
                                                <span class="num">Rp {{ $rp($e->amount) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a href="{{ route('expenses.index') }}" class="btn btn-light btn-sm mt-2">Lihat semua pengeluaran</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <div class="col-lg-4">
                <div class="row">
                    <div class="col-6">
                        <div class="stat">
                            <div class="stat__label"><i class="fas fa-mug-hot"></i> Produk aktif</div>
                            <div class="stat__value">{{ $counts['products'] }}</div>
                            <div class="stat__meta">{{ $counts['categories'] }} kategori</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat">
                            <div class="stat__label"><i class="fas fa-wallet"></i> Keluar hari ini</div>
                            <div class="stat__value" style="font-size:22px"><small>Rp</small>{{ $rp($todayExpense) }}</div>
                            <div class="stat__meta"><a href="{{ route('expenses.create') }}">Catat pengeluaran</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@if ($isAdmin)
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
        <script>
            (function () {
                if (!window.Chart) return;
                Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
                Chart.defaults.color = '#7A706A';

                var net = @json($trend['net']);
                var orders = @json($trend['orders']);
                var colors = net.map(function (_, i) { return i === net.length - 1 ? '#D7141A' : '#3A2A22'; });

                new Chart(document.getElementById('trendChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($trend['labels']),
                        datasets: [{
                            label: 'Penjualan bersih',
                            data: net,
                            backgroundColor: colors,
                            hoverBackgroundColor: '#B8893B',
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 34
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#2B1D17', padding: 12, cornerRadius: 10, displayColors: false,
                                callbacks: {
                                    label: function (c) { return ARCH.rupiah(c.parsed.y); },
                                    afterLabel: function (c) { return orders[c.dataIndex] + ' transaksi'; }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 12 } },
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#ECEDE9' },
                                ticks: {
                                    callback: function (v) {
                                        if (v >= 1e6) return (v / 1e6).toLocaleString('id-ID') + ' jt';
                                        if (v >= 1e3) return (v / 1e3).toLocaleString('id-ID') + ' rb';
                                        return v;
                                    }
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endif
