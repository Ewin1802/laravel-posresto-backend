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
                <h1>Laporan Fuluuusss</h1>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Filter Berdasarkan Tanggal</h4>
                            </div>

                            <div class="card-body">
                                <!-- Alert Error (Jika Ada) -->
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Error!</strong> {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Form Filter -->
                                <form method="GET" action="{{ route('order_reports.index') }}">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="start_date">Dari Tanggal:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ request('start_date', $start_date) }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="end_date">Sampai Tanggal:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ request('end_date', $end_date) }}" required>
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
                                                Pembayaran Cash
                                                <span>{{ number_format($summary['total_cash'], 2) }}</span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Pembayaran Transfer
                                                <span>{{ number_format($summary['total_transfer'], 2) }}</span>
                                            </li>

                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Total (Fulus Bersih)
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
                                                {{-- <th>Order ID</th> --}}
                                                <th>Nama Pengunjung</th>
                                                <th>Bayar</th>
                                                <th>Diskon</th>
                                                <th>Pajak</th>
                                                {{-- <th>Service Charge</th> --}}
                                                <th>Subtotal</th>
                                                <th>Metode</th>
                                                <th>Tanggal & Waktu</th>

                                                <th>Action</th>
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
                                                        {{-- <td>{{ $order->id }}</td> --}}
                                                        <td>{{ $order->customer_name }}</td>
                                                        <td>{{ number_format($order->payment_amount, 2) }}</td>
                                                        <td>{{ number_format($order->discount_amount, 2) }}</td>
                                                        <td>{{ number_format($order->tax, 2) }}</td>
                                                        {{-- <td>{{ number_format($order->service_charge, 2) }}</td> --}}
                                                        <td>{{ number_format($order->sub_total, 2) }}</td>
                                                        <td>
                                                            @php
                                                                $method = strtolower($order->payment_method);
                                                            @endphp

                                                            @if ($method === 'cash')
                                                                <span class="badge badge-success">Cash</span>
                                                            @elseif ($method === 'transfer')
                                                                <span class="badge badge-primary">Transfer</span>
                                                            @else
                                                                <span class="badge badge-secondary">
                                                                    {{ ucfirst($order->payment_method) }}
                                                                </span>
                                                            @endif
                                                        </td>

                                                        {{-- <td>{{ \Carbon\Carbon::parse($order->transaction_time)->format('Y-m-d') }}</td> <!-- Tanggal -->
                                                    <td>{{ \Carbon\Carbon::parse($order->transaction_time)->addHours(2)->format('H:i:s') }}</td> <!-- Waktu Garida (+2 Jam) --> --}}
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($order->transaction_time)->addHours(2)->format('Y-m-d H:i:s') }}
                                                        </td>

                                                        <td>
                                                            <button class="btn btn-info btn-sm view-details"
                                                                data-id="{{ $order->id }}">Detail</button>
                                                        </td>
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
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>

    <script>
        $(document).ready(function() {
            const ctx = document.getElementById('summaryChart').getContext('2d');
            new Chart(ctx, {
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
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1',
                            '#dc3545'
                        ],
                        borderColor: ['#0056b3', '#1e7e34', '#d39e00', '#117a8b', '#563d7c',
                            '#c82333'
                        ],
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
        });
        function printStruk() {
            window.print();
        }
    </script>



    <script>
        $(document).ready(function() {
            // Pastikan dropdown yang aktif tetap terbuka setelah reload
            $('.nav-item.dropdown.active').find('.dropdown-menu').show();
            $('.nav-link.has-dropdown').click(function(e) {
                e.preventDefault();
                let $parent = $(this).parent();
                if ($parent.hasClass('active')) {
                    $parent.removeClass('active');
                    $parent.find('.dropdown-menu').slideUp(200);
                } else {
                    $('.nav-item.dropdown').removeClass('active');
                    $('.dropdown-menu').slideUp(200);
                    $parent.addClass('active');
                    $parent.find('.dropdown-menu').slideDown(200);
                }
            });

            $('.view-details').click(function() {
                let orderId = $(this).data('id');
                $('#order-id').text(orderId);
                $('#order-items').html('');
                $('#customer-name').text('');
                $('#total-bayar').text(''); // Reset total bayar

                $.ajax({
                    url: `/orders/${orderId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.order) {
                            $('#customer-name').text(response.order.customer_name);

                            let totalBayar = 0; // Inisialisasi total pembayaran

                            if (response.items.length > 0) {
                                response.items.forEach(item => {
                                    let totalItem = parseFloat(item.total.replace(/,/g,
                                        '')); // Konversi string ke angka
                                    totalBayar += totalItem;

                                    $('#order-items').append(`
                                        <tr>
                                            <td>${item.product_name}</td>
                                            <td>${item.quantity}</td>
                                            <td>${item.price}</td>
                                            <td>${item.total}</td>
                                        </tr>
                                    `);
                                });

                                // Format total bayar dengan 2 desimal dan ribuan separator
                                $('#total-bayar').text(new Intl.NumberFormat('id-ID', {
                                    minimumFractionDigits: 2
                                }).format(totalBayar));
                            } else {
                                $('#order-items').html(
                                    '<tr><td colspan="4" class="text-center">Tidak ada item dalam pesanan ini.</td></tr>'
                                );
                                $('#total-bayar').text('0.00');
                            }

                            $('#orderDetailModal').modal('show');
                        } else {
                            alert('Data pesanan tidak ditemukan.');
                        }
                        // ===== PRINT DATA =====
                        $('#print-order-id').text(orderId);
                        $('#print-customer').text(response.order.customer_name);

                        let itemsPrint = '';
                        let totalPrint = 0;

                        response.items.forEach(item => {
                            let t = parseFloat(item.total.replace(/,/g, ''));
                            totalPrint += t;

                            itemsPrint += `
                            <p>${item.product_name}</p>
                            <p>${item.quantity} x ${item.price} = ${item.total}</p>
                        `;
                        });

                        $('#print-items').html(itemsPrint);
                        $('#print-total').text(new Intl.NumberFormat('id-ID').format(
                            totalPrint));

                        let now = new Date();
                        $('#print-date').text(now.toLocaleString('id-ID'));
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Gagal mengambil data pesanan.');
                    }
                });
            });
        });
    </script>
@endpush

<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Detail Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><strong>Order ID:</strong> <span id="order-id"></span></h6>
                <h6><strong>Nama Customer:</strong> <span id="customer-name"></span></h6>
                <hr>
                <h6><strong>Rincian Pesanan:</strong></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <!-- Data dari AJAX akan dimasukkan di sini -->
                    </tbody>
                </table>
                <hr>
                <h6><strong>Total Bayar:</strong> <span id="total-bayar" class="text-primary font-weight-bold"></span>
                </h6>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="printStruk()">
                    🖨️ Print Struk
                </button>
            </div>
        </div>
    </div>
</div>

<div id="print-area" style="display:none;">
    <div class="struk">

        <center>
            <h3>ARCH</h3>
            <p>Kompleks Batu Pinagut</p>
            <p>Boroko Timur, Kaidipang</p>
            <p>Kab. Bolaang Mongondow Utara</p>
            <p>0821 9511 0639</p>
        </center>

        <hr>

        <p id="print-date"></p>
        <p>Order : <span id="print-order-id"></span></p>
        <p>Customer : <span id="print-customer"></span></p>

        <hr>

        <div id="print-items"></div>

        <hr>

        <p><b>Total : <span id="print-total"></span></b></p>

        <hr>

        <center>
            <p>Terima Kasih 🙏</p>
        </center>

    </div>
</div>
