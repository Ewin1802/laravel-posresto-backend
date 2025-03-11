@extends('layouts.app')

@section('title', 'Tambah Transaksi Keluar')

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
            <h1>Input Persediaan Keluar</h1>
        </div>

        <div class="section-body">
            @include('layouts.alert')

            <form id="formInventoryOut" action="{{ route('inventory_out.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nama Bahan</label>
                    <select id="inventorySelect" name="inventory_id" class="form-control select2" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach ($inventories as $inventory)
                            <option value="{{ $inventory->id }}"
                                data-stok="{{ $inventory->quantity }}"
                                data-satuan="{{ $inventory->satuan }}"
                                data-harga="{{ $inventory->amount }}">
                                {{ $inventory->nama_bahan }}
                                (Stok: {{ $inventory->quantity }} {{ $inventory->satuan }} |
                                Harga satuan: Rp{{ number_format($inventory->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <small id="stokInfo" class="text-muted"></small>
                </div>

                <div class="form-group">
                    <label>Jumlah Keluar</label>
                    <input type="number" id="quantityOut" name="quantity_out" class="form-control" required>
                    <small id="stokWarning" class="text-danger d-none">Jumlah keluar melebihi stok yang tersedia!</small>
                </div>

                <div class="form-group">
                    <label>Penerima</label>
                    <input type="text" name="receiver" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let inventorySelect = document.getElementById("inventorySelect");
        let quantityOut = document.getElementById("quantityOut");
        let stokWarning = document.getElementById("stokWarning");
        let stokInfo = document.getElementById("stokInfo");
        let submitBtn = document.getElementById("submitBtn");

        // Saat bahan dipilih, tampilkan stoknya
        inventorySelect.addEventListener("change", function () {
            let selectedOption = this.options[this.selectedIndex];
            let stok = selectedOption.getAttribute("data-stok") || 0;
            let satuan = selectedOption.getAttribute("data-satuan") || "";
            stokInfo.textContent = `Stok tersedia: ${stok} ${satuan}`;
        });

        // Saat jumlah keluar diinput, periksa stok
        quantityOut.addEventListener("input", function () {
            let selectedOption = inventorySelect.options[inventorySelect.selectedIndex];
            let stok = parseInt(selectedOption.getAttribute("data-stok") || 0);
            let jumlahKeluar = parseInt(this.value);

            if (jumlahKeluar > stok) {
                stokWarning.classList.remove("d-none");
                submitBtn.disabled = true; // Nonaktifkan tombol submit
            } else {
                stokWarning.classList.add("d-none");
                submitBtn.disabled = false; // Aktifkan tombol submit
            }
        });
    });
</script>
@endpush
