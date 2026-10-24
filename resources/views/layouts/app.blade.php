@php $asv = fn ($p) => file_exists(public_path($p)) ? filemtime(public_path($p)) : '2'; @endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2B1D17">
    <title>@yield('title') — ARCH Manajemen</title>
    <script>
        /* Pulihkan status sidebar sebelum halaman digambar, agar tidak berkedip */
        document.documentElement.classList.add('sb-preload');
        try { if (localStorage.getItem('arch.sidebar') === 'collapsed') document.documentElement.classList.add('sb-collapsed'); } catch (e) {}
    </script>
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
    <div class="shell">
        @include('components.sidebar')
        <div class="sidebar-backdrop" aria-hidden="true"></div>

        <div class="main">
            @include('components.header')

            <main class="content" id="content">
                @yield('main')
            </main>

            @include('components.footer')
        </div>
    </div>

    @stack('modals')

    {{-- Shared confirm dialog for destructive actions (forms with data-confirm) --}}
    <div class="modal fade confirm-modal" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body pt-4">
                    <div class="confirm-modal__icon"><i class="fas fa-trash-can"></i></div>
                    <h3 data-confirm-title>Hapus data ini?</h3>
                    <p data-confirm-text></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" data-confirm-ok>Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"><\/script>')</script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>(window.jQuery && jQuery.fn.modal) || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"><\/script>')</script>
    <script src="{{ asset('js/arch-premium.js') }}?v={{ $asv('js/arch-premium.js') }}"></script>

    @stack('scripts')
</body>

</html>
