@push('modals')
    <div class="modal fade" id="modalTambahBahan" tabindex="-1" role="dialog" aria-labelledby="modalTambahBahanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="formTambahBahan" class="modal-content" data-no-lock novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahBahanLabel">Bahan baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_bahan_baru">Nama bahan</label>
                        <input type="text" class="form-control" id="nama_bahan_baru" placeholder="Contoh: Susu UHT" required>
                    </div>
                    <div class="form-group mb-0">
                        <label for="satuan_bahan_baru">Satuan</label>
                        <input type="text" class="form-control" id="satuan_bahan_baru" list="satuan-list-modal" placeholder="Kg, Liter, Pcs…" required>
                        <datalist id="satuan-list-modal">
                            <option value="Kg"><option value="Gram"><option value="Liter"><option value="Ml"><option value="Pcs"><option value="Pack"><option value="Botol"><option value="Dus">
                        </datalist>
                    </div>
                    <div class="flash flash--error mt-3 mb-0 d-none" id="bahanError"><i class="fas fa-circle-exclamation"></i><div class="flash__body"></div></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanBahan">Simpan bahan</button>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(function () {
            var $select = $('#bahan_id');
            if ($.fn.select2) {
                $select.select2({ placeholder: 'Pilih atau ketik nama bahan', width: '100%' });
            }

            var syncSatuan = function () {
                var satuan = $select.find(':selected').data('satuan') || 'satuan';
                $('[data-satuan-label]').text(satuan);
            };
            $select.on('change', syncSatuan);
            syncSatuan();

            // Total belanja = jumlah × harga
            var updateTotal = function () {
                var qty = parseInt($('#quantity').val() || 0, 10);
                var price = parseInt($('#amount_input').val() || 0, 10);
                $('#totalBelanja').text(ARCH.number(qty * price));
            };
            $('#quantity, #amount_display').on('input', updateTotal);
            updateTotal();

            // Bahan baru via AJAX (endpoint lama bahans.store, respons JSON)
            var lettersOnly = function () { this.value = this.value.replace(/[^a-zA-Z\s]/g, ''); };
            $('#nama_bahan_baru, #satuan_bahan_baru').on('input', lettersOnly);
            $('#modalTambahBahan').on('shown.bs.modal', function () { $('#nama_bahan_baru').trigger('focus'); });

            $('#formTambahBahan').on('submit', function (e) {
                e.preventDefault();
                var nama = $.trim($('#nama_bahan_baru').val());
                var satuan = $.trim($('#satuan_bahan_baru').val());
                var $err = $('#bahanError');
                var showErr = function (msg) { $err.removeClass('d-none').find('.flash__body').text(msg); };
                $err.addClass('d-none');

                if (!nama || !satuan) { showErr('Nama bahan dan satuan wajib diisi, hanya huruf.'); return; }

                var $btn = $('#btnSimpanBahan').prop('disabled', true).addClass('is-loading');
                $.ajax({
                    url: "{{ route('bahans.store') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: { nama_bahan: nama, satuan: satuan },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                }).done(function (res) {
                    var label = res.nama_bahan + ' (' + res.satuan + ')';
                    var opt = new Option(label, res.id, true, true);
                    $(opt).attr('data-satuan', res.satuan);
                    $select.append(opt).trigger('change');
                    $('#nama_bahan_baru, #satuan_bahan_baru').val('');
                    $('#modalTambahBahan').modal('hide');
                }).fail(function (xhr) {
                    var r = xhr.responseJSON || {};
                    showErr(r.error || r.message || 'Bahan gagal disimpan. Coba lagi.');
                }).always(function () {
                    $btn.prop('disabled', false).removeClass('is-loading');
                });
            });
        });
    </script>
@endpush
