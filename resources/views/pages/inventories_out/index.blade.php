@extends('layouts.app')

@section('title', 'Persediaan keluar')
@section('crumb', 'Persediaan')

@php $rp = fn ($v) => number_format((float) $v, 0, ',', '.'); @endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Persediaan keluar</h1>
            <p class="page-head__lead">
                Bulan ini {{ $monthValue->c ?? 0 }} pengeluaran bahan senilai Rp {{ $rp($monthValue->v ?? 0) }}.
            </p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('inventory_out.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat bahan keluar</a>
        </div>
    </div>

    @include('layouts.alert')

    <section class="panel">
        <div class="panel__head">
            <form method="GET" action="{{ route('inventory_out.index') }}" class="toolbar">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="search" value="{{ $search }}" placeholder="Cari bahan atau penerima" aria-label="Cari persediaan keluar">
                </div>
                @if ($search)
                    <a href="{{ route('inventory_out.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
            <span class="count-chip">{{ $inventory_outs->total() }} transaksi</span>
        </div>

        @if ($inventory_outs->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-arrow-right-from-bracket"></i></div>
                <h3>{{ $search ? 'Tidak ada yang cocok' : 'Belum ada bahan keluar' }}</h3>
                <p>{{ $search ? 'Coba kata kunci lain.' : 'Catat setiap bahan yang dipakai dapur atau bar agar stok tetap akurat.' }}</p>
                @unless ($search)
                    <a href="{{ route('inventory_out.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat bahan keluar</a>
                @endunless
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bahan</th>
                            <th class="t-right">Jumlah</th>
                            <th class="t-right">Harga / satuan</th>
                            <th class="t-right">Nilai</th>
                            <th>Penerima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventory_outs as $out)
                            @php $inv = $out->inventory; @endphp
                            <tr>
                                <td>
                                    <div>{{ $out->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="cell-sub">{{ $out->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="cell-main">{{ $inv->nama_bahan ?? 'Batch dihapus' }}</div>
                                    @if ($out->description)
                                        <div class="cell-sub text-truncate" style="max-width:280px">{{ $out->description }}</div>
                                    @endif
                                </td>
                                <td class="t-right num font-weight-bold">{{ $out->quantity_out }} <span class="text-muted font-weight-normal">{{ $inv->satuan ?? '' }}</span></td>
                                <td class="t-right num">Rp {{ $rp($inv->amount ?? 0) }}</td>
                                <td class="t-right num font-weight-bold">Rp {{ $rp($out->quantity_out * ($inv->amount ?? 0)) }}</td>
                                <td>{{ $out->receiver }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $inventory_outs->links() }}
        @endif
    </section>
@endsection
