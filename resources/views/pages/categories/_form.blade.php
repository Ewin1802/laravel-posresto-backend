@php $editing = isset($category); @endphp

<div class="form-section">
    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                <label for="name">Nama kategori</label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Kopi susu" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group mb-md-0">
                <label for="description">Deskripsi <span class="text-muted font-weight-normal">(opsional)</span></label>
                <textarea id="description" name="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Ringkasan singkat isi kategori">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-5">
            <span class="form-label d-block">Foto kategori</span>
            <label class="dropzone @error('image') is-invalid @enderror" for="image" style="flex-direction:column; align-items:stretch; text-align:center">
                <input id="image" type="file" name="image" accept="image/*" {{ $editing ? '' : 'required' }}>
                <span class="dropzone__preview {{ $editing && $category->image ? 'has-image' : '' }}"
                    style="width:100%; height:160px; flex:none; @if ($editing && $category->image) background-image:url('{{ asset($category->image) }}') @endif">
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
