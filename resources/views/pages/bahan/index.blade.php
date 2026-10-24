@extends('layouts.app')

@section('title', 'Daftar bahan')
@section('crumb', 'Persediaan')

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Daftar bahan</h1>
            <p class="page-head__lead">Master bahan baku beserta satuannya. Dipakai saat mencatat persediaan masuk.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('inventories.create') }}" class="btn btn-light"><i class="fas fa-arrow-right-to-bracket"></i> Catat persediaan masuk</a>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <form action="{{ route('bahans.store') }}" method="POST" class="panel" novalidate>
                @csrf
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Tambah bahan</h2>
                        <p class="panel__sub">Kombinasi nama dan satuan harus unik.</p>
                    </div>
                </div>
                <div class="panel__body">
                    <div class="form-group">
                        <label for="nama_bahan">Nama bahan</label>
                        <input id="nama_bahan" type="text" name="nama_bahan" value="{{ old('nama_bahan') }}"
                            class="form-control @error('nama_bahan') is-invalid @enderror" placeholder="Contoh: Biji kopi arabika" required>
                        @error('nama_bahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group mb-0">
                        <label for="satuan">Satuan</label>
                        <input id="satuan" type="text" name="satuan" value="{{ old('satuan') }}" list="satuan-list"
                            class="form-control @error('satuan') is-invalid @enderror" placeholder="Kg, Liter, Pcs…" required>
                        <datalist id="satuan-list">
                            <option value="Kg"><option value="Gram"><option value="Liter"><option value="Ml"><option value="Pcs"><option value="Pack"><option value="Botol"><option value="Dus">
                        </datalist>
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="panel__foot">
                    <button type="submit" class="btn btn-primary btn-block">Simpan bahan</button>
                </div>
            </form>
        </div>

        <div class="col-lg-8 order-lg-1">
            <section class="panel">
                <div class="panel__head">
                    <form method="GET" action="{{ route('bahans.index') }}" class="toolbar">
                        <div class="search">
                            <i class="fas fa-magnifying-glass"></i>
                            <input type="search" class="form-control" name="nama_bahan" value="{{ $search }}" placeholder="Cari bahan" aria-label="Cari bahan">
                        </div>
                        @if ($search)
                            <a href="{{ route('bahans.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                        @endif
                    </form>
                    <span class="count-chip">{{ $bahans->total() }} bahan</span>
                </div>

                @if ($bahans->isEmpty())
                    <div class="empty">
                        <div class="empty__icon"><i class="fas fa-seedling"></i></div>
                        <h3>{{ $search ? 'Bahan tidak ditemukan' : 'Belum ada bahan' }}</h3>
                        <p>{{ $search ? 'Coba kata kunci lain.' : 'Tambahkan bahan baku melalui formulir di samping.' }}</p>
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="table-x">
                            <thead>
                                <tr>
                                    <th>Bahan</th>
                                    <th>Satuan</th>
                                    <th class="t-right">Stok tersisa</th>
                                    <th class="t-actions"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bahans as $bahan)
                                    <tr>
                                        <td>
                                            <div class="cell-main">{{ $bahan->nama_bahan }}</div>
                                            <div class="cell-sub">{{ $bahan->inventories_count }} kali pembelian</div>
                                        </td>
                                        <td><span class="tag tag--plain">{{ $bahan->satuan }}</span></td>
                                        <td class="t-right num">
                                            @php $stok = (float) ($bahan->stok ?? 0); @endphp
                                            <span class="{{ $bahan->inventories_count > 0 && $stok <= 0 ? 'text-red font-weight-bold' : '' }}">{{ number_format($stok, 0, ',', '.') }}</span>
                                            <span class="text-muted">{{ $bahan->satuan }}</span>
                                        </td>
                                        <td class="t-actions">
                                            <a href="{{ route('bahans.edit', $bahan->id) }}" class="btn btn-light btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                                            <form action="{{ route('bahans.destroy', $bahan->id) }}" method="POST" class="d-inline"
                                                data-confirm="Bahan {{ $bahan->nama_bahan }} akan dihapus. Riwayat persediaan tetap tersimpan dengan nama bahan lama."
                                                data-confirm-title="Hapus bahan?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost-danger btn-sm" aria-label="Hapus {{ $bahan->nama_bahan }}"><i class="fas fa-trash-can"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $bahans->links() }}
                @endif
            </section>
        </div>
    </div>
@endsection
