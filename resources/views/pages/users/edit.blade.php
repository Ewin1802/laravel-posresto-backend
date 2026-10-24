@extends('layouts.app')

@section('title', 'Ubah pengguna')
@section('crumb')
    <a href="{{ route('users.index') }}">Pengguna</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $user->name }}</h1>
            <p class="page-head__lead">Terdaftar sejak {{ $user->created_at?->translatedFormat('d F Y') }}.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-9">
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="panel" novalidate>
                @csrf
                @method('PUT')
                @include('pages.users._form')
                <div class="panel__foot">
                    <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
