@extends('layouts.app')

@section('title', 'Analisa Keuangan')

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
                <h1>Analisa Keuangan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Analisa</a></div>
                    <div class="breadcrumb-item">Analisa Keuangan</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <!-- Card Filter -->
                <div class="row ">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Filter Berdasarkan Tanggal</h4>
                            </div>

                            <div class="card-body">
                                <form method="GET" action="{{ route('financial.report') }}">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="start_date">Dari Tanggal:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ request('start_date', $start_date ?? Carbon\Carbon::now()->startOfMonth()->toDateString()) }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="end_date">Sampai Tanggal:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ request('end_date', $end_date ?? Carbon\Carbon::now()->endOfMonth()->toDateString()) }}" required>
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

                <div class="row d-flex align-items-start">
                    <!-- Chart -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Perbandingan Total Persediaan & Pendapatan</h4>
                            </div>
                            <div class="card-body d-flex flex-column align-items-center">
                                <div style="width: 300px; height: 300px;">
                                    <canvas id="financialChart"></canvas>
                                </div>
                                <div id="financialMessage" class="mt-3 font-weight-bold text-center"
                                     style="font-size: 18px; max-width: 90%; margin: auto;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data Keuangan + Ringkasan Selisih -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Ringkasan</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Deskripsi</th>
                                                <th>Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Jumlah Belanja</td>
                                                <td>Rp {{ number_format($totalPersediaan, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Jumlah Penjualan</td>
                                                <td>Rp {{ number_format($totalKeuangan, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Pendapatan (Penjualan - Belanja)</td>
                                                <td id="differenceAmount"></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js" ></script> --}}

    <script>
        window.onload = function () {
            var totalPersediaan = {{ $totalPersediaan }};
            var totalKeuangan = {{ $totalKeuangan }};
            var selisih = totalKeuangan - totalPersediaan;

            var ctx = document.getElementById('financialChart').getContext('2d');
            var financialMessage = document.getElementById('financialMessage');

            if (totalPersediaan === 0 && totalKeuangan === 0) {
                financialMessage.innerHTML =
                    "<span style='color: orange; font-size: 18px; text-align: center; display: block;'>⚠️ Data Tidak Tersedia, mungkin kamu masih manganto sehingga kamu malas ba jual ditanggal ini!</span>";
                document.getElementById('financialChart').style.display = 'none';
            } else {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Total Belanja', 'Total Penjualan'],
                        datasets: [{
                            data: [totalPersediaan, totalKeuangan],
                            backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(13, 180, 185, 0.6)'],
                            borderColor: ['rgba(255, 99, 132, 1)', 'rgba(13, 180, 185, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' }
                        }
                    }
                });

                var differenceElement = document.getElementById('differenceAmount');
                differenceElement.innerHTML = "Rp " + new Intl.NumberFormat('id-ID').format(selisih);
                differenceElement.style.color = selisih < 0 ? 'red' : 'green';

                // Menampilkan pesan berdasarkan selisih
                if (totalKeuangan === totalPersediaan) {
                    financialMessage.innerHTML =
                        "<span style='color: orange; font-size: 18px; text-align: center; display: block;'>😵 Usahamu Jalan di tempat, Hidop mar Mati!</span>";
                } else if (totalKeuangan < totalPersediaan) {
                    financialMessage.innerHTML =
                        "<span style='color: red; font-size: 18px; text-align: center; display: block;'>⚠️ Belanjamu terlalu banyak, perlu improvisasi untuk menyesuaikan diri dengan kebutuhan pasar dan pelanggan!</span>";
                } else if (totalKeuangan > totalPersediaan) {
                    financialMessage.innerHTML =
                        "<span style='color: green; font-size: 18px; text-align: center; display: block;'>🎉 Fulus maso bagus, Kamu layak buka cabang di Turki!</span>";
                }
            }
        };
    </script>


@endpush

