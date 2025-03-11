@extends('layouts.app')

@section('title', 'Tambah Transaksi Inventaris')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Input Persediaan Masuk</h1>
            </div>

            <div class="section-body">

                <div class="card">
                    <form action="{{ route('inventories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">

                        {{-- <div class="card-header">
                            <h4>Input Transaksi</h4>
                        </div> --}}
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nama Bahan</label>
                                <div class="input-group">
                                    <select id="bahan_id" class="form-control select2" name="bahan_id" required>
                                        <option value="">-- Pilih / Ketik Nama Barang --</option>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}" data-satuan="{{ $material->satuan }}">{{ $material->nama_bahan }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahBahan">
                                            <i class="fas fa-plus"></i> Tambah Bahan
                                        </button>
                                    </div>
                                </div>
                                @error('bahan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Satuan</label>
                                <input type="text" id="satuan" class="form-control" name="satuan" readonly>
                            </div>

                            <div class="form-group">
                                <label>Banyak Bahan</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                       name="quantity" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" id="saldo_awal" name="saldo_awal">

                            <div class="form-group">
                                <label>Harga per satuan Bahan</label>
                                <input type="text" id="amount_display" class="form-control @error('amount') is-invalid @enderror"
                                       placeholder="Masukkan Harga Per Satuan Bahan" oninput="formatPrice(this)">
                                <input type="hidden" id="amount_input" name="amount">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Supplier (Opsional)</label>
                                <input type="text" class="form-control" name="supplier">
                            </div>

                            <div class="form-group">
                                <label>Penerima (Opsional)</label>
                                <input type="text" class="form-control" name="receiver">
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="description"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Upload Bukti Transaksi/Struk/Foto Bahan (Opsional)</label>
                                <input type="file" class="form-control" name="image">
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

<!-- Modal Tambah Bahan -->
<div class="modal fade" id="modalTambahBahan" tabindex="-1" role="dialog" aria-labelledby="modalTambahBahanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBahanLabel">Tambah Bahan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahBahan">
                    <div class="form-group">
                        <label for="nama_bahan">Nama Bahan</label>
                        <input type="text" class="form-control" id="nama_bahan" name="nama_bahan" required>
                    </div>
                    <div class="form-group">
                        <label for="satuan_bahan">Satuan</label>
                        <input type="text" class="form-control" id="satuan_bahan" name="satuan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>


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

        // Validasi input Nama Bahan (hanya huruf)
        $('#nama_bahan').on('input', function() {
            let value = $(this).val();
            let sanitizedValue = value.replace(/[^a-zA-Z\s]/g, ''); // Hanya huruf dan spasi yang diperbolehkan
            $(this).val(sanitizedValue);
        });

        // Validasi input Satuan (opsional, jika diperlukan)
        $('#satuan_bahan').on('input', function() {
            let value = $(this).val();
            let sanitizedValue = value.replace(/[^a-zA-Z\s]/g, ''); // Hanya huruf dan spasi
            $(this).val(sanitizedValue);
        });

        // Event submit form Tambah Bahan
        $('#formTambahBahan').on('submit', function(e) {
            e.preventDefault();
            let namaBahan = $('#nama_bahan').val().trim();
            let satuanBahan = $('#satuan_bahan').val().trim();

            // Cek apakah input valid (tidak kosong dan hanya huruf)
            if (!namaBahan.match(/^[a-zA-Z\s]+$/)) {
                alert("Nama Bahan hanya boleh berisi huruf!");
                return;
            }

            if (!satuanBahan.match(/^[a-zA-Z\s]+$/)) {
                alert("Satuan hanya boleh berisi huruf!");
                return;
            }

            $.ajax({
                url: "{{ route('bahans.store') }}",
                type: "POST",
                data: {
                    nama_bahan: namaBahan,
                    satuan: satuanBahan
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    let newOption = new Option(response.nama_bahan, response.id, true, true);
                    $('#bahan_id').append(newOption).trigger('change');
                    $('#satuan').val(response.satuan);

                    // Reset form & tutup modal
                    $('#nama_bahan').val('');
                    $('#satuan_bahan').val('');
                    $('#modalTambahBahan').modal('hide');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert("Gagal menyimpan bahan: " + (xhr.responseJSON?.error || "Terjadi kesalahan."));
                }
            });
        });
    });

    // Format harga dengan titik pemisah ribuan
    function formatPrice(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
        document.getElementById('amount_input').value = value;
    }
</script>


@endpush
