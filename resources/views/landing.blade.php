<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Arch Coffee Manajemen</title>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>

<style>
:root{
  --bg:#4E342E;
  --bg2:#1a120d;
  --gold:#c7a45a;
  --gold-soft:#e2c98f;
  --text:#f6f1eb;
  --muted:#cbbfb4;
}

*{box-sizing:border-box}
html,body{height:100%}
a{text-decoration:none;color:inherit}

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

/* ===== MODAL ===== */
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
  transition:.8s ease;
}
#welcomeModal.fade-out{opacity:0;pointer-events:none}

#enterBtn{
  margin-top:24px;
  padding:14px 36px;
  border:none;
  border-radius:40px;
  font-weight:600;
  cursor:pointer;
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#2a1b12;
  box-shadow:0 10px 30px rgba(199,164,90,.35);
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
  padding:16px 22px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.brand{
  font-family:'Playfair Display',serif;
  font-weight:700;
  letter-spacing:.12em;
}

.brand-white{
  color:#ffffff;
  margin-left:6px;
}

.nav-actions{
  display:flex;
  align-items:center;
  gap:18px;
}
.nav-login{
  padding:10px 22px;
  border-radius:30px;
  font-weight:600;
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#2a1b12;
}

/* Dropdown */
.dropdown{position:relative}
.dropdown-btn{
  background:none;
  border:none;
  color:var(--text);
  cursor:pointer;
  padding:10px 16px;
}
.dropdown-menu{
  position:absolute;
  right:0;
  top:130%;
  min-width:180px;
  background:rgba(20,13,9,.95);
  border-radius:12px;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 20px 40px rgba(0,0,0,.45);
  opacity:0;
  pointer-events:none;
  transform:translateY(10px);
  transition:.25s ease;
}
.dropdown.active .dropdown-menu{
  opacity:1;
  pointer-events:auto;
  transform:translateY(0);
}
.dropdown-menu a{
  display:block;
  padding:14px 18px;
}
.dropdown-menu a:active{
  background:rgba(199,164,90,.25);
  transform:scale(.97);
}

/* ===== HERO ===== */
.hero{
  min-height:89vh;
  padding:60px 22px 40px;
  display:flex;
  align-items:center;
}
.hero-inner{
  max-width:1200px;
  margin:auto;
  display:flex;
  gap:42px;
  flex-wrap:wrap;
  align-items:center;
  justify-content:space-between;
}
.hero-text{flex:1 1 480px}
.hero-title{
  font-family:'Playfair Display',serif;
  font-size:clamp(2.6rem,4vw + 1rem,4.8rem);
  line-height:1.05;
}
.hero-sub{color:var(--muted);line-height:1.8}

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
  box-shadow:0 20px 50px rgba(0,0,0,.45);
}
.logo-img{width:100%;border-radius:14px;display:block}

/* ===== TOP PRODUCTS ===== */
.top-products{padding:60px 22px}
.top-products-inner{max-width:1200px;margin:auto;text-align:center}

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
  border:1px solid rgba(255,255,255,.12);
}
.filter-btn.active{
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#2a1b12;
  font-weight:600;
}

table{
  width:100%;
  border-collapse:collapse;
  background:rgba(255,255,255,.04);
  border-radius:14px;
  overflow:hidden;
}
th,td{padding:16px;text-align:left}
th{background:rgba(199,164,90,.2);color:var(--gold)}
tr:not(:last-child){border-bottom:1px solid rgba(255,255,255,.08)}
/* ===== RANK BADGE ===== */
.rank-badge{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:34px;
  height:34px;
  border-radius:50%;
  font-weight:600;
  font-size:.9rem;
}

