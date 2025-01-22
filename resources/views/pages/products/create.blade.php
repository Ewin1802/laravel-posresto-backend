@extends('layouts.app')

@section('title', 'Product Create')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">
@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Form Input Produk</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Forms</a></div>
                    <div class="breadcrumb-item">Form Input Produk</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Produk</h2>

                <div class="card">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-header">
                            <h4>Input Text</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text"
                                    class="form-control @error('name')
                                is-invalid
                            @enderror"
                                    name="name">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Deskripsi Produk</label>
                                <input type="text"
                                    class="form-control @error('description')
                                is-invalid
                            @enderror"
                                    name="description">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Harga</label>
                                <input type="text" id="price_display" class="form-control @error('price') is-invalid @enderror" placeholder="Enter price" oninput="formatPrice(this)" />
                                <input type="hidden" id="price_input" name="price"> {{-- Hidden input untuk nilai asli --}}

                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Stok (Isi Angka Berapa saja, tidak berpengaruh di tabel lain) </label>
                                <input type="number"
                                    class="form-control @error('stock')
                                is-invalid
                            @enderror"
                                    name="stock">
                                @error('stock')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kategori Produk</label>
                                <select class="form-control selectric @error('category_id') is-invalid @enderror"
                                    name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Load Foto Produk</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image">
                                </div>
                                @error('image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label class="form-label">Status (Tidak perlu diubah)</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="status" value="1" class="selectgroup-input"
                                            checked="">
                                        <span class="selectgroup-button">Active</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="status" value="0" class="selectgroup-input">
                                        <span class="selectgroup-button">Inactive</span>
                                    </label>
                                </div>
                            </div>

                            {{-- is favorite --}}
                            <div class="form-group">
                                <label class="form-label">Is Favorite (Tidak perlu diubah)</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_favorite" value="1" class="selectgroup-input"
                                            checked="">
                                        <span class="selectgroup-button">Yes</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="is_favorite" value="0" class="selectgroup-input">
                                        <span class="selectgroup-button">No</span>
                                    </label>
                                </div>
                            </div>


                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    function formatPrice(input) {
        // Ambil nilai asli tanpa format
        let value = input.value.replace(/[^0-9]/g, '');

        // Format nilai dengan pemisah ribuan
        let formatted = new Intl.NumberFormat('id-ID').format(value);

        // Update tampilan input dengan nilai terformat
        input.value = formatted;

        // Simpan nilai asli (tanpa format) ke hidden input
        document.getElementById('price_input').value = value;
    }

    document.querySelector('form').addEventListener('submit', function(event) {
        const requiredFields = ['name', 'description', 'price_input', 'stock', 'category_id'];
        let isValid = true;

        // Validasi input teks dan select
        requiredFields.forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input || !input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Validasi gambar
        const imageInput = document.querySelector('[name="image"]');
        if (!imageInput.files || imageInput.files.length === 0) {
            event.preventDefault();
            imageInput.classList.add('is-invalid');
            alert('Please upload an image before submitting.');
        } else {
            imageInput.classList.remove('is-invalid');
        }

        // Validasi kategori (tidak boleh kosong)
        const categorySelect = document.querySelector('[name="category_id"]');
        if (categorySelect && categorySelect.value === '') {
            isValid = false;
            categorySelect.classList.add('is-invalid');
        } else {
            categorySelect.classList.remove('is-invalid');
        }

        // Jika tidak valid, cegah pengiriman form
        if (!isValid) {
            event.preventDefault();
            alert('Please fill in all required fields, including the image, before submitting.');
        }
    });
</script>
@endpush

