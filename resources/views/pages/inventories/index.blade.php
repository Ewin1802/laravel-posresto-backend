@extends('layouts.app')

@section('title', 'Persediaan masuk')
@section('crumb', 'Persediaan')

@php $rp = fn ($v) => number_format((float) $v, 0, ',', '.'); @endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Persediaan masuk</h1>
            <p class="page-head__lead">Setiap pembelian bahan dicatat sebagai satu batch, lengkap dengan harga dan sisa stoknya.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('inventory_out.create') }}" class="btn btn-light"><i class="fas fa-arrow-right-from-bracket"></i> Catat keluar</a>
            <a href="{{ route('inventories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pembelian</a>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-coins"></i> Nilai stok tersisa</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($summary->stock_value) }}</div>
                <div class="stat__meta">Sisa stok × harga per satuan</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-layer-group"></i> Total batch</div>
                <div class="stat__value">{{ $rp($summary->batches) }}</div>
                <div class="stat__meta">Seluruh catatan pembelian</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-box-open"></i> Batch habis</div>
                <div class="stat__value {{ $summary->empty_count > 0 ? 'text-red' : '' }}">{{ $rp($summary->empty_count) }}</div>
                <div class="stat__meta"><a href="{{ route('inventories.index', ['stock' => 'empty']) }}">Lihat daftar</a></div>
            </div>
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <form method="GET" action="{{ route('inventories.index') }}" class="toolbar">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="search" value="{{ $search }}" placeholder="Cari bahan, supplier, penerima" aria-label="Cari persediaan">
                </div>
                @if ($stock)<input type="hidden" name="stock" value="{{ $stock }}">@endif
            </form>
            <div class="chips">
                @foreach (['' => 'Semua', 'available' => 'Tersedia', 'low' => 'Menipis', 'empty' => 'Habis'] as $key => $label)
                    <a href="{{ route('inventories.index', array_filter(['search' => $search, 'stock' => $key])) }}" class="chip {{ (string) $stock === (string) $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @if ($inventories->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-boxes-stacked"></i></div>
                <h3>Tidak ada catatan persediaan</h3>
                <p>{{ $search || $stock ? 'Tidak ada batch yang cocok dengan filter ini.' : 'Catat pembelian bahan pertama untuk mulai melacak stok.' }}</p>
                <a href="{{ route('inventories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pembelian</a>
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th style="min-width:170px">Sisa stok</th>
                            <th class="t-right">Harga / satuan</th>
                            <th class="t-right">Nilai beli</th>
                            <th>Supplier</th>
                            <th>Bukti</th>
                            <th class="t-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            @php
                                $ratio = $inventory->saldo_awal > 0 ? max(0, $inventory->quantity) / $inventory->saldo_awal : 0;
                                $tone = $inventory->quantity <= 0 ? 'red' : ($ratio <= .2 ? 'gold' : '');
                            @endphp
                            <tr>
                                <td>
                                    <div class="cell-main">{{ $inventory->nama_bahan }}</div>
                                    <div class="cell-sub">{{ $inventory->tanggal_masuk ? \Carbon\Carbon::parse($inventory->tanggal_masuk)->translatedFormat('d M Y, H:i') : '-' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between num" style="font-size:13px">
                                        <span class="{{ $inventory->quantity <= 0 ? 'text-red font-weight-bold' : 'font-weight-bold' }}">{{ $inventory->quantity }} {{ $inventory->satuan }}</span>
                                        <span class="text-muted">dari {{ $inventory->saldo_awal }}</span>
                                    </div>
                                    <div class="meter {{ $tone ? 'meter--' . $tone : '' }} mt-1"><span style="width: {{ max(2, $ratio * 100) }}%"></span></div>
                                </td>
                                <td class="t-right num">Rp {{ $rp($inventory->amount) }}</td>
                                <td class="t-right num font-weight-bold">Rp {{ $rp($inventory->amount * $inventory->saldo_awal) }}</td>
                                <td>
                                    <div>{{ $inventory->supplier ?: '—' }}</div>
                                    @if ($inventory->receiver)
                                        <div class="cell-sub">Diterima {{ $inventory->receiver }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($inventory->image)
                                        <a href="{{ asset('storage/' . $inventory->image) }}" target="_blank" rel="noopener" title="Buka bukti transaksi">
                                            <img src="{{ asset('storage/' . $inventory->image) }}" alt="Bukti transaksi {{ $inventory->nama_bahan }}" class="thumb" loading="lazy">
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="t-actions">
                                    <a href="{{ route('inventories.edit', $inventory->id) }}" class="btn btn-light btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $inventories->links() }}
        @endif
    </section>
@endsection
