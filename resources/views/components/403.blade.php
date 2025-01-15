<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    {{-- Favicon - Logo web disamping title --}}
    <link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            font-size: 72px;
            margin-bottom: 20px;
            color: #800000;
            font-weight: bold;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .message-container {
            background-color: #800000;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .message-container p {
            color: #ffffff;
            margin: 0;
            font-size: 18px;
            line-height: 1.6;
        }
        a {
            display: inline-block;
            background-color: #80c4b3; /* Ubah warna tombol menjadi #80c4b3 */
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #66a89b; /* Tambahkan warna hover yang lebih gelap */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>403</h1>
        <div class="message-container">
            {{-- <p>Maaf, Anda tidak memiliki akses untuk halaman ini.</p> --}}
            <p>INI ADALAH DAERAH TERLARANG.</p>
            <p>Terima Kasih</p>
        </div>
        <a href="{{ url()->previous() }}">Pulang disini</a>
    </div>
</body>
</html>
