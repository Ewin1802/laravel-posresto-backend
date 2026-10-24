@extends('layouts.app')

@section('title', 'Laporan penjualan')
@section('crumb', 'Laporan')

@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $rangeLabel = \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') . ' – ' . \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y');
@endphp

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Laporan penjualan</h1>
            <p class="page-head__lead">{{ $rangeLabel }}, berdasarkan waktu transaksi di aplikasi kasir.</p>
        </div>
    </div>

    @include('layouts.alert')

    @include('pages.partials.range-filter', [
        'action' => route('order_reports.index'),
        'start' => $start_date,
        'end' => $end_date,
    ])

    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-sack-dollar"></i> Fulus bersih</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($summary['total']) }}</div>
                <div class="stat__meta">Subtotal − diskon − pajak + service</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-receipt"></i> Transaksi</div>
                <div class="stat__value">{{ $rp($summary['orders']) }}</div>
                <div class="stat__meta">{{ $rp($summary['total_items']) }} item terjual</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-scale-balanced"></i> Rata-rata per transaksi</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($summary['avg']) }}</div>
                <div class="stat__meta">Dari fulus bersih</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat">
                <div class="stat__label"><i class="fas fa-hand-holding-dollar"></i> Uang diterima</div>
                <div class="stat__value"><small>Rp</small>{{ $rp($summary['total_revenue']) }}</div>
                <div class="stat__meta">Total pembayaran pelanggan</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Penjualan harian</h2>
                        <p class="panel__sub">Fulus bersih per hari dalam rentang terpilih</p>
                    </div>
                </div>
                <div class="panel__body">
                    @if (array_sum($chartSummary['net']) > 0)
                        <div class="chart-box"><canvas id="dailyChart" aria-label="Grafik penjualan harian"></canvas></div>
                    @else
                        <div class="empty" style="padding:70px 24px">
                            <div class="empty__icon"><i class="fas fa-chart-column"></i></div>
                            <h3>Belum ada penjualan</h3>
                            <p>Tidak ada transaksi pada rentang tanggal ini. Coba pilih rentang lain.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2 class="panel__title">Rincian</h2>
                        <p class="panel__sub">Komponen pembentuk fulus bersih</p>
                    </div>
                </div>
                <div class="panel__body pt-2">
                    <ul class="ledger">
                        <li><span>Subtotal produk</span><span class="num">Rp {{ $rp($summary['total_subtotal']) }}</span></li>
                        <li><span>Diskon</span><span class="num text-red">− Rp {{ $rp($summary['total_discount']) }}</span></li>
                        <li><span>Pajak (PB1)</span><span class="num text-red">− Rp {{ $rp($summary['total_tax']) }}</span></li>
                        <li><span>Service charge</span><span class="num">+ Rp {{ $rp($summary['total_service_charge']) }}</span></li>
                        <li class="is-total"><span>Fulus bersih</span><span class="num">Rp {{ $rp($summary['total']) }}</span></li>
                    </ul>

                    @php $payTotal = max(1, $summary['total_cash'] + $summary['total_transfer']); @endphp
                    <div class="cell-sub mt-4 mb-2">Pembayaran diterima</div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between" style="font-size:13.5px">
                            <span class="font-weight-bold">Cash</span><span class="num">Rp {{ $rp($summary['total_cash']) }}</span>
                        </div>
                        <div class="meter mt-1"><span style="width: {{ ($summary['total_cash'] / $payTotal) * 100 }}%"></span></div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between" style="font-size:13.5px">
                            <span class="font-weight-bold">Transfer</span><span class="num">Rp {{ $rp($summary['total_transfer']) }}</span>
                        </div>
                        <div class="meter meter--gold mt-1"><span style="width: {{ ($summary['total_transfer'] / $payTotal) * 100 }}%"></span></div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <div>
                <h2 class="panel__title">Daftar transaksi</h2>
                <p class="panel__sub">Klik Rincian untuk melihat item dan mencetak ulang struk</p>
            </div>
            <form method="GET" action="{{ route('order_reports.index') }}" class="toolbar">
                <input type="hidden" name="start_date" value="{{ $start_date }}">
                <input type="hidden" name="end_date" value="{{ $end_date }}">
                <div class="search">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" class="form-control" name="q" value="{{ $search }}" placeholder="Pelanggan, kasir, atau no. order" aria-label="Cari transaksi">
                </div>
                <select name="method" class="custom-select text-capitalize" style="width:auto" onchange="this.form.submit()" aria-label="Metode pembayaran">
                    <option value="">Semua metode</option>
                    @foreach ($methods as $m)
                        <option value="{{ $m }}" @selected($method === $m)>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($orders->isEmpty())
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-receipt"></i></div>
                <h3>Tidak ada transaksi</h3>
                <p>Tidak ada transaksi yang cocok untuk rentang tanggal dan filter ini.</p>
            </div>
        @else
            <div class="table-scroll">
                <table class="table-x">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pelanggan</th>
                            <th class="t-right">Subtotal</th>
                            <th class="t-right">Diskon</th>
                            <th class="t-right">Pajak</th>
                            <th class="t-right">Dibayar</th>
                            <th>Metode</th>
                            <th class="t-actions"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $m = strtolower($order->payment_method);
                                try { $tx = \Carbon\Carbon::parse($order->transaction_time)->addHours(2); } catch (\Throwable $e) { $tx = null; }
                            @endphp
                            <tr>
                                <td>
                                    <div class="num">{{ $tx ? $tx->translatedFormat('d M Y') : $order->transaction_time }}</div>
                                    <div class="cell-sub num">{{ $tx ? $tx->format('H:i:s') : '' }}</div>
                                </td>
                                <td>
                                    <div class="cell-main">{{ $order->customer_name ?: 'Pelanggan umum' }}</div>
                                    <div class="cell-sub">#{{ $order->id }} · {{ $order->total_item }} item · {{ $order->nama_kasir }}</div>
                                </td>
                                <td class="t-right num">{{ $rp($order->sub_total) }}</td>
                                <td class="t-right num {{ $order->discount_amount > 0 ? 'text-red' : 'text-muted' }}">{{ $order->discount_amount > 0 ? '−' . $rp($order->discount_amount) : '—' }}</td>
                                <td class="t-right num text-muted">{{ $rp($order->tax) }}</td>
                                <td class="t-right num font-weight-bold">{{ $rp($order->payment_amount) }}</td>
                                <td>
                                    <span class="tag {{ $m === 'cash' ? 'tag--sage' : ($m === 'transfer' ? 'tag--blue' : '') }}">{{ ucfirst($order->payment_method) }}</span>
                                </td>
                                <td class="t-actions">
                                    <button type="button" class="btn btn-light btn-sm view-details" data-url="{{ route('orders.show', $order->id) }}" data-id="{{ $order->id }}">
                                        Rincian
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        @endif
    </section>
