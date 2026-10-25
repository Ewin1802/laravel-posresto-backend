{{-- Panduan halaman Pengeluaran: panel samping. Dibuka lewat [data-guide-open="expenses"]. --}}
@push('modals')
    <div class="guide" id="guide-expenses" data-guide="expenses" role="dialog" aria-modal="true" aria-labelledby="guideTitle" hidden>
        <div class="guide__backdrop" data-guide-close></div>
        <aside class="guide__panel">
            <header class="guide__head">
                <div>
                    <h2 class="guide__title" id="guideTitle">Panduan pengeluaran</h2>
                    <p class="guide__lead">Catat setiap uang yang keluar untuk menjalankan cafe. Dari sini ARCH menghitung laba bersih Anda.</p>
                </div>
                <button type="button" class="guide__close" data-guide-close aria-label="Tutup panduan"><i class="fas fa-xmark"></i></button>
            </header>

            <div class="guide__body">
                {{-- Rumus: inti dari halaman ini --}}
                <div class="guide-formula">
                    <div class="guide-formula__row"><span>Penjualan bersih</span><span>dari aplikasi kasir</span></div>
                    <div class="guide-formula__row is-minus"><span>Pengeluaran</span><span>dicatat di halaman ini</span></div>
                    <div class="guide-formula__total"><span>Laba bersih</span><small><i class="fas fa-wand-magic-sparkles"></i> dihitung otomatis</small></div>
                </div>

                <section class="guide__section">
                    <h3>Tiga langkah</h3>
                    <ol class="guide-steps">
                        <li>
                            <strong>Catat saat uang keluar</strong>
                            <span>Isi jumlah, tanggal uang dibayarkan, dan keterangan singkat. Tanggal yang dipakai adalah tanggal bayar, bukan tanggal input.</span>
                        </li>
                        <li>
                            <strong>Pilih kategori yang tepat</strong>
                            <span>Kategori menentukan pos biaya di laporan. Lihat contoh tiap kategori di bawah.</span>
                        </li>
                        <li>
                            <strong>Pantau di Analisa keuangan</strong>
                            <span>Laba bersih, porsi tiap pos biaya, dan perbandingan dengan periode sebelumnya terhitung otomatis.</span>
                        </li>
                    </ol>
                </section>

                <section class="guide__section">
                    <h3>Yang dicatat dan yang tidak</h3>
                    <div class="guide-split">
                        <div class="guide-split__col is-yes">
                            <div class="guide-split__title"><i class="fas fa-check"></i> Catat</div>
                            <ul>
                                <li>Belanja bahan dan perlengkapan</li>
                                <li>Gaji, upah harian, lembur</li>
                                <li>Listrik, air, internet, gas</li>
                                <li>Sewa, retribusi, servis alat</li>
                            </ul>
                        </div>
                        <div class="guide-split__col is-no">
                            <div class="guide-split__title"><i class="fas fa-xmark"></i> Jangan dicatat</div>
                            <ul>
                                <li>Penjualan, sudah otomatis dari kasir</li>
                                <li>Uang pribadi yang diambil pemilik</li>
                                <li>Pindah uang antar kas atau rekening</li>
                                <li>Uang kembalian pelanggan</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="guide__section">
                    <h3>Contoh tiap kategori</h3>
                    <ul class="guide-cats">
                        @foreach (\App\Models\Expense::CATEGORIES as $key => $c)
                            <li>
                                <span class="guide-cats__ic"><i class="fas {{ $c['icon'] }}"></i></span>
                                <span>
                                    <strong>{{ $c['label'] }}</strong>
                                    <small>{{ $c['hint'] }}</small>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="guide__section">
                    <h3>Kebiasaan yang membantu</h3>
                    <ul class="guide-tips">
                        <li><i class="fas fa-receipt"></i><span>Satu nota satu catatan. Untuk belanja pasar kecil-kecil, gabungkan per hari dan tulis nama toko di Catatan.</span></li>
                        <li><i class="fas fa-forward"></i><span>Punya beberapa nota? Pakai <b>Simpan &amp; catat lagi</b>; kategori dan tanggal terakhir ikut terisi.</span></li>
                        <li><i class="fas fa-calendar-check"></i><span>Lupa mencatat kemarin? Tekan tombol <b>Kemarin</b> di samping tanggal.</span></li>
                        <li><i class="fas fa-pen"></i><span>Salah ketik jumlah? Ubah lewat ikon pensil; laporan langsung ikut terkoreksi.</span></li>
                        <li><i class="fas fa-clock-rotate-left"></i><span>Biasakan mencatat sebelum tutup kasir setiap malam supaya laba harian akurat.</span></li>
                    </ul>
                </section>
            </div>

            <footer class="guide__foot">
                <button type="button" class="btn btn-light" data-guide-close>Tutup</button>
                @unless (request()->routeIs('expenses.create'))
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Catat pengeluaran</a>
                @endunless
            </footer>
        </aside>
    </div>
@endpush
