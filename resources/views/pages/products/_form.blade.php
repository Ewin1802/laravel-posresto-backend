@php
    $editing = isset($product);
    $status = (string) old('status', $editing ? (int) $product->status : 1);
    $fav = (string) old('is_favorite', $editing ? (int) $product->is_favorite : 1);
    $priceRaw = old('price', $editing ? (int) $product->price : '');
@endphp

<div class="form-section">
    <h2 class="form-section__title">Informasi menu</h2>
    <p class="form-section__lead">Nama dan harga tampil di aplikasi kasir serta struk.</p>
    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                <label for="name">Nama produk</label>
                <input id="name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Kopi susu gula aren" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Bahan utama atau penjelasan singkat" required>{{ old('description', $product->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label for="price_display">Harga jual</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input id="price_display" type="text" inputmode="numeric" data-rupiah="#price_input"
                            class="form-control num @error('price') is-invalid @enderror" placeholder="0" autocomplete="off">
                    </div>
                    <input type="hidden" id="price_input" name="price" value="{{ $priceRaw }}">
                    @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-sm-6 form-group">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="custom-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <span class="form-label d-block">Foto produk</span>
            <label class="dropzone @error('image') is-invalid @enderror" for="image" style="flex-direction:column; align-items:stretch; text-align:center">
                <input id="image" type="file" name="image" accept="image/png,image/jpeg,image/webp" {{ $editing ? '' : 'required' }}>
                <span class="dropzone__preview {{ $editing && $product->image ? 'has-image' : '' }}"
                    style="width:100%; height:200px; flex:none; @if ($editing && $product->image) background-image:url('{{ asset($product->image) }}') @endif">
                    <i class="fas fa-image"></i>
                </span>
                <span class="dropzone__text">
                    <strong>{{ $editing ? 'Ganti foto' : 'Pilih atau seret foto' }}</strong>
                    <span data-file-name>JPG, PNG, WEBP · maks. 2 MB</span>
                </span>
            </label>
            @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <h2 class="form-section__title">Pengaturan</h2>
    <p class="form-section__lead">Produk nonaktif disembunyikan dari halaman publik. Produk favorit tampil lebih dulu.</p>
    <div class="row">
        <div class="col-sm-4 form-group">
            <label for="stock">Stok</label>
            <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 100) }}"
                class="form-control num @error('stock') is-invalid @enderror" required>
            <span class="hint">Hanya angka penanda; tidak memengaruhi persediaan bahan.</span>
            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-4 form-group">
            <span class="form-label d-block">Status</span>
            <div class="segmented" role="radiogroup" aria-label="Status">
                <label><input type="radio" name="status" value="1" @checked($status === '1')><span>Aktif</span></label>
                <label><input type="radio" name="status" value="0" @checked($status === '0')><span>Nonaktif</span></label>
            </div>
            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-4 form-group">
            <span class="form-label d-block">Favorit</span>
            <div class="segmented" role="radiogroup" aria-label="Favorit">
                <label><input type="radio" name="is_favorite" value="1" @checked($fav === '1')><span><i class="fas fa-star text-gold"></i> Ya</span></label>
                <label><input type="radio" name="is_favorite" value="0" @checked($fav === '0')><span>Tidak</span></label>
            </div>
            @error('is_favorite') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
