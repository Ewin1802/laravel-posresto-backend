@extends('layouts.app')

@section('title', 'Ubah bahan')
@section('crumb')
    <a href="{{ route('bahans.index') }}">Daftar bahan</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">{{ $bahan->nama_bahan }}</h1>
            <p class="page-head__lead">Nama dan satuan otomatis dirapikan menjadi huruf kapital di awal kata.</p>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-xl-7">
            <form action="{{ route('bahans.update', $bahan) }}" method="POST" class="panel" novalidate>
                @csrf
                @method('PUT')
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-7 form-group">
                            <label for="nama_bahan">Nama bahan</label>
                            <input id="nama_bahan" type="text" name="nama_bahan" value="{{ old('nama_bahan', $bahan->nama_bahan) }}"
                                class="form-control @error('nama_bahan') is-invalid @enderror" required>
                            @error('nama_bahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-5 form-group">
                            <label for="satuan">Satuan</label>
                            <input id="satuan" type="text" name="satuan" value="{{ old('satuan', $bahan->satuan) }}"
                                class="form-control @error('satuan') is-invalid @enderror" required>
                            @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="panel__foot">
                    <a href="{{ route('bahans.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
