<div class="form-section">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label for="name">Nama diskon</label>
                <input id="name" type="text" name="name" value="{{ old('name', $discount->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Member ARCH" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group mb-md-0">
                <label for="description">Keterangan</label>
                <textarea id="description" name="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Kapan diskon ini diberikan" required>{{ old('description', $discount->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-4 form-group">
            <label for="value">Besar potongan</label>
            <div class="input-group">
                <input id="value" type="number" name="value" min="0" max="100" step="0.01"
                    value="{{ old('value', isset($discount) ? (string) (float) $discount->value : '') }}"
                    class="form-control num @error('value') is-invalid @enderror" placeholder="10" required>
                <div class="input-group-append"><span class="input-group-text">%</span></div>
            </div>
            <span class="hint">Isi 10 untuk potongan 10% dari subtotal.</span>
            @error('value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
