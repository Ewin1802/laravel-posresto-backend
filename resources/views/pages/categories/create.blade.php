@extends('layouts.app')

@section('title', 'Tambah kategori')
@section('crumb')
    <a href="{{ route('categories.index') }}">Kategori</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Tambah kategori</h1>
            <p class="page-head__lead">Foto kategori tampil di aplikasi kasir, jadi pilih gambar yang jelas.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-9">
            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="panel" novalidate>
                @csrf
                @include('pages.categories._form')
                <div class="panel__foot">
                    <a href="{{ route('categories.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan kategori</button>
                </div>
            </form>
        </div>
    </div>
@endsection
