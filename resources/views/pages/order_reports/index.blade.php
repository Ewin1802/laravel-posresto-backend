@extends('layouts.app')

@section('title', 'Order Report')
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">

    <style>
        /* === PRINT STYLE === */
        .struk {
            width: 58mm;
            font-family: monospace;
            font-size: 12px;
        }

        .struk p {
            margin: 2px 0;
        }

        .struk hr {
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm;
            }
        }
    </style>
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

                <!-- Filter -->
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('order_reports.index') }}">
                            <input type="date" name="start_date" value="{{ $start_date }}">
                            <input type="date" name="end_date" value="{{ $end_date }}">
                            <button class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ number_format($order->payment_amount, 2) }}</td>
                                        <td>
                                            <button class="btn btn-info view-details" data-id="{{ $order->id }}">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

{{-- MODAL --}}
<div class="modal fade" id="orderDetailModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Detail Pesanan</h5>
            </div>

            <div class="modal-body">
                <p>Order ID: <span id="order-id"></span></p>
                <p>Customer: <span id="customer-name"></span></p>

                <table class="table">
                    <tbody id="order-items"></tbody>
                </table>

                <p>Total: <span id="total-bayar"></span></p>
            </div>

            <div class="modal-footer">
                <!-- === PRINT BUTTON === -->
                <button class="btn btn-success" onclick="printStruk()">🖨️ Print</button>
            </div>

        </div>
    </div>
</div>


<!-- === PRINT AREA === -->
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


@push('scripts')
    <script>
        $(document).ready(function() {

            $('.view-details').click(function() {

                let orderId = $(this).data('id');

                $('#order-items').html('');
                $('#customer-name').text('');
                $('#total-bayar').text('');

                $.ajax({
                    url: `/orders/${orderId}`,
                    type: 'GET',

                    success: function(response) {

                        let total = 0;

                        // TABLE NORMAL
                        response.items.forEach(item => {

                            let t = parseFloat(item.total.replace(/,/g, ''));
                            total += t;

                            $('#order-items').append(`
<tr>
<td>${item.product_name}</td>
<td>${item.quantity}</td>
<td>${item.price}</td>
<td>${item.total}</td>
</tr>
`);
                        });

                        // SET MODAL
                        $('#order-id').text(orderId);
                        $('#customer-name').text(response.order.customer_name);
                        $('#total-bayar').text(total.toLocaleString('id-ID'));


                        // === PRINT DATA ===
                        $('#print-order-id').text(orderId);
                        $('#print-customer').text(response.order.customer_name);

                        let itemsPrint = '';

                        response.items.forEach(item => {
                            itemsPrint += `
<p>${item.product_name}</p>
<p>${item.quantity} x ${item.price} = ${item.total}</p>
`;
                        });

                        $('#print-items').html(itemsPrint);
                        $('#print-total').text(total.toLocaleString('id-ID'));

                        let now = new Date();
                        $('#print-date').text(now.toLocaleString('id-ID'));

                        $('#orderDetailModal').modal('show');

                    }

                });

            });

        });
    </script>


    <!-- === PRINT FUNCTION === -->
    <script>
        function printStruk() {
            window.print();
        }
    </script>
@endpush
