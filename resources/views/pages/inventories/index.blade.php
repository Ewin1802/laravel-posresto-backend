@extends('layouts.app')

@section('title', 'Inventaris')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Persediaan Masuk</h1>
                <div class="section-header-button">
                    <a href="{{ route('inventories.create') }}" class="btn btn-primary">Tambah Transaksi Masuk</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Persediaan</a></div>
                    <div class="breadcrumb-item">Persediaan Masuk</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Transaksi Persediaan Masuk</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right">
                                    <form method="GET" action="{{ route('inventories.index') }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Cari transaksi..." name="search">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="clearfix mb-3"></div>
                                <div class="table-responsive">
                                    <table class="table-striped table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                {{-- <th>Jenis</th> --}}
                                                <th>Nama Bahan</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Harga Satuan</th>
                                                <th>Supplier</th>
                                                <th>Penerima</th>
                                                <th>Gambar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inventories as $inventory)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="{{ $inventory->quantity == 0 ? 'text-danger font-weight-bold' : '' }}">
                                                        {{ $inventory->nama_bahan }}
                                                    </td>
                                                    <td class="{{ $inventory->quantity == 0 ? 'text-danger font-weight-bold' : '' }}">
                                                        {{ $inventory->quantity }}
                                                    </td>
                                                    <td>{{ $inventory->satuan }}</td>
                                                    <td>Rp {{ number_format($inventory->amount, 0, ',', '.') }}</td>
                                                    <td>{{ $inventory->supplier ?? '-' }}</td>
                                                    <td>{{ $inventory->receiver ?? '-' }}</td>
                                                    <td>
                                                        @if ($inventory->image)
                                                            <img src="{{ asset('storage/' . $inventory->image) }}" width="50" height="50" alt="Bukti Transaksi">
                                                        @else
                                                            <span>Tidak ada gambar</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">

                                                            <a href="{{ route('inventories.edit', $inventory->id) }}" class="btn btn-sm btn-info btn-icon">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            {{-- <form action="{{ route('inventories.destroy', $inventory->id) }}" method="POST" class="ml-2">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-sm btn-danger btn-icon confirm-delete">
                                                                    <i class="fas fa-times"></i> Hapus
                                                                </button>
                                                            </form> --}}

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>

                                <div class="float-right">
                                    {{ $inventories->withQueryString()->links() }}
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
    <!-- JS Libraries -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmDeleteButtons = document.querySelectorAll('.confirm-delete');
            confirmDeleteButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    if (!confirm('Yakin ingin menghapus transaksi ini?')) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
