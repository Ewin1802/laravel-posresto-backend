@php $asv = fn ($p) => file_exists(public_path($p)) ? filemtime(public_path($p)) : '2'; @endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses ditolak — ARCH Manajemen</title>
    <link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght,SOFT@9..144,500..700,0..100&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/arch-premium.css') }}?v={{ $asv('css/arch-premium.css') }}">
    <style>
        .deny { min-height: 100vh; display: grid; place-items: center; padding: 24px; }
        .deny__card { max-width: 460px; text-align: center; }
        .deny__code { font-family: var(--font-display); font-size: 96px; font-weight: 600; line-height: 1; color: var(--red); letter-spacing: -.03em; }
        .deny h1 { font-family: var(--font-display); font-weight: 600; font-size: 28px; margin: 14px 0 8px; }
        .deny p { color: var(--muted); margin: 0 0 26px; }
        .deny .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .deny .btn { text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: 600; display: inline-block; }
        .deny .btn-primary { background: var(--red); color: #fff; }
        .deny .btn-light { background: #fff; border: 1px solid var(--line-strong); color: var(--ink); }
    </style>
</head>

<body class="arch">
    <main class="deny">
        <div class="deny__card">
            <img src="{{ asset('img/logo_arch_web.png') }}" alt="ARCH" width="56" height="56" style="border-radius:50%">
            <div class="deny__code" style="margin-top:18px">403</div>
            <h1>Halaman ini khusus admin</h1>
            <p>Akun Anda tidak memiliki izin untuk membuka halaman ini. Hubungi pemilik usaha bila Anda memerlukan akses.</p>
            <div class="actions">
                <a href="{{ url('/home') }}" class="btn btn-primary">Kembali ke dashboard</a>
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="margin:0">
                        @csrf
                        <button type="submit" class="btn btn-light" style="cursor:pointer; font: inherit; font-weight:600;">Masuk dengan akun lain</button>
                    </form>
                @endauth
            </div>
        </div>
    </main>
</body>

</html>
