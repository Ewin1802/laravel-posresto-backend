@extends('layouts.app')

@section('title', 'Produk terlaris')
@section('crumb', 'Laporan')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rangeLabel = \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') . ' – ' . \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y');
    $offset = ($topProducts->currentPage() - 1) * $topProducts->perPage();
    $totalQty = max(1, (int) $totals->qty);
@endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Produk terlaris</h1>
            <p class="page-head__lead">{{ $rangeLabel }}. Diurutkan berdasarkan jumlah porsi terjual.</p>
        </div>
    </div>

    @include('layouts.alert')

    @include('pages.partials.range-filter', [
        'action' => route('top.products'),
        'start' => $start_date,
        'end' => $end_date,
    ])

    <div class="row">
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-utensils"></i> Porsi terjual</div>
                <div class="stat__value">{{ $rp($totals->qty) }}</div>
                <div class="stat__meta">Dari {{ $rp($totals->products) }} produk berbeda</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-coins"></i> Nilai penjualan item</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($totals->revenue) }}</div>
                <div class="stat__meta">Qty × harga, sebelum diskon & pajak</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-crown"></i> Nomor satu</div>
                <div class="stat__value" style="font-size:22px">{{ $chartData['labels'][0] ?? '—' }}</div>
                <div class="stat__meta">{{ isset($chartData['data'][0]) ? $rp($chartData['data'][0]) . ' porsi' : 'Belum ada penjualan' }}</div>
            </div>
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <div>
                <h2 class="panel__title">Sepuluh teratas</h2>
                <p class="panel__sub">Jumlah porsi terjual per produk</p>
            </div>
        </div>
        <div class="panel__body">
            @if (count($chartData['data']))
                <div class="chart-box" style="height: {{ max(220, count($chartData['data']) * 36) }}px"><canvas id="productChart" aria-label="Grafik produk terlaris"></canvas></div>
            @else
                <div class="empty" style="padding:40px 24px">
                    <div class="empty__icon"><i class="fas fa-ranking-star"></i></div>
                    <h3>Belum ada penjualan</h3>
                    <p>Tidak ada produk terjual pada rentang tanggal ini.</p>
                </div>
            @endif
        </div>
    </section>

    @if ($topProducts->total() > 0)
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Semua produk terjual</h2>
                    <p class="panel__sub">Kontribusi dihitung dari total porsi terjual</p>
                </div>
            </div>
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th class="t-idx">Peringkat</th>
                            <th>Produk</th>
                            <th class="t-right">Porsi</th>
                            <th class="t-right">Transaksi</th>
                            <th class="t-right">Nilai</th>
                            <th style="min-width:160px">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $i => $item)
                            @php $share = ($item->total_quantity / $totalQty) * 100; $rank = $offset + $i + 1; @endphp
                            <tr>
                                <td class="t-idx">
                                    <span class="{{ $rank === 1 ? 'text-red' : '' }}" style="font-family: var(--font-display); font-weight:600; font-size:17px">{{ $rank }}</span>
                                </td>
                                <td>
                                    <div class="cell-media">
                                        @if ($item->image)
                                            <img src="{{ asset($item->image) }}" alt="" class="thumb" loading="lazy">
                                        @else
                                            <span class="thumb"><i class="fas fa-mug-hot"></i></span>
                                        @endif
                                        <div>
                                            <div class="cell-main">{{ $item->name }}</div>
                                            <div class="cell-sub">{{ $item->category_name ?? 'Produk sudah dihapus' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="t-right num font-weight-bold">{{ $rp($item->total_quantity) }}</td>
                                <td class="t-right num">{{ $rp($item->order_count) }}</td>
                                <td class="t-right num">Rp {{ $rp($item->revenue) }}</td>
                                <td>
                                    <div class="d-flex align-items-center" style="gap:10px">
                                        <div class="meter {{ $rank === 1 ? 'meter--red' : '' }}" style="flex:1"><span style="width: {{ max(1, $share) }}%"></span></div>
                                        <span class="num text-muted" style="font-size:12.5px; width:44px; text-align:right">{{ number_format($share, 1, ',', '.') }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $topProducts->links() }}
        </section>
    @endif
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        (function () {
            var canvas = document.getElementById('productChart');
            if (!canvas || !window.Chart) return;
            Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
            Chart.defaults.color = '#7A706A';
            var data = @json($chartData['data']);
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Porsi terjual',
                        data: data,
                        backgroundColor: data.map(function (_, i) { return i === 0 ? '#D7141A' : '#3A2A22'; }),
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 18
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#2B1D17', padding: 12, cornerRadius: 10, displayColors: false,
                            callbacks: { label: function (c) { return ARCH.number(c.parsed.x) + ' porsi'; } } }
                    },
                    scales: {
                        x: { beginAtZero: true, border: { display: false }, grid: { color: '#ECEDE9' }, ticks: { precision: 0 } },
                        y: { grid: { display: false }, ticks: { color: '#221A16', font: { weight: 600 } } }
                    }
                }
            });
        })();
    </script>
@endpush
