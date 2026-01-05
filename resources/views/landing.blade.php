<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Arch Coffee Manajemen</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>

  <!-- AOS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#5c2c08;
      --bg2:#1a120d;
      --espresso:#2b1a12;
      --gold:#c7a45a;
      --gold-soft:#e2c98f;
      --text:#f6f1eb;
      --muted:#cbbfb4;
    }

    *{box-sizing:border-box}
    html,body{height:100%}

    body{
      margin:0;
      font-family:'Poppins',sans-serif;
      color:var(--text);
      background:
        radial-gradient(900px 600px at 10% -10%, #2a1b12 0%, transparent 60%),
        radial-gradient(800px 600px at 90% 10%, #1f140e 0%, transparent 65%),
        linear-gradient(180deg,var(--bg) 0%, var(--bg2) 100%);
      overflow-x:hidden;
    }

    .gold{
      color:var(--gold);
      text-shadow:0 0 8px rgba(199,164,90,.4);
    }

    /* ===== Modal Pembuka ===== */
    #welcomeModal{
      position:fixed;
      inset:0;
      background:rgba(10,6,4,.94);
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      z-index:9999;
      text-align:center;
      transition:opacity .8s ease;
    }

    #welcomeModal.fade-out{
      opacity:0;
      pointer-events:none;
    }

    #enterBtn{
      padding:14px 36px;
      background:linear-gradient(135deg,var(--gold),var(--gold-soft));
      color:#2a1b12;
      border:none;
      border-radius:40px;
      font-weight:600;
      font-size:1.05rem;
      cursor:pointer;
      box-shadow:0 10px 30px rgba(199,164,90,.35);
      transition:.25s ease;
    }

    #enterBtn:hover{
      transform:translateY(-3px);
      box-shadow:0 16px 40px rgba(199,164,90,.55);
    }

    /* ===== NAV ===== */
    .nav{
      position:sticky;
      top:0;
      z-index:50;
      backdrop-filter:blur(10px);
      background:rgba(20,13,9,.65);
      border-bottom:1px solid rgba(255,255,255,.06);
    }

    .nav-inner{
      max-width:1200px;
      margin:auto;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:16px 22px;
    }

    .brand{
      font-family:'Playfair Display',serif;
      letter-spacing:.12em;
      font-weight:700;
    }

    .nav-links a{
      color:var(--text);
      opacity:.85;
      margin-left:20px;
      font-weight:500;
      transition:.25s ease;
    }

    .nav-links a:hover{
      color:var(--gold);
    }

    /* ===== HERO ===== */
    .hero{
        min-height: 89vh; /* sebelumnya 90vh */
        display:flex;
        align-items:center;
        padding:60px 22px 40px;
    }


    .hero-inner{
      max-width:1200px;
      margin:auto;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:42px;
      flex-wrap:wrap;
    }

    .hero-text{
      flex:1 1 480px;
    }

    .hero-title{
      font-family:'Playfair Display',serif;
      font-weight:800;
      line-height:1.05;
      margin:0 0 14px;
      font-size:clamp(2.6rem,4vw + 1rem,4.8rem);
    }

    .hero-sub{
      color:var(--muted);
      line-height:1.8;
      max-width:720px;
    }

    .btn-cta{
      display:inline-block;
      margin-top:26px;
      padding:14px 34px;
      border-radius:40px;
      background:linear-gradient(135deg,var(--gold),var(--gold-soft));
      color:#2a1b12;
      font-weight:600;
      letter-spacing:.02em;
      transition:.25s ease;
      box-shadow:0 10px 30px rgba(199,164,90,.35);
      position:relative;
      overflow:hidden;
    }

    .btn-cta:hover{
      transform:translateY(-3px);
      box-shadow:0 18px 44px rgba(199,164,90,.55);
    }

    .btn-cta:after{
      content:"";
      position:absolute;
      inset:0;
      background:linear-gradient(120deg,transparent 30%,rgba(255,255,255,.5),transparent 60%);
      transform:translateX(-120%);
      transition:.5s ease;
    }

    .btn-cta:hover:after{
      transform:translateX(120%);
    }
    /* ===== TOP PRODUCTS ===== */
    .top-products{
      padding:60px 22px;
    }

    .top-products-inner{
      max-width:1200px;
      margin:auto;
      text-align:center;
    }

    .section-title{
      font-family:'Playfair Display',serif;
      font-size:2.2rem;
      color:var(--gold);
    }

    .section-sub{
      color:var(--muted);
      margin-bottom:30px;
    }

    table{
      width:100%;
      border-collapse:collapse;
      background:rgba(255,255,255,.04);
      border-radius:14px;
      overflow:hidden;
    }

    th, td{
      padding:16px;
      text-align:left;
    }

    th{
      background:rgba(199,164,90,.2);
      color:var(--gold);
    }

    tr:not(:last-child){
      border-bottom:1px solid rgba(255,255,255,.08);
    }

    tr:hover{
      background:rgba(199,164,90,.08);
    }
    /* ===== FILTER RANGE ===== */
    .filter-range{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-bottom:24px;
    flex-wrap:wrap;
    }

    .filter-btn{
    padding:10px 22px;
    border-radius:30px;
    background:rgba(255,255,255,.06);
    color:var(--text);
    font-weight:500;
    text-decoration:none;
    transition:.25s ease;
    border:1px solid rgba(255,255,255,.12);
    }

    .filter-btn:hover{
    background:rgba(199,164,90,.18);
    color:var(--gold);
    }

    .filter-btn.active{
    background:linear-gradient(135deg,var(--gold),var(--gold-soft));
    color:#2a1b12;
    border-color:transparent;
    font-weight:600;
    }


    .hero-visual{
      flex:0 0 auto;
      max-width:46%;
      min-width:320px;
    }

    .logo-panel{
      background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);
      border-radius:18px;
      padding:16px;
      backdrop-filter:blur(6px);
      box-shadow:0 20px 50px rgba(0,0,0,.45);
      transition:.25s ease;
    }

    .logo-panel:hover{
      transform:translateY(-4px);
    }

    .logo-img{
      width:100%;
      height:auto;
      border-radius:14px;
      display:block;
      filter:drop-shadow(0 10px 30px rgba(0,0,0,.5));
    }

    /* ===== MAP ===== */
    .map-wrap{
      padding:50px 22px 80px;
    }

    .map-inner{
      max-width:1200px;
      margin:auto;
      text-align:center;
    }

    .map-title{
      font-family:'Playfair Display',serif;
      margin:0 0 20px;
      font-size:clamp(1.6rem,1vw + 1rem,2.2rem);
    }

    .map-frame{
      width:100%;
      height:460px;
      border:0;
      border-radius:16px;
      box-shadow:0 20px 50px rgba(0,0,0,.45);
      transition:.25s ease;
    }

    .map-frame:hover{
      transform:translateY(-3px);
    }

    footer{
      padding:36px 22px;
      border-top:1px solid rgba(255,255,255,.06);
      text-align:center;
      color:var(--muted);
    }

    @media(max-width:900px){
      .hero-inner{flex-direction:column}
      .hero-visual{order:-1;max-width:88%}
      .hero-text{text-align:center}
    }
  </style>
