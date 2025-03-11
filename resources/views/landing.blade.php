<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arch Coffee Manajemen</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #011a24;
            color: white;
            font-family: 'Poppins', sans-serif;
        }

        h1, h2 {
            font-family: 'Orbitron', sans-serif;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 100vh;
            padding: 50px;
        }

        .hero-text {
            max-width: 50%;
        }

        .hero h1 {
            font-size: 5rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .hero p {
            font-size: 1.2rem;
            color: #bdbdbd;
            font-weight: 300;
        }

        .btn-custom {
            background-color: #00bcd4;
            color: white;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
            font-weight: 600;
        }

        .btn-custom:hover {
            background-color: #008c9e;
        }

        .hero img {
            max-width: 45%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 0 20px #00bcd4, 0 0 40px #00bcd4, 0 0 60px #00bcd4;
            transition: 0.3s ease-in-out;
        }

        .hero img:hover {
            box-shadow: 0 0 30px #00ffff, 0 0 60px #00ffff, 0 0 90px #00ffff;
        }

        header {
            text-align: center;
            padding: 20px 0;
        }

        header nav a {
            margin: 0 15px;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        header nav a:hover {
            color: #00bcd4;
        }
    </style>
</head>
<body>

    <header>
        <h2>ARCH COFFEE MANAJEMEN</h2>
        <nav>
            <a href="https://www.instagram.com/_archcoffee/">Instagram</a>
            <a href="https://www.instagram.com/ewin.lntp/">Programmer</a>
            <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s">Tutorial</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-text">
            <h1>HELLO,</h1>
            <h1>BOS WANTO !</h1>
            <p>Semoga Penjualan hari ini meningkat dan cepat kaya. Aminnn</p>
            <a href="{{ route('login') }}" class="btn-custom">Login</a>
        </div>
        <img src="{{ asset('img/logo_arch_landing.png') }}" alt="Digital Art">
    </section>

</body>
</html>
