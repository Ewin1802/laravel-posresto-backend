@extends('layouts.app')

@section('title', 'Tambah produk')
@section('crumb')
    <a href="{{ route('products.index') }}">Produk</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Tambah produk</h1>
            <p class="page-head__lead">Produk baru langsung muncul di aplikasi kasir setelah disimpan.</p>
        </div>
    </div>

    @include('layouts.alert')

    @if ($categories->isEmpty())
        <div class="notice mb-4">
            <i class="fas fa-circle-info"></i>
            <div>Belum ada kategori. <a href="{{ route('categories.create') }}">Buat kategori</a> terlebih dahulu sebelum menambahkan produk.</div>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="panel" novalidate>
        @csrf
        @include('pages.products._form')
        <div class="panel__foot">
            <a href="{{ route('products.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan produk</button>
        </div>
    </form>
@endsection
