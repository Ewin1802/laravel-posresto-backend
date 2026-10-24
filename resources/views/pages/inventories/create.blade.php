@extends('layouts.app')

@section('title', 'Catat pembelian')
@section('crumb')
    <a href="{{ route('inventories.index') }}">Persediaan masuk</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Catat pembelian bahan</h1>
            <p class="page-head__lead">Tanggal dan jam masuk dicatat otomatis saat disimpan.</p>
        </div>
    </div>

    @include('layouts.alert')

    <form action="{{ route('inventories.store') }}" method="POST" enctype="multipart/form-data" class="panel" novalidate>
        @csrf
        @include('pages.inventories._form')
        <div class="panel__foot">
            <a href="{{ route('inventories.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan pembelian</button>
        </div>
    </form>
@endsection

@include('pages.inventories._bahan-modal')
