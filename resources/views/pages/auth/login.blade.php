@extends('layouts.auth')

@section('title', 'Masuk')

@section('main')
    <h1>Selamat datang kembali</h1>
    <p class="lead">Masuk untuk mengelola penjualan dan persediaan ARCH.</p>

    @if (session('status'))
        <div class="flash flash--info"><i class="fas fa-circle-info"></i><div class="flash__body">{{ session('status') }}</div></div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                autocomplete="username" placeholder="nama@arch.id" autofocus required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Kata sandi</label>
            <div class="password-field">
                <input id="password" type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    autocomplete="current-password" required>
                <button type="button" data-toggle-password aria-label="Tampilkan kata sandi"><i class="fas fa-eye"></i></button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group d-flex align-items-center justify-content-between">
            <label class="d-flex align-items-center mb-0" style="gap:8px; font-weight:500; cursor:pointer;">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Ingat saya di perangkat ini
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Masuk</button>
    </form>
@endsection
