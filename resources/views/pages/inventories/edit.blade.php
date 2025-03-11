@extends('layouts.app')

@section('title', 'Edit Inventory')
{{-- Favicon --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Inventory</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Inventory</a></div>
                    <div class="breadcrumb-item">Edit</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Inventory Item</h2>

                <div class="card">
                    <form action="{{ route('inventories.update', $inventory) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h4>Inventory Details</h4>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Bahan</label>
                                <select id="bahan_id" class="form-control select2 @error('bahan_id') is-invalid @enderror" name="bahan_id" required>
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}" data-satuan="{{ $material->satuan }}"
                                            {{ $inventory->bahan_id == $material->id ? 'selected' : '' }}>
                                            {{ $material->nama_bahan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bahan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Satuan</label>
                                <input type="text" id="satuan" class="form-control" name="satuan" value="{{ $inventory->bahan->satuan }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ $inventory->quantity }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Harga Barang</label>
                                <input type="text" id="amount_display" class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ number_format($inventory->amount, 0, ',', '.') }}"
                                       oninput="formatAmount(this)">
                                <input type="hidden" id="amount_input" name="amount" value="{{ $inventory->amount }}">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Supplier</label>
                                <input type="text" class="form-control @error('supplier') is-invalid @enderror" name="supplier" value="{{ $inventory->supplier }}">
                                @error('supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Penerima</label>
                                <input type="text" class="form-control @error('receiver') is-invalid @enderror" name="receiver" value="{{ $inventory->receiver }}">
                                @error('receiver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description">{{ $inventory->description }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Foto Transaksi (Opsional)</label>
                                @if($inventory->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $inventory->image) }}" alt="Bukti Transaksi" width="100">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('library/select2/dist/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let $materialSelect = $('#bahan_id');

        // Inisialisasi Select2
        $materialSelect.select2();

        // Set satuan otomatis berdasarkan bahan yang dipilih
        $materialSelect.on('change', function() {
            let satuan = $(this).find(':selected').data('satuan') || '';
            $('#satuan').val(satuan);
        });

        // Set satuan saat pertama kali halaman dimuat
        let selectedMaterial = $materialSelect.find(':selected');
        if (selectedMaterial.length > 0) {
            $('#satuan').val(selectedMaterial.data('satuan'));
        }
    });

    // Format harga dengan titik pemisah ribuan
    function formatAmount(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
        document.getElementById('amount_input').value = value;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const amountDisplay = document.getElementById('amount_display');
        const amountInput = document.getElementById('amount_input');

        if (amountInput.value) {
            amountDisplay.value = new Intl.NumberFormat('id-ID').format(amountInput.value);
        }
    });
</script>
@endpush
