@extends('layouts.app')

@section('title', 'Tambah pengguna')
@section('crumb')
    <a href="{{ route('users.index') }}">Pengguna</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Tambah pengguna</h1>
            <p class="page-head__lead">Buat akun baru untuk staf atau kasir ARCH.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-9">
            <form action="{{ route('users.store') }}" method="POST" class="panel" novalidate>
                @csrf
                @include('pages.users._form')
                <div class="panel__foot">
                    <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan pengguna</button>
                </div>
            </form>
        </div>
    </div>
@endsection
