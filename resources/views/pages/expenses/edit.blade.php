@extends('layouts.app')

@section('title', 'Ubah pengeluaran')
@section('crumb')
    <a href="{{ route('expenses.index') }}">Pengeluaran</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $expense->description }}</h1>
            <p class="page-head__lead">
                Dicatat {{ $expense->user->name ?? 'sistem' }} pada {{ $expense->created_at?->translatedFormat('d F Y, H:i') }}.
            </p>
        </div>
    </div>

    @include('layouts.alert')

    <form action="{{ route('expenses.update', $expense) }}" method="POST" class="panel" novalidate>
        @csrf
        @method('PUT')
        @include('pages.expenses._form')
        <div class="panel__foot">
            <a href="{{ route('expenses.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
@endsection
