@extends('layouts.app')

@section('title', 'Laporan persediaan')
@section('crumb', 'Laporan')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rangeLabel = \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') . ' – ' . \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y');
@endphp

@push('style')
    <style>
        .stock-table td { vertical-align: middle; }
        .stock-table tbody td:first-child { padding-left: 16px; }
        .stock-table tbody td.batch-cell:first-child { padding-left: 22px; }
        .stock-table .batch-cell { background: var(--surface-2); }
        .stock-table tr.batch-start td { border-top: 2px solid var(--line-strong); }
        .stock-table tbody tr:first-child td { border-top: 0; }
        .stock-table th.group { text-align: center; color: var(--ink-2); border-bottom: 1px dashed var(--line-strong); }
    </style>
@endpush

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Laporan persediaan</h1>
            <p class="page-head__lead">Batch yang masuk {{ $rangeLabel }}, beserta pengeluaran dan sisa stoknya.</p>
        </div>
    </div>

    @include('layouts.alert')

    @include('pages.partials.range-filter', [
        'action' => route('inventory.reports'),
        'start' => $start_date,
        'end' => $end_date,
    ])

    <div class="row">
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-arrow-right-to-bracket"></i> Nilai masuk</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($totals->masuk) }}</div>
                <div class="stat__meta">{{ $rp($totals->batches) }} batch pembelian</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-arrow-right-from-bracket"></i> Nilai keluar</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($totals->keluar) }}</div>
                <div class="stat__meta">Dari batch pada periode ini</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-box"></i> Nilai sisa saat ini</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($totals->sisa) }}</div>
                <div class="stat__meta">Sisa stok × harga satuan</div>
            </div>
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <div>
                <h2 class="panel__title">Kartu stok per batch</h2>
                <p class="panel__sub">Sisa dihitung berurutan dari jumlah masuk dikurangi setiap pengeluaran</p>
            </div>
        </div>

        @if ($inventoryReport->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-boxes-stacked"></i></div>
                <h3>Tidak ada persediaan masuk</h3>
                <p>Tidak ada pembelian bahan pada rentang tanggal ini.</p>
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x stock-table">
                    <thead>
                        <tr>
                            <th rowspan="2">Bahan</th>
                            <th colspan="3" class="group">Masuk</th>
                            <th colspan="4" class="group">Keluar</th>
                            <th rowspan="2" class="t-right">Sisa</th>
                        </tr>
                        <tr>
                            <th class="t-right">Jumlah</th>
                            <th class="t-right">Harga</th>
                            <th class="t-right">Nilai</th>
                            <th>Tanggal</th>
                            <th class="t-right">Jumlah</th>
                            <th class="t-right">Nilai</th>
                            <th>Penerima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventoryReport as $item)
                            @php
                                $sisa = $item->saldo_awal;
                                $outs = $item->inventoryOut;
                                $rows = max(1, $outs->count());
                                $nama = $item->bahan->nama_bahan ?? $item->nama_bahan;
                            @endphp

                            @for ($r = 0; $r < $rows; $r++)
                                @php
                                    $out = $outs[$r] ?? null;
                                    if ($out) { $sisa -= $out->quantity_out; }
                                @endphp
                                <tr class="{{ $r === 0 ? 'batch-start' : '' }}">
                                    @if ($r === 0)
                                        <td rowspan="{{ $rows }}" class="batch-cell">
                                            <div class="cell-main">{{ $nama }}</div>
                                            <div class="cell-sub">{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->translatedFormat('d M Y, H:i') : '-' }}</div>
                                            @if ($item->supplier)
                                                <div class="cell-sub">{{ $item->supplier }}</div>
                                            @endif
                                        </td>
                                        <td rowspan="{{ $rows }}" class="t-right num batch-cell">{{ $item->saldo_awal }} <span class="text-muted">{{ $item->satuan }}</span></td>
                                        <td rowspan="{{ $rows }}" class="t-right num batch-cell">{{ $rp($item->harga_satuan) }}</td>
                                        <td rowspan="{{ $rows }}" class="t-right num batch-cell font-weight-bold">{{ $rp($item->saldo_awal * $item->harga_satuan) }}</td>
                                    @endif

                                    @if ($out)
                                        <td class="num">{{ $out->created_at->translatedFormat('d M, H:i') }}</td>
                                        <td class="t-right num">{{ $out->quantity_out }}</td>
                                        <td class="t-right num">{{ $rp($out->quantity_out * $item->harga_satuan) }}</td>
                                        <td>{{ $out->receiver ?: '—' }}</td>
                                    @else
                                        <td colspan="4" class="text-muted">Belum ada pengeluaran pada periode ini</td>
                                    @endif

                                    <td class="t-right num font-weight-bold {{ $sisa <= 0 ? 'text-red' : '' }}">{{ $sisa }}</td>
                                </tr>
                            @endfor
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $inventoryReport->links() }}
        @endif
    </section>
@endsection
