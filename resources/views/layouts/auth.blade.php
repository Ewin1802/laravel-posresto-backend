@php $asv = fn ($p) => file_exists(public_path($p)) ? filemtime(public_path($p)) : '2'; @endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#2B1D17">
    <title>@yield('title') — ARCH Manajemen</title>
    <link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght,SOFT@9..144,500..700,0..100&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('style')
    <link rel="stylesheet" href="{{ asset('css/arch-premium.css') }}?v={{ $asv('css/arch-premium.css') }}">
</head>

<body class="arch">
    <div class="auth">
        <section class="auth__art">
            <a href="{{ route('landing') }}" class="auth__brand">
                <img src="{{ asset('img/logo_arch_web.png') }}" alt="">
                <strong>ARCH</strong>
            </a>

            <div style="position: relative; z-index: 1;">
                <p class="auth__quote">Setiap cangkir tercatat, setiap rupiah terjaga.</p>
                <p class="auth__quote-sub">Pusat kendali penjualan, persediaan, dan laporan keuangan ARCH — Boroko Timur, Kaidipang.</p>
            </div>

            <div class="auth__mark" aria-hidden="true">
                <img src="{{ asset('img/logo_arch_web.png') }}" alt="">
            </div>

            <div class="auth__foot">&copy; {{ date('Y') }} ARCH Manajemen · Dirancang oleh <a href="https://www.instagram.com/ewin.lntp/" target="_blank" rel="noopener">Ewin Lantapa</a></div>
        </section>

        <section class="auth__form">
            <div class="auth__card">
                @yield('main')
            </div>
        </section>
    </div>

    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"><\/script>')</script>
    <script src="{{ asset('js/arch-premium.js') }}?v={{ $asv('js/arch-premium.js') }}"></script>
    @stack('scripts')
</body>

</html>
