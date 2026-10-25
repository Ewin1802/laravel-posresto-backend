@extends('layouts.app')

@section('title', 'Analisa keuangan')
@section('crumb', 'Laporan')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rangeLabel = $startDate->translatedFormat('d M Y') . ' – ' . $endDate->translatedFormat('d M Y');
    $noData = $totalPengeluaran == 0 && $totalKeuangan == 0;
    if ($noData) {
        $verdict = ['tone' => 'amber', 'title' => 'Belum ada data pada periode ini', 'note' => 'Data tidak tersedia, mungkin kamu masih manganto sehingga kamu malas ba jual ditanggal ini!'];
    } elseif ($selisih > 0) {
        $verdict = ['tone' => 'sage', 'title' => 'Penjualan melampaui pengeluaran', 'note' => 'Fulus maso bagus, kamu layak buka cabang di Turki!'];
    } elseif ($selisih < 0) {
        $verdict = ['tone' => 'red', 'title' => 'Pengeluaran melebihi penjualan', 'note' => 'Pengeluaranmu terlalu banyak, perlu improvisasi menyesuaikan kebutuhan pasar dan pelanggan.'];
    } else {
        $verdict = ['tone' => 'amber', 'title' => 'Penjualan sama dengan pengeluaran', 'note' => 'Usahamu jalan di tempat, hidop mar mati!'];
    }
    $deltaProfit = $selisih - $prevProfit;
    $maxCat = max(1, (float) $expenseByCategory->max('total'));
    $expenseRatio = $totalKeuangan > 0 ? $totalPengeluaran / $totalKeuangan * 100 : null;
@endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Analisa keuangan</h1>
            <p class="page-head__lead">Penjualan bersih dibandingkan pengeluaran operasional, {{ $rangeLabel }}.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('expenses.index', ['start_date' => $start_date, 'end_date' => $end_date]) }}" class="btn btn-light"><i class="fas fa-wallet"></i> Rincian pengeluaran</a>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pengeluaran</a>
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
                <div class="receipt__label">Laba bersih (penjualan − pengeluaran)</div>
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
            <div><span>Pengeluaran</span><strong>Rp {{ $rp($totalPengeluaran) }}</strong></div>
            <div>
                <span>Dibanding periode sebelumnya</span>
                <strong class="{{ $deltaProfit >= 0 ? 'delta--up' : 'delta--down' }}">{{ $deltaProfit >= 0 ? '+' : '−' }}Rp {{ $rp(abs($deltaProfit)) }}</strong>
            </div>
            <div><span>Transaksi</span><strong>{{ $rp($orderAgg->orders) }}</strong></div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Penjualan, pengeluaran, dan laba berjalan</h2>
                        <p class="panel__sub">{{ $monthly ? 'Per bulan' : 'Per hari' }}. Garis emas adalah akumulasi laba sejak awal periode.</p>
                    </div>
                    <div class="legend-inline">
                        <span><i style="background:#3A2A22"></i> Penjualan</span>
                        <span><i style="background:#D7141A"></i> Pengeluaran</span>
                        <span><i style="background:#B8893B; height:3px"></i> Akumulasi laba</span>
                    </div>
                </div>
                <div class="panel__body">
                    @if ($noData)
                        <div class="empty" style="padding:70px 24px">
                            <div class="empty__icon"><i class="fas fa-chart-line"></i></div>
                            <h3>Belum ada data</h3>
                            <p>Tidak ada penjualan maupun pengeluaran pada rentang ini.</p>
                            <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pengeluaran</a>
                        </div>
                    @else
                        <div class="chart-box" style="height:340px"><canvas id="flowChart" aria-label="Grafik penjualan, pengeluaran, dan akumulasi laba"></canvas></div>
                    @endif
                </div>
            </section>

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Pengeluaran per kategori</h2>
                        <p class="panel__sub">
                            @if (!is_null($expenseRatio))
                                Pengeluaran setara {{ number_format($expenseRatio, 1, ',', '.') }}% dari penjualan bersih
                            @else
                                Porsi tiap pos biaya pada periode ini
                            @endif
                        </p>
                    </div>
                </div>
                @if ($expenseByCategory->isEmpty())
                    <div class="empty" style="padding:36px 24px">
                        <div class="empty__icon"><i class="fas fa-wallet"></i></div>
                        <h3>Belum ada pengeluaran</h3>
                        <p>Catat pengeluaran agar laba bersih bisa dihitung dengan tepat.</p>
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="table-x">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="t-right">Catatan</th>
                                    <th class="t-right">Jumlah</th>
                                    <th style="min-width:180px">Porsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenseByCategory as $row)
                                    @php $share = $totalPengeluaran > 0 ? $row->total / $totalPengeluaran * 100 : 0; @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('expenses.index', ['start_date' => $start_date, 'end_date' => $end_date, 'category' => $row->category]) }}" class="cell-media" style="color:inherit">
                                                <span class="thumb" style="width:34px;height:34px;flex-basis:34px"><i class="fas {{ \App\Models\Expense::categoryIcon($row->category) }}"></i></span>
                                                <span class="cell-main">{{ \App\Models\Expense::categoryLabel($row->category) }}</span>
                                            </a>
                                        </td>
                                        <td class="t-right num">{{ $row->items }}</td>
                                        <td class="t-right num font-weight-bold">Rp {{ $rp($row->total) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center" style="gap:10px">
                                                <div class="meter {{ $loop->first ? 'meter--red' : '' }}" style="flex:1"><span style="width: {{ max(2, $row->total / $maxCat * 100) }}%"></span></div>
                                                <span class="num text-muted" style="font-size:12.5px; width:44px; text-align:right">{{ number_format($share, 1, ',', '.') }}%</span>
                                            </div>
                                        </td>
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
                        <h2 class="panel__title">Ringkasan</h2>
                        <p class="panel__sub">Akumulasi akhir periode</p>
                    </div>
                </div>
                <div class="panel__body pt-2">
                    <ul class="ledger">
                        <li><span>Subtotal penjualan</span><span class="num">Rp {{ $rp($orderAgg->sub_total) }}</span></li>
                        <li><span>Diskon</span><span class="num text-red">− Rp {{ $rp($orderAgg->discount) }}</span></li>
                        <li><span>Pajak (PB1)</span><span class="num text-red">− Rp {{ $rp($orderAgg->tax) }}</span></li>
                        <li><span>Service charge</span><span class="num">+ Rp {{ $rp($orderAgg->service) }}</span></li>
                        <li><span class="font-weight-bold" style="color:var(--ink)">Penjualan bersih</span><span class="num font-weight-bold">Rp {{ $rp($totalKeuangan) }}</span></li>
                        <li><span>Pengeluaran</span><span class="num text-red">− Rp {{ $rp($totalPengeluaran) }}</span></li>
                        <li class="is-total">
                            <span>Laba bersih</span>
                            <span class="num {{ $selisih < 0 ? 'text-red' : 'text-sage' }}">{{ $selisih < 0 ? '−' : '' }}Rp {{ $rp(abs($selisih)) }}</span>
                        </li>
                    </ul>
                    <div class="notice mt-3" style="{{ $verdict['tone'] === 'sage' ? 'background:var(--sage-050);border-color:#C9E0D1;color:#2B5E41' : ($verdict['tone'] === 'red' ? 'background:var(--red-050);border-color:#F5C9CA;color:var(--red-600)' : '') }}">
                        <i class="fas fa-quote-left" style="color:inherit"></i>
                        <div><em>{{ $verdict['note'] }}</em></div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Periode sebelumnya</h2>
                        <p class="panel__sub">{{ $prevStart->translatedFormat('d M') }} – {{ $prevEnd->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
                <div class="panel__body pt-2">
                    <ul class="ledger">
                        <li><span>Penjualan bersih</span><span class="num">Rp {{ $rp($prevSales) }}</span></li>
                        <li><span>Pengeluaran</span><span class="num">Rp {{ $rp($prevExpense) }}</span></li>
                        <li><span class="font-weight-bold" style="color:var(--ink)">Laba bersih</span><span class="num font-weight-bold {{ $prevProfit < 0 ? 'text-red' : '' }}">{{ $prevProfit < 0 ? '−' : '' }}Rp {{ $rp(abs($prevProfit)) }}</span></li>
                    </ul>
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
                var short = function (v) {
                    var a = Math.abs(v), s = v < 0 ? '−' : '';
                    if (a >= 1e6) return s + (a / 1e6).toLocaleString('id-ID') + ' jt';
                    if (a >= 1e3) return s + (a / 1e3).toLocaleString('id-ID') + ' rb';
                    return s + a;
                };
                new Chart(canvas, {
                    data: {
                        labels: @json($series['labels']),
                        datasets: [
                            { type: 'bar', label: 'Penjualan', data: @json($series['sales']), backgroundColor: '#3A2A22', borderRadius: 4, borderSkipped: false, maxBarThickness: 18, order: 2 },
                            { type: 'bar', label: 'Pengeluaran', data: @json($series['spend']), backgroundColor: '#D7141A', borderRadius: 4, borderSkipped: false, maxBarThickness: 18, order: 2 },
                            { type: 'line', label: 'Akumulasi laba', data: @json($series['cumulative']), borderColor: '#B8893B', backgroundColor: '#B8893B',
                              borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 4, tension: .3, yAxisID: 'y2', order: 1 }
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
                            y: { beginAtZero: true, border: { display: false }, grid: { color: '#ECEDE9' }, ticks: { callback: short } },
                            y2: { position: 'right', border: { display: false }, grid: { display: false }, ticks: { callback: short, color: '#8A6421' } }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endif
