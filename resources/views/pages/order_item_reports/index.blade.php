@extends('layouts.app')

@section('title', 'Produk Terlaris')

{{-- Favicon --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Produk Terlaris</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Laporan</a></div>
                    <div class="breadcrumb-item">Daftar Produk Terlaris</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <!-- Card Filter -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Filter Berdasarkan Tanggal</h4>
                            </div>

                            <div class="card-body">
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Error!</strong> {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <form method="GET" action="{{ route('top.products') }}">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="start_date">Dari Tanggal:</label>
                                            {{-- <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $startDate) }}"> --}}
                                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $start_date ?? Carbon::now()->startOfMonth()->toDateString()) }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="end_date">Sampai Tanggal:</label>
                                            {{-- <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', $endDate) }}"> --}}
                                            <input type="date" name="end_date" id="end_date" class="form-control"
    value="{{ request('end_date', $end_date ?? Carbon::now()->endOfMonth()->toDateString()) }}" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-filter"></i> Filter
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Produk Terlaris -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Grafik Produk Terlaris</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="productChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Top Produk Terlaris ARCH saat ini</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Produk</th>
                                                <th>Jumlah Dipesan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topProducts as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->total_quantity }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="float-right">
                                    {{ $topProducts->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        var ctx = document.getElementById('productChart').getContext('2d');
        var productChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Jumlah Dipesan',
                    data: @json($chartData['data']),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });
    </script>
@endpush
