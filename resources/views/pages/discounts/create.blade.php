@extends('layouts.app')

@section('title', 'Tambah diskon')
@section('crumb')
    <a href="{{ route('discounts.index') }}">Diskon</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Tambah diskon</h1>
            <p class="page-head__lead">Diskon baru langsung tersedia di aplikasi kasir.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-9">
            <form action="{{ route('discounts.store') }}" method="POST" class="panel" novalidate>
                @csrf
                @include('pages.discounts._form')
                <div class="panel__foot">
                    <a href="{{ route('discounts.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan diskon</button>
                </div>
            </form>
        </div>
    </div>
@endsection
