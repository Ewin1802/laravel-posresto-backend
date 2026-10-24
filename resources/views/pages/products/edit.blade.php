@extends('layouts.app')

@section('title', 'Ubah produk')
@section('crumb')
    <a href="{{ route('products.index') }}">Produk</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $product->name }}</h1>
            <p class="page-head__lead">Terakhir diperbarui {{ $product->updated_at?->diffForHumans() }}.</p>
        </div>
    </div>

    @include('layouts.alert')

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="panel" novalidate>
        @csrf
        @method('PUT')
        @include('pages.products._form')
        <div class="panel__foot">
            <a href="{{ route('products.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
@endsection
