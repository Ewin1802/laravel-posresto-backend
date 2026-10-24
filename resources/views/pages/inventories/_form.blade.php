@php
    $editing = isset($inventory);
    $selectedBahan = old('bahan_id', $inventory->bahan_id ?? '');
    $amountRaw = old('amount', $editing ? (int) $inventory->amount : '');
@endphp

<div class="form-section">
    <h2 class="form-section__title">Bahan & jumlah</h2>
    <p class="form-section__lead">Satuan mengikuti master bahan dan terisi otomatis.</p>

    <div class="form-group">
        <label for="bahan_id">Nama bahan</label>
        <div class="input-group">
            <select id="bahan_id" name="bahan_id" class="form-control @error('bahan_id') is-invalid @enderror" required>
                <option value="">Pilih atau ketik nama bahan</option>
                @foreach ($materials as $material)
                    <option value="{{ $material->id }}" data-satuan="{{ $material->satuan }}" @selected((string) $selectedBahan === (string) $material->id)>
                        {{ $material->nama_bahan }} ({{ $material->satuan }})
                    </option>
                @endforeach
            </select>
            <div class="input-group-append">
                <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#modalTambahBahan">
                    <i class="fas fa-plus"></i> Bahan baru
                </button>
            </div>
        </div>
        @error('bahan_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        @if ($editing && !$inventory->bahan)
            <span class="hint text-red">Bahan asli batch ini sudah dihapus dari master. Pilih bahan pengganti.</span>
        @endif
    </div>

    <div class="row">
        @if ($editing)
            <div class="col-sm-4 form-group">
                <label for="saldo_awal">Jumlah dibeli</label>
                <div class="input-group">
                    <input id="saldo_awal" type="number" min="0" name="saldo_awal" value="{{ old('saldo_awal', $inventory->saldo_awal) }}"
                        class="form-control num @error('saldo_awal') is-invalid @enderror">
                    <div class="input-group-append"><span class="input-group-text" data-satuan-label>{{ $inventory->satuan }}</span></div>
                </div>
                @error('saldo_awal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-sm-4 form-group">
                <label for="quantity">Sisa stok</label>
                <div class="input-group">
                    <input id="quantity" type="number" min="0" name="quantity" value="{{ old('quantity', $inventory->quantity) }}"
                        class="form-control num @error('quantity') is-invalid @enderror" required>
                    <div class="input-group-append"><span class="input-group-text" data-satuan-label>{{ $inventory->satuan }}</span></div>
                </div>
                <span class="hint">Sudah keluar {{ (int) ($inventory->total_keluar ?? 0) }} {{ $inventory->satuan }} lewat persediaan keluar.</span>
                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        @else
            <div class="col-sm-4 form-group">
                <label for="quantity">Jumlah dibeli</label>
                <div class="input-group">
                    <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity') }}"
                        class="form-control num @error('quantity') is-invalid @enderror" placeholder="0" required>
                    <div class="input-group-append"><span class="input-group-text" data-satuan-label>satuan</span></div>
                </div>
                @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        @endif

        <div class="col-sm-4 form-group">
            <label for="amount_display">Harga per satuan</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                <input id="amount_display" type="text" inputmode="numeric" data-rupiah="#amount_input"
                    class="form-control num @error('amount') is-invalid @enderror" placeholder="0" autocomplete="off">
            </div>
            <input type="hidden" id="amount_input" name="amount" value="{{ $amountRaw }}">
            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        @unless ($editing)
            <div class="col-sm-4 form-group">
                <span class="form-label d-block">Total belanja</span>
                <div class="stat__value" style="margin-top:14px; font-size:26px"><small>Rp</small><span id="totalBelanja">0</span></div>
            </div>
        @endunless
    </div>
</div>

<div class="form-section">
    <h2 class="form-section__title">Detail pembelian <span class="text-muted font-weight-normal">(opsional)</span></h2>
    <p class="form-section__lead">Membantu menelusuri asal bahan bila ada selisih stok.</p>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="supplier">Supplier</label>
            <input id="supplier" type="text" name="supplier" value="{{ old('supplier', $inventory->supplier ?? '') }}" class="form-control" placeholder="Nama toko atau pemasok">
        </div>
        <div class="col-md-6 form-group">
            <label for="receiver">Diterima oleh</label>
            <input id="receiver" type="text" name="receiver" value="{{ old('receiver', $inventory->receiver ?? '') }}" class="form-control" placeholder="Nama staf penerima">
        </div>
        <div class="col-md-6 form-group">
            <label for="description">Keterangan</label>
            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $inventory->description ?? '') }}</textarea>
        </div>
        <div class="col-md-6 form-group">
            <span class="form-label d-block">Bukti transaksi</span>
            <label class="dropzone @error('image') is-invalid @enderror" for="image">
                <input id="image" type="file" name="image" accept="image/*">
                <span class="dropzone__preview {{ $editing && $inventory->image ? 'has-image' : '' }}"
                    @if ($editing && $inventory->image) style="background-image:url('{{ asset('storage/' . $inventory->image) }}')" @endif>
                    <i class="fas fa-receipt"></i>
                </span>
                <span class="dropzone__text">
                    <strong>{{ $editing && $inventory->image ? 'Ganti foto struk' : 'Foto struk atau nota' }}</strong>
                    <span data-file-name>Gambar · maks. 2 MB</span>
                </span>
            </label>
            @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
