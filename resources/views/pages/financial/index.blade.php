@extends('layouts.app')

@section('title', 'Analisa keuangan')
@section('crumb', 'Laporan')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rangeLabel = $startDate->translatedFormat('d M Y') . ' – ' . $endDate->translatedFormat('d M Y');
    $noData = $totalPersediaan == 0 && $totalKeuangan == 0;
    if ($noData) {
        $verdict = ['tone' => 'amber', 'title' => 'Belum ada data pada periode ini', 'note' => 'Data tidak tersedia, mungkin kamu masih manganto sehingga kamu malas ba jual ditanggal ini!'];
    } elseif ($selisih > 0) {
        $verdict = ['tone' => 'sage', 'title' => 'Penjualan melampaui belanja', 'note' => 'Fulus maso bagus, kamu layak buka cabang di Turki!'];
    } elseif ($selisih < 0) {
        $verdict = ['tone' => 'red', 'title' => 'Belanja melebihi penjualan', 'note' => 'Belanjamu terlalu banyak, perlu improvisasi menyesuaikan kebutuhan pasar dan pelanggan.'];
    } else {
        $verdict = ['tone' => 'amber', 'title' => 'Penjualan sama dengan belanja', 'note' => 'Usahamu jalan di tempat, hidop mar mati!'];
    }
@endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Analisa keuangan</h1>
            <p class="page-head__lead">Perbandingan penjualan bersih dengan belanja persediaan, {{ $rangeLabel }}.</p>
        </div>
    </div>

    @include('layouts.alert')

    @include('pages.partials.range-filter', [
        'action' => route('financial.report'),
        'start' => $start_date,
        'end' => $end_date,
    ])

    <section class="receipt" aria-label="Hasil periode">
        <div class="receipt__top">
            <div>
                <div class="receipt__label">Pendapatan (penjualan − belanja)</div>
                <div class="receipt__amount">
                    <small>Rp</small>{{ $selisih < 0 ? '−' : '' }}{{ $rp(abs($selisih)) }}
                </div>
                <div class="receipt__label">
                    <span class="delta {{ $verdict['tone'] === 'sage' ? 'delta--up' : ($verdict['tone'] === 'red' ? 'delta--down' : '') }}">{{ $verdict['title'] }}</span>
                    @if (!is_null($margin))
                        · margin {{ number_format($margin, 1, ',', '.') }}%
                    @endif
                </div>
            </div>
            <div class="receipt__date">{{ $rangeLabel }}</div>
        </div>
        <hr class="receipt__rule">
        <div class="receipt__grid">
            <div><span>Penjualan bersih</span><strong>Rp {{ $rp($totalKeuangan) }}</strong></div>
            <div><span>Belanja persediaan</span><strong>Rp {{ $rp($totalPersediaan) }}</strong></div>
            <div><span>Bahan terpakai</span><strong>Rp {{ $rp($bahanTerpakai) }}</strong></div>
            <div><span>Transaksi</span><strong>{{ $rp($orderAgg->orders) }}</strong></div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Arus penjualan dan belanja</h2>
                        <p class="panel__sub">{{ $startDate->diffInDays($endDate) + 1 > 62 ? 'Per bulan' : 'Per hari' }} dalam rentang terpilih</p>
                    </div>
                    <div class="legend-inline">
                        <span><i style="background:#3A2A22"></i> Penjualan</span>
                        <span><i style="background:#D7141A"></i> Belanja</span>
                    </div>
                </div>
                <div class="panel__body">
                    @if ($noData)
                        <div class="empty" style="padding:70px 24px">
                            <div class="empty__icon"><i class="fas fa-chart-line"></i></div>
                            <h3>Belum ada data</h3>
                            <p>Tidak ada penjualan maupun belanja pada rentang ini.</p>
                        </div>
                    @else
                        <div class="chart-box"><canvas id="flowChart" aria-label="Grafik penjualan dan belanja"></canvas></div>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Ringkasan</h2>
                        <p class="panel__sub">Rumus sama seperti laporan penjualan</p>
                    </div>
                </div>
                <div class="panel__body pt-2">
                    <ul class="ledger">
                        <li><span>Subtotal penjualan</span><span class="num">Rp {{ $rp($orderAgg->sub_total) }}</span></li>
                        <li><span>Diskon</span><span class="num text-red">− Rp {{ $rp($orderAgg->discount) }}</span></li>
                        <li><span>Pajak (PB1)</span><span class="num text-red">− Rp {{ $rp($orderAgg->tax) }}</span></li>
                        <li><span>Service charge</span><span class="num">+ Rp {{ $rp($orderAgg->service) }}</span></li>
                        <li><span class="font-weight-bold" style="color:var(--ink)">Penjualan bersih</span><span class="num font-weight-bold">Rp {{ $rp($totalKeuangan) }}</span></li>
                        <li><span>Belanja persediaan</span><span class="num text-red">− Rp {{ $rp($totalPersediaan) }}</span></li>
                        <li class="is-total">
                            <span>Pendapatan</span>
                            <span class="num {{ $selisih < 0 ? 'text-red' : 'text-sage' }}">{{ $selisih < 0 ? '−' : '' }}Rp {{ $rp(abs($selisih)) }}</span>
                        </li>
                    </ul>
                    <div class="notice mt-3" style="{{ $verdict['tone'] === 'sage' ? 'background:var(--sage-050);border-color:#C9E0D1;color:#2B5E41' : ($verdict['tone'] === 'red' ? 'background:var(--red-050);border-color:#F5C9CA;color:var(--red-600)' : '') }}">
                        <i class="fas fa-quote-left" style="color:inherit"></i>
                        <div><em>{{ $verdict['note'] }}</em></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@if (!$noData)
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
        <script>
            (function () {
                var canvas = document.getElementById('flowChart');
                if (!canvas || !window.Chart) return;
                Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
                Chart.defaults.color = '#7A706A';
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: @json($series['labels']),
                        datasets: [
                            { label: 'Penjualan', data: @json($series['sales']), backgroundColor: '#3A2A22', borderRadius: 4, borderSkipped: false, maxBarThickness: 18 },
                            { label: 'Belanja', data: @json($series['spend']), backgroundColor: '#D7141A', borderRadius: 4, borderSkipped: false, maxBarThickness: 18 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: '#2B1D17', padding: 12, cornerRadius: 10,
                                callbacks: { label: function (c) { return c.dataset.label + ': ' + ARCH.rupiah(c.parsed.y); } } }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 10 } },
                            y: { beginAtZero: true, border: { display: false }, grid: { color: '#ECEDE9' },
                                ticks: { callback: function (v) { return v >= 1e6 ? (v / 1e6).toLocaleString('id-ID') + ' jt' : (v >= 1e3 ? (v / 1e3).toLocaleString('id-ID') + ' rb' : v); } } }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endif
