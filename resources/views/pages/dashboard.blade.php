@extends('layouts.app')

@section('title', 'Welcome')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">
@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard - ARCH Manajemen</h1>
            </div>
            <div class="row">

                <div class="col-12 col md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                <i class="fas fa-lightbulb text-dark"></i> <!-- Icon lampu menyala -->
                                Welcome to the Dashboard
                            </h4>
                        </div>
                        <div class="card-body">
                            <p>Selamat datang di dashboard ARCH Manajemen. Silahkan gunakan menu di samping untuk mengakses
                                fitur-fitur yang tersedia.</p>
                                <p>Diinformasikan kepada Owner, Website ini menggunakan Layanan Cloud Server yang dibayar per Tahun sebesar Rp.1.000.000,-. Diingatkan untuk membayar kewajiban tersebut setiap tahun sebelum tanggal 23 Januari 2026, agar data penjualan tidak hilang. Terima Kasih.</p>
                        </div>
                    </div>
                </div>

        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush
