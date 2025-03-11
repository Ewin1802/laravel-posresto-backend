@extends('layouts.app')

@section('title', 'Edit Bahan')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">
@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Bahan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Bahan</a></div>
                    <div class="breadcrumb-item">Edit Bahan</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Bahan</h2>

                <div class="card">
                    <form action="{{ route('bahans.update', $bahan) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h4>Form Edit Bahan</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Bahan</label>
                                <input type="text"
                                    class="form-control @error('nama_bahan') is-invalid @enderror"
                                    name="nama_bahan" value="{{ old('nama_bahan', $bahan->nama_bahan) }}">
                                @error('nama_bahan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Satuan</label>
                                <input type="text"
                                    class="form-control @error('satuan') is-invalid @enderror"
                                    name="satuan" value="{{ old('satuan', $bahan->satuan) }}">
                                @error('satuan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
@endpush
