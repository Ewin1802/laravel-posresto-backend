@extends('layouts.app')

@section('title', 'Catat pengeluaran')
@section('crumb')
    <a href="{{ route('expenses.index') }}">Pengeluaran</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Catat pengeluaran</h1>
            <p class="page-head__lead">Gunakan "Simpan & catat lagi" bila ada beberapa nota sekaligus.</p>
        </div>
        <div class="page-head__actions">
            <button type="button" class="btn btn-light" data-guide-open="expenses"><i class="fas fa-book-open"></i> Panduan</button>
        </div>
    </div>

    @include('layouts.alert')

    <form action="{{ route('expenses.store') }}" method="POST" class="panel" novalidate>
        @csrf
        @include('pages.expenses._form')
        <div class="panel__foot">
            <a href="{{ route('expenses.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" name="again" value="1" class="btn btn-light">Simpan &amp; catat lagi</button>
            <button type="submit" class="btn btn-primary">Simpan pengeluaran</button>
        </div>
    </form>
@endsection

@include('pages.expenses._guide')
