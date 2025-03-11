@extends('layouts.app')

@section('title', 'Inventaris Keluar')
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
            <h1>Persediaan Keluar</h1>

            <div class="section-header-button">
                <a href="{{ route('inventory_out.create') }}" class="btn btn-primary">Tambah Transaksi Keluar</a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Persediaan</a></div>
                <div class="breadcrumb-item">Persediaan Keluar</div>
            </div>
        </div>

        <div class="section-body">
            @include('layouts.alert')

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Bahan</th>
                        <th>Jumlah Keluar</th>
                        <th>Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                        <th>Penerima</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventory_outs as $out)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $out->inventory->nama_bahan }}</td>
                            <td>{{ $out->quantity_out }}</td>
                            <td>{{ $out->inventory->satuan }}</td>
                            <td>Rp{{ number_format($out->inventory->amount, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($out->quantity_out * $out->inventory->amount, 0, ',', '.') }}</td>
                            <td>{{ $out->receiver }}</td>
                            <td>{{ $out->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $inventory_outs->links() }}
        </div>
    </section>
</div>
@endsection
