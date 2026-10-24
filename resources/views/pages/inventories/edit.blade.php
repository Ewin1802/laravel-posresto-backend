@extends('layouts.app')

@section('title', 'Ubah persediaan')
@section('crumb')
    <a href="{{ route('inventories.index') }}">Persediaan masuk</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $inventory->nama_bahan }}</h1>
            <p class="page-head__lead">
                Batch masuk {{ $inventory->tanggal_masuk ? \Carbon\Carbon::parse($inventory->tanggal_masuk)->translatedFormat('d F Y, H:i') : '-' }}.
            </p>
        </div>
    </div>

    @include('layouts.alert')

    <form action="{{ route('inventories.update', $inventory) }}" method="POST" enctype="multipart/form-data" class="panel" novalidate>
        @csrf
        @method('PUT')
        @include('pages.inventories._form')
        <div class="panel__foot">
            <a href="{{ route('inventories.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
@endsection

@include('pages.inventories._bahan-modal')
