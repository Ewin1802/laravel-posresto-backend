@extends('layouts.app')

@section('title', 'Produk')
@section('crumb', 'Data umum')

@php $rp = fn ($v) => number_format((float) $v, 0, ',', '.'); @endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Produk</h1>
            <p class="page-head__lead">{{ $totals['active'] }} dari {{ $totals['all'] }} menu sedang aktif di aplikasi kasir.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah produk</a>
        </div>
    </div>

    @include('layouts.alert')

    <section class="panel">
        <div class="panel__head">
            <form method="GET" action="{{ route('products.index') }}" class="toolbar">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="name" value="{{ $search }}" placeholder="Cari nama produk" aria-label="Cari produk">
                </div>
                <select name="category" class="custom-select" style="width:auto" onchange="this.form.submit()" aria-label="Filter kategori">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected((string) $categoryId === (string) $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="custom-select" style="width:auto" onchange="this.form.submit()" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="1" @selected($status === '1')>Aktif</option>
                    <option value="0" @selected($status === '0')>Nonaktif</option>
                </select>
                @if ($search || $categoryId || $status !== null && $status !== '')
                    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
            <span class="count-chip">{{ $products->total() }} produk</span>
        </div>

        @if ($products->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-mug-hot"></i></div>
                <h3>Tidak ada produk</h3>
                <p>{{ $search || $categoryId ? 'Tidak ada produk yang cocok dengan filter ini.' : 'Tambahkan menu pertama agar bisa dijual dari aplikasi kasir.' }}</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah produk</a>
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th class="t-right">Harga</th>
                            <th class="t-right">Terjual 30 hari</th>
                            <th>Status</th>
                            <th class="t-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="{{ $product->status ? '' : 'row-muted' }}">
                                <td>
                                    <div class="cell-media">
                                        @if ($product->image)
                                            <img src="{{ asset($product->image) }}" alt="" class="thumb" loading="lazy">
                                        @else
                                            <span class="thumb"><i class="fas fa-image"></i></span>
                                        @endif
                                        <div style="min-width:0">
                                            <div class="cell-main">
                                                {{ $product->name }}
                                                @if ($product->is_favorite)
                                                    <i class="fas fa-star fav" title="Favorit" aria-label="Favorit"></i>
                                                @endif
                                            </div>
                                            <div class="cell-sub text-truncate" style="max-width:300px">{{ $product->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td class="t-right num font-weight-bold">Rp {{ $rp($product->price) }}</td>
                                <td class="t-right num">{{ $rp($sold[$product->id] ?? 0) }}</td>
                                <td>
                                    @if ($product->status)
                                        <span class="tag tag--sage">Aktif</span>
                                    @else
                                        <span class="tag">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="t-actions">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-light btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline"
                                        data-confirm="{{ $product->name }} akan dihapus beserta rincian item penjualannya. Bila hanya ingin menyembunyikan dari kasir, ubah statusnya menjadi Nonaktif."
                                        data-confirm-title="Hapus produk?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost-danger btn-sm" aria-label="Hapus {{ $product->name }}"><i class="fas fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $products->links() }}
        @endif
    </section>
@endsection
