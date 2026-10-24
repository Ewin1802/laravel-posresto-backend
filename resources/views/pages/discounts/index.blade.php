@extends('layouts.app')

@section('title', 'Diskon')
@section('crumb', 'Data umum')

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Diskon</h1>
            <p class="page-head__lead">Potongan harga dalam persen yang bisa dipilih kasir saat transaksi.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('discounts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah diskon</a>
        </div>
    </div>

    @include('layouts.alert')

    <section class="panel">
        <div class="panel__head">
            <form method="GET" action="{{ route('discounts.index') }}" class="toolbar">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="name" value="{{ $search }}" placeholder="Cari diskon" aria-label="Cari diskon">
                </div>
                @if ($search)
                    <a href="{{ route('discounts.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
            <span class="count-chip">{{ $discounts->total() }} diskon</span>
        </div>

        @if ($discounts->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-percent"></i></div>
                <h3>{{ $search ? 'Diskon tidak ditemukan' : 'Belum ada diskon' }}</h3>
                <p>{{ $search ? 'Coba kata kunci lain.' : 'Buat diskon seperti Member 10% atau Promo pembukaan.' }}</p>
                @unless ($search)
                    <a href="{{ route('discounts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah diskon</a>
                @endunless
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th style="width:110px">Potongan</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="t-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($discounts as $discount)
                            <tr>
                                <td>
                                    <span class="stat__value" style="margin:0; font-size:24px">{{ str_replace('.', ',', (string) (float) $discount->value) }}<small style="margin-left:2px">%</small></span>
                                </td>
                                <td>
                                    <div class="cell-main">{{ $discount->name }}</div>
                                    <div class="cell-sub">{{ $discount->description }}</div>
                                </td>
                                <td>
                                    @if ($discount->status === 'inactive')
                                        <span class="tag">Nonaktif</span>
                                    @elseif ($discount->expired_date && \Carbon\Carbon::parse($discount->expired_date)->isPast())
                                        <span class="tag tag--amber">Kedaluwarsa</span>
                                    @else
                                        <span class="tag tag--sage">Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $discount->created_at?->translatedFormat('d M Y') }}</td>
                                <td class="t-actions">
                                    <a href="{{ route('discounts.edit', $discount->id) }}" class="btn btn-light btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                                    <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="d-inline"
                                        data-confirm="Diskon {{ $discount->name }} tidak akan bisa dipilih lagi di aplikasi kasir."
                                        data-confirm-title="Hapus diskon?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost-danger btn-sm" aria-label="Hapus {{ $discount->name }}"><i class="fas fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $discounts->links() }}
        @endif
    </section>
@endsection
