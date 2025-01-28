@extends('layouts.app')

@section('title', 'Order Report')
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Laporan Pesanan dan Keuangan</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Filter berdasarkan Tanggal</h4>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('order_reports.index') }}">
                                    <div class="form-row">
                                        <div class="col-md-5">
                                            <input type="date" name="start_date" class="form-control" value="{{ $start_date }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="date" name="end_date" class="form-control" value="{{ $end_date }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Ringkasan</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Payment Amount (Jumlah Pembayaran Konsumen)
                                                <span>{{ number_format($summary['total_revenue'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Discount
                                                <span>{{ number_format($summary['total_discount'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Tax (PB1)
                                                <span>{{ number_format($summary['total_tax'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Subtotal (Harga Produk sebelum diskon dan tax)
                                                <span>{{ number_format($summary['total_subtotal'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Service Charge
                                                <span>{{ number_format($summary['total_service_charge'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total (Faedah Bersih)
                                                <span>{{ number_format($summary['total'], 2) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <canvas id="summaryChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Table -->
                       <!-- Orders Table -->
                       <div class="card">
                        <div class="card-header">
                            <h4>Daftar Transaksi</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Payment Amount</th>
                                            <th>Discount</th>
                                            <th>Tax</th>
                                            <th>Service Charge</th>
                                            <th>Subtotal</th>
                                            <th>Date</th>
                                            <th>Time</th> <!-- Kolom baru untuk jam -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($orders->isEmpty())
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    {{ $start_date && $end_date ? 'Tidak ada data transaksi ditemukan untuk rentang tanggal yang dipilih.' : 'Silakan pilih rentang tanggal untuk menampilkan data.' }}
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $order->id }}</td>
                                                    <td>{{ $order->customer_name }}</td>
                                                    <td>{{ number_format($order->payment_amount, 2) }}</td>
                                                    <td>{{ number_format($order->discount_amount, 2) }}</td>
                                                    <td>{{ number_format($order->tax, 2) }}</td>
                                                    <td>{{ number_format($order->service_charge, 2) }}</td>
                                                    <td>{{ number_format($order->sub_total, 2) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($order->transaction_time)->format('Y-m-d') }}</td> <!-- Tanggal -->
                                                    <td>{{ \Carbon\Carbon::parse($order->transaction_time)->format('H:i:s') }}</td> <!-- Jam -->
                                                </tr>
                                            @endforeach
                                        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js for summary visualization
        const ctx = document.getElementById('summaryChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Payment Amount', 'Discount', 'Tax', 'Subtotal', 'Service Charge', 'Total'],
                datasets: [{
                    label: 'Summary',
                    data: [
                        {{ $summary['total_revenue'] }},
                        {{ $summary['total_discount'] }},
                        {{ $summary['total_tax'] }},
                        {{ $summary['total_subtotal'] }},
                        {{ $summary['total_service_charge'] }},
                        {{ $summary['total'] }}
                    ],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#dc3545'],
                    borderColor: ['#0056b3', '#1e7e34', '#d39e00', '#117a8b', '#563d7c', '#c82333'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
