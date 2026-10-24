@extends('layouts.app')

@section('title', 'Catat bahan keluar')
@section('crumb')
    <a href="{{ route('inventory_out.index') }}">Persediaan keluar</a>
@endsection

@section('main')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Catat bahan keluar</h1>
            <p class="page-head__lead">Stok batch yang dipilih otomatis berkurang setelah disimpan.</p>
        </div>
    </div>

    @include('layouts.alert')

    @if ($inventories->isEmpty())
        <section class="panel">
            <div class="empty">
                <div class="empty__icon"><i class="fas fa-box-open"></i></div>
                <h3>Tidak ada stok yang bisa dikeluarkan</h3>
                <p>Semua batch persediaan sudah habis. Catat pembelian bahan terlebih dahulu.</p>
                <a href="{{ route('inventories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pembelian</a>
            </div>
        </section>
    @else
        <div class="row">
            <div class="col-xl-9">
                <form id="formInventoryOut" action="{{ route('inventory_out.store') }}" method="POST" class="panel" novalidate>
                    @csrf
                    <div class="form-section">
                        <div class="form-group">
                            <label for="inventorySelect">Bahan dan batch</label>
                            <select id="inventorySelect" name="inventory_id" class="form-control @error('inventory_id') is-invalid @enderror" required>
                                <option value="">Pilih bahan</option>
                                @foreach ($inventories as $inventory)
                                    <option value="{{ $inventory->id }}"
                                        data-stok="{{ $inventory->quantity }}"
                                        data-satuan="{{ $inventory->satuan }}"
                                        data-harga="{{ (int) $inventory->amount }}"
                                        @selected((string) old('inventory_id') === (string) $inventory->id)>
                                        {{ $inventory->nama_bahan }} — sisa {{ $inventory->quantity }} {{ $inventory->satuan }}, Rp {{ number_format($inventory->amount, 0, ',', '.') }}/{{ $inventory->satuan }}{{ $inventory->tanggal_masuk ? ', masuk ' . \Carbon\Carbon::parse($inventory->tanggal_masuk)->translatedFormat('d M') : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inventory_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label for="quantityOut">Jumlah keluar</label>
                                <div class="input-group">
                                    <input type="number" id="quantityOut" name="quantity_out" min="1" value="{{ old('quantity_out') }}"
                                        class="form-control num @error('quantity_out') is-invalid @enderror" required>
                                    <div class="input-group-append"><span class="input-group-text" id="satuanLabel">satuan</span></div>
                                </div>
                                <span class="hint" id="stokInfo">Pilih bahan untuk melihat stok.</span>
                                <span class="hint text-red d-none" id="stokWarning">Jumlah melebihi stok tersedia.</span>
                                @error('quantity_out') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-4 form-group">
                                <label for="receiver">Penerima</label>
                                <input type="text" id="receiver" name="receiver" value="{{ old('receiver') }}"
                                    class="form-control @error('receiver') is-invalid @enderror" placeholder="Contoh: Dapur, Barista" required>
                                @error('receiver') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-4 form-group">
                                <span class="form-label d-block">Nilai bahan keluar</span>
                                <div class="stat__value" style="margin-top:14px; font-size:26px"><small>Rp</small><span id="nilaiKeluar">0</span></div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="description">Keterangan <span class="text-muted font-weight-normal">(opsional)</span></label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="panel__foot">
                        <a href="{{ route('inventory_out.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan transaksi keluar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(function () {
            var $select = $('#inventorySelect');
            if (!$select.length) return;
            if ($.fn.select2) $select.select2({ placeholder: 'Pilih bahan', width: '100%' });

            var $qty = $('#quantityOut');
            var check = function () {
                var $opt = $select.find(':selected');
                var stok = parseInt($opt.data('stok') || 0, 10);
                var satuan = $opt.data('satuan') || 'satuan';
                var harga = parseInt($opt.data('harga') || 0, 10);
                var qty = parseInt($qty.val() || 0, 10);

                $('#satuanLabel').text(satuan);
                $('#stokInfo').text($opt.val() ? 'Stok tersedia ' + ARCH.number(stok) + ' ' + satuan + '.' : 'Pilih bahan untuk melihat stok.');
                $('#nilaiKeluar').text(ARCH.number(qty * harga));

                var over = $opt.val() && qty > stok;
                $('#stokWarning').toggleClass('d-none', !over);
                $qty.toggleClass('is-invalid', !!over);
                $('#submitBtn').prop('disabled', !!over);
            };
            $select.on('change', check);
            $qty.on('input', check);
            check();
        });
    </script>
@endpush