@endsection

@push('modals')
    <div class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailTitle">Order #<span id="order-id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="order-loading" class="text-center text-muted py-4"><i class="fas fa-circle-notch fa-spin"></i> Memuat rincian…</div>
                    <div id="order-content" class="d-none">
                        <div class="kv">
                            <div><span>Pelanggan</span><strong id="customer-name"></strong></div>
                            <div><span>Kasir</span><strong id="cashier-name"></strong></div>
                            <div><span>Waktu</span><strong id="tx-time"></strong></div>
                        </div>
                        <div class="panel mb-3" style="box-shadow:none">
                            <div class="table-scroll" style="border-radius: var(--r-lg)">
                                <table class="table-x">
                                    <thead>
                                        <tr><th>Produk</th><th class="t-right">Qty</th><th class="t-right">Harga</th><th class="t-right">Total</th></tr>
                                    </thead>
                                    <tbody id="order-items"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <ul class="ledger">
                                    <li><span>Subtotal</span><span class="num" id="d-subtotal"></span></li>
                                    <li><span>Diskon</span><span class="num text-red" id="d-discount"></span></li>
                                    <li><span>Pajak</span><span class="num" id="d-tax"></span></li>
                                    <li><span>Service charge</span><span class="num" id="d-service"></span></li>
                                    <li class="is-total"><span>Total</span><span class="num" id="total-bayar"></span></li>
                                    <li><span>Dibayar (<span id="d-method" class="text-capitalize"></span>)</span><span class="num" id="d-paid"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-dark" id="btnPrint" disabled><i class="fas fa-print"></i> Cetak struk</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Area cetak struk thermal 58mm --}}
    <div id="print-area" aria-hidden="true">
        <div class="c">
            <p><b>ARCH</b></p>
            <p>Kompleks Batu Pinagut</p>
            <p>Boroko Timur, Kaidipang</p>
            <p>Kab. Bolaang Mongondow Utara</p>
            <p>0821 9511 0639</p>
        </div>
        <hr>
        <p id="print-date"></p>
        <p>Order : #<span id="print-order-id"></span></p>
        <p>Pelanggan : <span id="print-customer"></span></p>
        <p>Kasir : <span id="print-cashier"></span></p>
        <hr>
        <div id="print-items"></div>
        <hr>
        <div class="row-line"><span>Subtotal</span><span id="print-subtotal"></span></div>
        <div class="row-line"><span>Diskon</span><span id="print-discount"></span></div>
        <div class="row-line"><span>Pajak</span><span id="print-tax"></span></div>
        <div class="row-line"><b>Total</b><b id="print-total"></b></div>
        <div class="row-line"><span>Bayar</span><span id="print-paid"></span></div>
        <hr>
        <div class="c"><p>Terima kasih</p></div>
    </div>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        $(function () {
            var fmt = function (v) { return ARCH.number(Math.round(Number(v || 0))); };

            /* Grafik harian */
            var canvas = document.getElementById('dailyChart');
            if (canvas && window.Chart) {
                Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
                Chart.defaults.color = '#7A706A';
                var orders = @json($chartSummary['orders']);
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: @json($chartSummary['labels']),
                        datasets: [{
                            label: 'Fulus bersih',
                            data: @json($chartSummary['net']),
                            backgroundColor: '#3A2A22',
                            hoverBackgroundColor: '#D7141A',
                            borderRadius: 5,
                            borderSkipped: false,
                            maxBarThickness: 28
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#2B1D17', padding: 12, cornerRadius: 10, displayColors: false,
                                callbacks: {
                                    label: function (c) { return ARCH.rupiah(c.parsed.y); },
                                    afterLabel: function (c) { return orders[c.dataIndex] + ' transaksi'; }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 10 } },
                            y: {
                                beginAtZero: true, border: { display: false }, grid: { color: '#ECEDE9' },
                                ticks: { callback: function (v) { return v >= 1e6 ? (v / 1e6).toLocaleString('id-ID') + ' jt' : (v >= 1e3 ? (v / 1e3).toLocaleString('id-ID') + ' rb' : v); } }
                            }
                        }
                    }
                });
            }

            /* Rincian order */
            var $modal = $('#orderDetailModal');
            $(document).on('click', '.view-details', function () {
                var url = $(this).data('url');
                $('#order-id').text($(this).data('id'));
                $('#order-loading').removeClass('d-none').html('<i class="fas fa-circle-notch fa-spin"></i> Memuat rincian…');
                $('#order-content').addClass('d-none');
                $('#btnPrint').prop('disabled', true);
                $modal.modal('show');

                $.getJSON(url).done(function (res) {
                    var o = res.order || {};
                    var rows = '', printRows = '';
                    var esc = function (s) { return $('<div>').text(s == null ? '' : s).html(); };

                    (res.items || []).forEach(function (it) {
                        rows += '<tr><td class="cell-main">' + esc(it.product_name) + '</td>' +
                            '<td class="t-right num">' + esc(it.quantity) + '</td>' +
                            '<td class="t-right num">' + fmt(it.price_raw) + '</td>' +
                            '<td class="t-right num font-weight-bold">' + fmt(it.total_raw) + '</td></tr>';
                        printRows += '<p>' + esc(it.product_name) + '</p>' +
                            '<div class="row-line"><span>' + esc(it.quantity) + ' x ' + fmt(it.price_raw) + '</span><span>' + fmt(it.total_raw) + '</span></div>';
                    });
                    if (!rows) rows = '<tr><td colspan="4" class="text-center text-muted">Tidak ada item pada order ini.</td></tr>';

                    $('#order-items').html(rows);
                    $('#customer-name').text(o.customer_name || 'Pelanggan umum');
                    $('#cashier-name').text(o.nama_kasir || '-');
                    $('#tx-time').text(res.transaction_label || o.transaction_time || '-');
                    $('#d-subtotal').text('Rp ' + fmt(o.sub_total));
                    $('#d-discount').text('− Rp ' + fmt(o.discount_amount));
                    $('#d-tax').text('Rp ' + fmt(o.tax));
                    $('#d-service').text('Rp ' + fmt(o.service_charge));
                    $('#total-bayar').text('Rp ' + fmt(o.total));
                    $('#d-paid').text('Rp ' + fmt(o.payment_amount));
                    $('#d-method').text(o.payment_method || '-');

                    $('#print-order-id').text(o.id);
                    $('#print-customer').text(o.customer_name || 'Umum');
                    $('#print-cashier').text(o.nama_kasir || '-');
                    $('#print-date').text(res.transaction_label || new Date().toLocaleString('id-ID'));
                    $('#print-items').html(printRows);
                    $('#print-subtotal').text(fmt(o.sub_total));
                    $('#print-discount').text(fmt(o.discount_amount));
                    $('#print-tax').text(fmt(o.tax));
                    $('#print-total').text(fmt(o.total));
                    $('#print-paid').text(fmt(o.payment_amount));

                    $('#order-loading').addClass('d-none');
                    $('#order-content').removeClass('d-none');
                    $('#btnPrint').prop('disabled', false);
                }).fail(function () {
                    $('#order-loading').html('<span class="text-red"><i class="fas fa-circle-exclamation"></i> Rincian gagal dimuat. Periksa koneksi lalu coba lagi.</span>');
                });
            });

            $('#btnPrint').on('click', function () { window.print(); });
        });
    </script>
@endpush