.rank-1{
  background:linear-gradient(135deg,#FFD700,#E6B800);
  color:#2a1b12;
  box-shadow:0 0 10px rgba(255,215,0,.5);
}

.rank-2{
  background:linear-gradient(135deg,#E0E0E0,#BDBDBD);
  color:#2a1b12;
}

.rank-3{
  background:linear-gradient(135deg,#CD7F32,#A05A2C);
  color:#fff;
}

.rank-default{
  background:rgba(255,255,255,.12);
  color:var(--text);
}


/* ===== MAP ===== */
.map-wrap{padding:50px 22px 80px}
.map-frame{
  width:100%;
  height:460px;
  border:0;
  border-radius:16px;
  box-shadow:0 20px 50px rgba(0,0,0,.45);
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

<!-- MODAL -->
<div id="welcomeModal">
  <h1 class="gold" style="font-family:'Playfair Display',serif;font-size:2.3rem">
    Selamat Datang di Arch Coffee
  </h1>
  <p style="max-width:420px;color:var(--muted)">
    Rasakan pengalaman kopi premium dengan suasana elegan dan menenangkan.
  </p>
  <button id="enterBtn">Mulai</button>
</div>

<audio id="bgAudio" src="/audio/ARCH11.mp3" preload="auto" loop></audio>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <div class="brand">
    <span class="gold">ARCH COFFEE -</span>
    <span class="brand-white">MANAJEMEN</span>
    </div>

    <div class="nav-actions">
      <div class="dropdown" id="infoDropdown">
        <button class="dropdown-btn">Informasi ▾</button>
        <div class="dropdown-menu">
          <a href="https://www.instagram.com/_archcoffee/" target="_blank">Instagram</a>
          <a href="https://www.instagram.com/ewin.lntp/" target="_blank">Programmer</a>
          <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s" target="_blank">Tutorial</a>
        </div>
      </div>
      <a href="{{ route('login') }}" class="nav-login">Login</a>
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
        <br><br>
        Lokasi di Kompleks Wisata Pantai Batu Pinagut Kabupaten Bolaang Mongondow Utara.
      </p>
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
    <h2 class="gold">Produk Terlaris {{ $range }} Hari Terakhir</h2>

    <div class="filter-range">
      <a href="{{ route('landing',['range'=>7]) }}" class="filter-btn {{ $range==7?'active':'' }}">7 Hari</a>
      <a href="{{ route('landing',['range'=>30]) }}" class="filter-btn {{ $range==30?'active':'' }}">30 Hari</a>
      <a href="{{ route('landing',['range'=>90]) }}" class="filter-btn {{ $range==90?'active':'' }}">90 Hari</a>
    </div>

    <table>
      <thead>
        <tr><th>#</th><th>Produk</th><th>Total</th><th>Harga</th></tr>
      </thead>
      <tbody>
        @forelse($topProducts as $i=>$item)
        <tr>
          <td>
            @if($i === 0)
                <span class="rank-badge rank-1">1</span>
            @elseif($i === 1)
                <span class="rank-badge rank-2">2</span>
            @elseif($i === 2)
                <span class="rank-badge rank-3">3</span>
            @else
                <span class="rank-badge rank-default">{{ $i + 1 }}</span>
            @endif
            </td>

          <td>{{ $item->name }}</td>
          <td>{{ $item->total_qty }}</td>
          <td>Rp {{ number_format($item->unit_price,0,',','.') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center">Belum ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<!-- MAP -->
<section class="map-wrap">
  <div data-aos="zoom-in">
    <h2 class="gold">Lokasi Arch Coffee</h2>
    <iframe class="map-frame"
      src="https://maps.google.com/maps?q=0.9153337773,123.2724709&z=18&output=embed"
      loading="lazy"></iframe>
  </div>
</section>

<footer>© {{ date('Y') }} Arch Coffee - Mr. Suwanto Goma. All rights reserved.</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
AOS.init({ duration:1100, once:true, offset:90 });

const audio = document.getElementById('bgAudio');
const modal = document.getElementById('welcomeModal');

document.getElementById('enterBtn').addEventListener('click', () => {
  modal.classList.add('fade-out');
  setTimeout(()=> modal.style.display='none', 800);
  audio.play().catch(()=>{});
});

// Dropdown logic
const dropdown = document.getElementById('infoDropdown');
const dropdownBtn = dropdown.querySelector('.dropdown-btn');

dropdownBtn.addEventListener('click', (e) => {
  e.stopPropagation();
  dropdown.classList.toggle('active');
});

document.addEventListener('click', (e) => {
  if (!dropdown.contains(e.target)) {
    dropdown.classList.remove('active');
  }
});

dropdown.querySelectorAll('.dropdown-menu a').forEach(link => {
  link.addEventListener('click', () => dropdown.classList.remove('active'));
});
</script>

</body>
</html>
