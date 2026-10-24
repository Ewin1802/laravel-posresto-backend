@extends('layouts.app')

@section('title', 'Ubah kategori')
@section('crumb')
    <a href="{{ route('categories.index') }}">Kategori</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $category->name }}</h1>
            <p class="page-head__lead">Dipakai oleh {{ $category->products_count }} produk.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-9">
            <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="panel" novalidate>
                @csrf
                @method('PUT')
                @include('pages.categories._form')
                <div class="panel__foot">
                    <a href="{{ route('categories.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
