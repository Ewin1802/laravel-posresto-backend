@php
    $editing = isset($expense);
    $catNow = old('category', $expense->category ?? $defaultCategory);
    $dateNow = old('expense_date', $editing ? $expense->expense_date->toDateString() : now()->toDateString());
    $amountRaw = old('amount', $editing ? (int) $expense->amount : '');
@endphp

<div class="form-section">
    <h2 class="form-section__title">Kategori</h2>
    <p class="form-section__lead">Dipakai untuk mengelompokkan biaya di laporan.</p>
    <div class="cat-grid" role="radiogroup" aria-label="Kategori pengeluaran">
        @foreach ($categories as $key => $c)
            <label class="cat-tile">
                <input type="radio" name="category" value="{{ $key }}" data-hint="{{ $c['hint'] }}" @checked($catNow === $key) required>
                <span><i class="fas {{ $c['icon'] }}"></i>{{ $c['label'] }}</span>
            </label>
        @endforeach
    </div>
    @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    <div class="cat-hint" aria-live="polite">
        <i class="fas fa-lightbulb"></i>
        <span><b data-cat-label>{{ $categories[$catNow]["label"] ?? "" }}</b>. <span data-cat-hint>{{ $categories[$catNow]['hint'] ?? '' }}</span></span>
    </div>
</div>

<div class="form-section">
    <div class="row">
        <div class="col-md-4 form-group">
            <label for="amount_display">Jumlah</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                <input id="amount_display" type="text" inputmode="numeric" data-rupiah="#amount_input"
                    class="form-control num @error('amount') is-invalid @enderror" placeholder="0" autocomplete="off" autofocus>
            </div>
            <input type="hidden" id="amount_input" name="amount" value="{{ $amountRaw }}">
            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 form-group">
            <label for="expense_date">Tanggal</label>
            <input id="expense_date" type="date" name="expense_date" value="{{ $dateNow }}" max="{{ now()->addDay()->toDateString() }}"
                class="form-control @error('expense_date') is-invalid @enderror" required>
            @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4 form-group">
            <span class="form-label d-block">Cepat</span>
            <div class="chips">
                <button type="button" class="chip" data-date="{{ now()->toDateString() }}">Hari ini</button>
                <button type="button" class="chip" data-date="{{ now()->subDay()->toDateString() }}">Kemarin</button>
            </div>
        </div>
        <div class="col-12 form-group">
            <label for="description">Keterangan</label>
            <input id="description" type="text" name="description" maxlength="255" value="{{ old('description', $expense->description ?? '') }}"
                class="form-control @error('description') is-invalid @enderror" placeholder="Contoh: Susu UHT 2 dus, gaji barista minggu ke-3" required>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 form-group mb-0">
            <label for="notes">Catatan <span class="text-muted font-weight-normal">(opsional)</span></label>
            <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                placeholder="Nama toko, nomor nota, atau penjelasan tambahan">{{ old('notes', $expense->notes ?? '') }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            $('[data-date]').on('click', function () { $('#expense_date').val($(this).data('date')); });
            $('input[name="category"]').on('change', function () {
                $('[data-cat-hint]').text($(this).data('hint'));
                $('[data-cat-label]').text($.trim($(this).next('span').text()));
            });
        });
    </script>
@endpush
