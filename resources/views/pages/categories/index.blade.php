@extends('layouts.app')

@section('title', 'Kategori')
@section('crumb', 'Data umum')

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Kategori</h1>
            <p class="page-head__lead">Kelompok menu yang tampil sebagai tab di aplikasi kasir dan halaman publik.</p>
        </div>
        <div class="page-head__actions">
            <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah kategori</a>
        </div>
    </div>

    @include('layouts.alert')

    <section class="panel">
        <div class="panel__head">
            <form method="GET" action="{{ route('categories.index') }}" class="toolbar">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="name" value="{{ $search }}" placeholder="Cari kategori" aria-label="Cari kategori">
                </div>
                @if ($search)
                    <a href="{{ route('categories.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
            <span class="count-chip">{{ $categories->total() }} kategori</span>
        </div>

        @if ($categories->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-layer-group"></i></div>
                <h3>{{ $search ? 'Kategori tidak ditemukan' : 'Belum ada kategori' }}</h3>
                <p>{{ $search ? 'Coba kata kunci lain.' : 'Buat kategori seperti Kopi, Non-kopi, atau Makanan sebelum menambahkan produk.' }}</p>
                @unless ($search)
                    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah kategori</a>
                @endunless
            </div>
        @else
            <div class="tile-grid">
                @foreach ($categories as $category)
                    <article class="tile">
                        <div class="tile__media" @if ($category->image) style="background-image:url('{{ asset($category->image) }}')" @endif>
                            @unless ($category->image)
                                <i class="fas fa-image"></i>
                            @endunless
                        </div>
                        <div class="tile__body">
                            <h3 class="tile__title">{{ $category->name }}</h3>
                            <p class="tile__text">{{ $category->description ?: 'Tanpa deskripsi' }}</p>
                        </div>
                        <div class="tile__foot">
                            <span class="tag tag--plain">{{ $category->products_count }} produk</span>
                            <span>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-light btn-sm" aria-label="Ubah {{ $category->name }}"><i class="fas fa-pen"></i></a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline"
                                    data-confirm="{{ $category->products_count > 0 ? 'Kategori ini masih dipakai ' . $category->products_count . ' produk, sehingga tidak akan bisa dihapus sebelum produknya dipindahkan.' : 'Kategori ' . $category->name . ' akan dihapus permanen.' }}"
                                    data-confirm-title="Hapus kategori?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost-danger btn-sm" aria-label="Hapus {{ $category->name }}"><i class="fas fa-trash-can"></i></button>
                                </form>
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>
            {{ $categories->links() }}
        @endif
    </section>
@endsection