</head>
<body>

  <!-- Modal Pembuka -->
  <div id="welcomeModal">
    <h1 class="gold" style="font-family:'Playfair Display',serif;font-size:2.3rem;margin-bottom:18px;">
      Selamat Datang di Arch Coffee
    </h1>
    <p style="max-width:420px;margin-bottom:30px;color:var(--muted);">
      Rasakan pengalaman kopi premium dengan suasana elegan dan menenangkan.
    </p>
    <button id="enterBtn">Mulai</button>
  </div>

  <!-- Audio -->
  <audio id="bgAudio" src="/audio/ARCH11.mp3" preload="auto" loop></audio>

  <!-- NAV -->
  <nav class="nav">
    <div class="nav-inner">
      <div class="brand gold">ARCH COFFEE MANAJEMEN</div>
      <div class="nav-links">
        <a href="https://www.instagram.com/_archcoffee/" target="_blank">Instagram</a>
        <a href="https://www.instagram.com/ewin.lntp/" target="_blank">Programmer</a>
        <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s" target="_blank">Tutorial</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-text" data-aos="fade-right">
        <h1 class="hero-title gold">Hi,<br/>Coffee Lovers!</h1>
        <p class="hero-sub">
          Nikmati kopi istimewa sambil memandang ombak dan merasakan semilir angin laut di Arch Coffee.
          Cocok untuk bekerja, bersantai, atau berkumpul bersama teman.
          Kami juga menyajikan hidangan ringan hingga makanan berat.
          <br><br>
          Lokasi di Kompleks Wisata Pantai Batu Pinagut Kabupaten Bolaang Mongondow Utara.
        </p>
        <a href="{{ route('login') }}" class="btn-cta" id="loginBtn">Login</a>
      </div>

      <div class="hero-visual" data-aos="fade-left">
        <div class="logo-panel">
          <img src="{{ asset('img/logo_arch_landing.png') }}" alt="Logo Arch Coffee" class="logo-img"/>
        </div>
      </div>
    </div>
  </section>



  <!-- TOP PRODUCTS -->
  <section class="top-products">
    <div class="top-products-inner" data-aos="fade-up">
        <h2 class="section-title">
            Produk Terlaris {{ $range }} Hari Terakhir
        </h2>

      <p class="section-sub">Berdasarkan jumlah pemesanan pelanggan</p>
        <div class="filter-range">
            <a href="{{ route('landing', ['range' => 7]) }}"
                class="filter-btn {{ $range == 7 ? 'active' : '' }}">
                7 Hari
            </a>

            <a href="{{ route('landing', ['range' => 30]) }}"
                class="filter-btn {{ $range == 30 ? 'active' : '' }}">
                30 Hari
            </a>

            <a href="{{ route('landing', ['range' => 90]) }}"
                class="filter-btn {{ $range == 90 ? 'active' : '' }}">
                90 Hari
            </a>
        </div>

      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Produk</th>
            <th>Total Dipesan</th>
            <th>Harga Satuan</th>

          </tr>
        </thead>
        <tbody>
          @forelse($topProducts as $i => $item)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->total_qty }}</td>
              <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align:center;color:var(--muted)">
                Belum ada data pemesanan
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <!-- MAP -->
  <section class="map-wrap">
    <div class="map-inner" data-aos="zoom-in">
      <h2 class="map-title gold">Lokasi Arch Coffee</h2>
      <iframe
        class="map-frame"
        src="https://maps.google.com/maps?q=0.9153337773,123.2724709&z=18&output=embed"
        allowfullscreen
        loading="lazy">
      </iframe>
    </div>
  </section>

  <footer>© {{ date('Y') }} Arch Coffee. All rights reserved.</footer>

  <!-- AOS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({ duration:1100, once:true, offset:90 });

    const audio = document.getElementById('bgAudio');

    document.getElementById('enterBtn').addEventListener('click', function(){
      const modal = document.getElementById('welcomeModal');
      modal.classList.add('fade-out');
      audio.play().catch(err => console.log('Autoplay diblokir:', err));
      setTimeout(()=>{ modal.style.display='none'; },800);
    });

    document.getElementById('loginBtn').addEventListener('click', function(){
      audio.pause();
      audio.currentTime = 0;
    });
  </script>
</body>
</html>
