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
                <h1>Order Report</h1>
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
                                <h4>Filter Orders</h4>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('order_reports.index') }}">
                                    <div class="form-row">
                                        <div class="col-md-5">
                                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Summary Section -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Order Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Revenue
                                                <span>{{ number_format($summary['total_revenue'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Discount
                                                <span>{{ number_format($summary['total_discount'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Tax
                                                <span>{{ number_format($summary['total_tax'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Subtotal
                                                <span>{{ number_format($summary['total_subtotal'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total Service Charge
                                                <span>{{ number_format($summary['total_service_charge'], 2) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total (Pendapatan Bersih)
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
                        {{-- <div class="card">
                            <div class="card-header">
                                <h4>Orders List</h4>
                            </div>
                            <div class="card-body">
                                @if ($orders->isEmpty())
                                    <p class="text-center">No orders found for the selected date range.</p>
                                @else
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
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div> --}}

                        <div class="card">
                            <div class="card-header">
                                <h4>Orders List</h4>
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
                                            </tr>
                                        </thead>
                                        <tbody id="orders-tbody">
                                            <!-- Orders will be loaded here dynamically -->
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
                                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($orders->hasMorePages())
                                    <button id="load-more" class="btn btn-primary btn-block" data-next-page="{{ $orders->nextPageUrl() }}">
                                        Load More
                                    </button>
                                @endif
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
        const ctx = document.getElementById('summaryChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Revenue', 'Discount', 'Tax', 'Subtotal', 'Service Charge', 'Total'],
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const loadMoreButton = document.getElementById('load-more');

        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function () {
                const nextPageUrl = this.getAttribute('data-next-page');

                if (nextPageUrl) {
                    // Fetch data from the next page
                    fetch(nextPageUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Update the table with new data
                            const tbody = document.getElementById('orders-tbody');
                            data.data.forEach((order, index) => {
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${order.id}</td>
                                        <td>${order.customer_name || '-'}</td>
                                        <td>${formatNumber(order.payment_amount)}</td>
                                        <td>${formatNumber(order.discount_amount)}</td>
                                        <td>${formatNumber(order.tax)}</td>
                                        <td>${formatNumber(order.service_charge)}</td>
                                        <td>${formatNumber(order.sub_total)}</td>
                                        <td>${new Date(order.created_at).toISOString().split('T')[0]}</td>
                                    </tr>
                                `;
                                tbody.innerHTML += row;
                            });

                            // Update the "Load More" button
                            if (data.next_page_url) {
                                loadMoreButton.setAttribute('data-next-page', data.next_page_url);
                            } else {
                                // If no more pages, remove the button
                                loadMoreButton.remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading more orders:', error);
                        });
                }
            });
        }
    });

    // Fungsi untuk memformat angka dengan pemisah ribuan
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
    }


    </script>
@endpush
