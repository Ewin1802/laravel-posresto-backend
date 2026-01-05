@extends('layouts.app')

@section('title', 'Laporan Persediaan')

{{-- Favicon --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <style>
        /* Styling tabel */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #444; /* Garis tepi tabel */
            background-color: #fff;
        }

        /* Styling header tabel */
        .styled-table thead {
            background-color: #5c2c08;
            color: white;
        }

        .styled-table th {
            padding: 12px;
            border: 2px solid #ddd; /* Garis antar kolom soft */
            text-align: center;
        }

        /* Styling isi tabel */
        .styled-table td {
            padding: 10px;
            border: 1px solid #ddd; /* Warna soft antar kolom */
            text-align: center;
            font-weight: normal; /* Hapus cetak tebal */
        }

        /* Warna selang-seling antar baris */
        .styled-table tbody tr:nth-child(even) {
            background-color: #f8f9fa; /* Warna abu-abu sangat soft */
        }

        /* Pemisah antar bahan tanpa baris kosong */
        .separator td {
            border-top: 3px solid #5c2c08 !important; /* Garis pemisah antar bahan */
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Laporan Persediaan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Laporan</a></div>
                    <div class="breadcrumb-item">Laporan Persediaan</div>
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

                                <form method="GET" action="{{ route('inventory.reports') }}">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="start_date">Dari Tanggal:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', $start_date) }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="end_date">Sampai Tanggal:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', $end_date) }}" required>
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

                <!-- Tabel Data Persediaan -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Stok Persediaan Saat Ini</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="styled-table">
                                        <thead>
                                            <tr>
                                                <th>Nama Bahan</th>
                                                <th>Tanggal Masuk</th>
                                                <th>Satuan</th>
                                                <th>Jumlah Masuk</th>
                                                <th>Harga</th>
                                                <th>Total (Masuk)</th>
                                                <th>Jumlah Keluar</th>
                                                <th>Harga</th>
                                                <th>Total (Keluar)</th>
                                                <th>Tanggal Keluar</th>
                                                <th>Penerima</th>
                                                <th>Sisa Stok</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $previousBahanId = null;
                                            @endphp

                                            @forelse($inventoryReport as $item)
                                                @php
                                                    $firstRow = true;
                                                    $sisaStok = $item->saldo_awal; // Mulai dari saldo awal
                                                @endphp

                                                @foreach($item->inventoryOut as $out)
                                                @php
                                                    // Hitung Sisa Stok per transaksi keluar
                                                    $sisaStok -= $out->quantity_out;

                                                    // Tambahkan class "separator" jika bahan_id berubah
                                                    $rowClass = ($previousBahanId !== null && $previousBahanId !== $item->bahan_id) ? 'separator' : '';
                                                @endphp

                                                <tr class="{{ $rowClass }}">
                                                    @if ($firstRow)
                                                        <td>{{ $item->bahan->nama_bahan }}</td>
                                                        <td>{{ $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y H:i') : '-' }}</td> <!-- Perbaikan disini -->
                                                        <td>{{ $item->satuan }}</td>
                                                        <td>{{ $item->saldo_awal }}</td>
                                                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                        <td>Rp {{ number_format($item->saldo_awal * $item->harga_satuan, 0, ',', '.') }}</td>
                                                        @php $firstRow = false; @endphp
                                                    @else
                                                        <td></td><td></td><td></td><td></td><td></td><td></td>
                                                    @endif

                                                    <td>{{ $out->quantity_out }}</td>
                                                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($out->quantity_out * $item->harga_satuan, 0, ',', '.') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($out->created_at)->format('d-m-Y H:i') }}</td>
                                                    <td>{{ $out->receiver ?? '-' }}</td> <!-- Menampilkan Nama Penerima -->
                                                    <td>{{ $sisaStok }}</td>
                                                </tr>

                                                @php
                                                    // Pindahkan pembaruan previousBahanId ke dalam loop agar selalu diperbarui
                                                    $previousBahanId = $item->bahan_id;
                                                @endphp
                                                @endforeach


                                                @if ($item->inventoryOut->isEmpty())
                                                    @php
                                                        $rowClass = ($previousBahanId !== null && $previousBahanId !== $item->bahan_id) ? 'separator' : '';
                                                    @endphp
                                                    <tr class="{{ $rowClass }}">
                                                        <td>{{ $item->bahan->nama_bahan }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}</td>
                                                        <td>{{ $item->satuan }}</td>
                                                        <td>{{ $item->saldo_awal }}</td>
                                                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                        <td>Rp {{ number_format($item->saldo_awal * $item->harga_satuan, 0, ',', '.') }}</td>
                                                        <td colspan="4" class="text-center">Belum ada pengeluaran</td>
                                                        <td>{{ $item->saldo_awal }}</td> <!-- Jika tidak ada keluar, sisa stok = saldo awal -->
                                                    </tr>

                                                    @php
                                                        $previousBahanId = $item->bahan_id;
                                                    @endphp
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="float-right">
                                    {{ $inventoryReport->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
