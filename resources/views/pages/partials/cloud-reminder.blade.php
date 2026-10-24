@php $due = $reminder['due']; $days = $reminder['days']; @endphp
<div class="notice mb-4">
    <i class="fas fa-server"></i>
    <div>
        <strong>Perpanjangan cloud server {{ $days === 0 ? 'jatuh tempo hari ini' : 'dalam ' . $days . ' hari' }}</strong>
        <div class="mt-1">
            Layanan server ARCH dibayar tahunan sebesar Rp 1.000.000. Jatuh tempo berikutnya
            {{ $due->translatedFormat('d F Y') }}. Lakukan pembayaran sebelum tanggal tersebut agar data penjualan tetap aman.
        </div>
    </div>
</div>
