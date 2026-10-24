@extends('layouts.app')

@section('title', 'Ubah diskon')
@section('crumb')
    <a href="{{ route('discounts.index') }}">Diskon</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $discount->name }}</h1>
            <p class="page-head__lead">Perubahan berlaku untuk transaksi berikutnya, bukan transaksi yang sudah tercatat.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-9">
            <form action="{{ route('discounts.update', $discount->id) }}" method="POST" class="panel" novalidate>
                @csrf
                @method('PUT')
                @include('pages.discounts._form')
                <div class="panel__foot">
                    <a href="{{ route('discounts.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
