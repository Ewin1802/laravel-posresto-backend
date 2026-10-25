@php
    $r = $reminder ?? \App\Support\CloudReminder::info();
    $rpAmt = number_format($r['amount'], 0, ',', '.');
@endphp
<section class="renew renew--{{ $r['level'] }}" aria-label="Pengingat perpanjangan cloud server">
    <div class="renew__icon"><i class="fas fa-server"></i></div>
    <div class="renew__body">
        <div class="renew__title">
            Perpanjangan cloud server
            @if ($r['level'] === 'urgent')
                <span class="tag tag--red">Segera bayar</span>
            @elseif ($r['level'] === 'soon')
                <span class="tag tag--amber">Mendekati jatuh tempo</span>
            @endif
        </div>
        <p class="renew__text">
            Biaya tahunan Rp {{ $rpAmt }}, jatuh tempo <b>{{ $r['due']->translatedFormat('l, d F Y') }}</b>.
            Bayar sebelum tanggal tersebut agar web dan data penjualan tetap aman.
        </p>
        <div class="renew__meter" aria-hidden="true"><span style="width: {{ $r['progress'] }}%"></span></div>
    </div>
    <div class="renew__count">
        <strong>{{ $r['days'] === 0 ? 'Hari ini' : $r['days'] }}</strong>
        @if ($r['days'] > 0)<span>hari lagi</span>@endif
    </div>
    @if (auth()->user()->role === 'admin')
        <a href="{{ route('expenses.create', ['category' => 'listrik_air']) }}" class="btn btn-light btn-sm renew__action">
            <i class="fas fa-check"></i> Sudah bayar? Catat
        </a>
    @endif
</section>
