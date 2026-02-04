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
  position:fixed;
  top:0;
  left:0;
  width:100%;
  z-index:100;
  backdrop-filter:blur(10px);
  background:linear-gradient(
    180deg,
    rgba(10,6,4,.75),
    rgba(10,6,4,.35)
  );
  backdrop-filter:blur(12px);
  border-bottom:1px solid rgba(255,255,255,.08);
}

.nav-inner{
  max-width:1200px;
  margin:auto;
  padding:16px 22px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

/* ===== BRAND LOGO ===== */
.brand-logo{
  display:flex;
  align-items:center;
  gap:12px;
  position:relative;
}
.brand-logo::after{
  content:'';
  position:absolute;
  left:52px;              /* sejajar setelah logo */
  bottom:-6px;            /* sedikit di bawah teks */
  width:90px;             /* ⬅️ DIPENDEKKAN */
  height:2px;
  background:linear-gradient(
    90deg,
    rgba(199,164,90,.9),
    rgba(199,164,90,.2),
    transparent
  );
}

.brand-img{
  width:32px;              /* ⬅️ LEBIH TERLIHAT */
  height:auto;
  display:block;
  filter:drop-shadow(0 0 6px rgba(199,164,90,.6));
}
.brand-text{
  font-family:'Playfair Display',serif;
  font-weight:700;
  letter-spacing:.12em;    /* sedikit dikurangi */
  font-size:1.15rem;       /* lebih balance */
  line-height:1.1;
  color:var(--gold);
  text-shadow:
    0 2px 6px rgba(0,0,0,.85),
    0 0 10px rgba(199,164,90,.35);
}

/* HAMBURGER */
.nav-toggle{
  background:none;
  border:none;
  font-size:1.9rem;
  color:#fff;
  cursor:pointer;
}
.nav-toggle:hover{
  color:var(--gold-soft);
}

/* MENU HAMBURGER */
.nav-actions{
  position:absolute;
  top:100%;
  right:22px;

  display:flex;
  flex-direction:column;
  gap:14px;

  background:rgba(20,13,9,.96);
  padding:20px;
  border-radius:18px;
  min-width:220px;

  box-shadow:0 20px 40px rgba(0,0,0,.45);
  border:1px solid rgba(255,255,255,.08);

  opacity:0;
  pointer-events:none;
  transform:translateY(-10px);
  transition:.25s ease;
}

.nav-actions.active{
  opacity:1;
  pointer-events:auto;
  transform:translateY(0);
}

.nav-actions a{
  padding:10px 14px;
  border-radius:10px;
  text-decoration:none;
  color:var(--text);
}

.nav-actions a:hover{
  background:rgba(199,164,90,.15);
}

/* LOGIN BUTTON */
.nav-login{
  margin-top:6px;
  text-align:center;
  font-weight:600;
  border-radius:30px;
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#2a1b12 !important;
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
  min-height:auto;
  padding:0 22px 60px;   /* HAPUS padding atas */
  margin-top:-180px;    /* TARIK NAIK ke slider */
  position:relative;
  z-index:5;
margin-bottom:-40px; /* tarik produk terlaris naik */
}

.hero-inner{
  max-width:100%;        /* HAPUS BATAS */
  margin:auto;
  padding:0 6vw;         /* spacing responsif */
  display:flex;
  gap:42px;
  align-items:center;
  justify-content:flex-start;
}

/* .hero-text{flex:1 1 480px} */
.hero-text{
  max-width:900px; /* biar ga kepanjangan */
  background:rgba(20,13,9,.55);
  backdrop-filter:blur(8px);
  padding:36px 42px;
  border-radius:24px;
  box-shadow:0 20px 50px rgba(0,0,0,.45);
}


.hero-title{
  font-family:'Playfair Display',serif;
  font-size:clamp(2.6rem,4vw + 1rem,4.8rem);
  line-height:1.05;
}
.hero-sub{
  color:var(--muted);
  line-height:1.6;
  margin-top:18px;
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
  box-shadow:0 20px 50px rgba(0,0,0,.45);
}
.logo-img{width:100%;border-radius:14px;display:block}

/* ===== TOP PRODUCTS ===== */
.top-products{
  padding:20px 22px 24px;
}

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
.map-wrap{
  padding:24px 22px 60px;
}
.map-wrap h2{
  margin-top:0;
  margin-bottom:18px;
}

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

/* ===== PRODUCT SLIDER ===== */
.product-slider{
  width:100%;
  height:100vh;
  position:relative;
  overflow:hidden;
}
.product-slider::after{
  content:'';
  position:absolute;
  bottom:0;
  left:0;
  right:0;
  height:220px;
  background:linear-gradient(
    to bottom,
    rgba(0,0,0,0),
    rgba(26,18,13,1)
  );
  z-index:2;
}

.slider-wrapper{
  width:100%;
  height:100%;
  position:relative;
}

.slide{
  position:absolute;
  inset:0;
  background-size:cover;
  background-position:center;
  opacity:0;
  transform:scale(1.05);
  transition:opacity 1s ease, transform 1.2s ease;
}

.slide.active{
  opacity:1;
  transform:scale(1);
  z-index:1;
}

.slide-overlay{
  position:absolute;
  inset:0;
  display:flex;
  flex-direction:column;
  justify-content:center;
  padding-left:8%;
  background:linear-gradient(
    90deg,
    rgba(10,6,4,.88) 0%,   /* LEBIH GELAP */
    rgba(10,6,4,.55) 35%,
    rgba(10,6,4,.15) 60%,
    transparent 100%
  );
}

.slide-overlay h2{
  font-family:'Playfair Display',serif;
  font-size:clamp(2.4rem,4vw,4rem);
  color:var(--gold);
  text-shadow:
    0 2px 6px rgba(0,0,0,.8),
    0 6px 18px rgba(0,0,0,.6);
}

.slide-overlay p{
  font-size:1.5rem;
  font-weight:600;
  color:#fff;
  text-shadow:
    0 2px 6px rgba(0,0,0,.85);
}
.slide-content{
  position:absolute;
  left:8%;
  top:50%;
  transform:translateY(-50%);
  z-index:5; /* PASTIKAN DI ATAS SEMUA */

  max-width:420px;
  padding:28px 32px;
  background:rgba(20,13,9,.65);
  backdrop-filter:blur(6px);
  border-radius:18px;
  box-shadow:0 20px 40px rgba(0,0,0,.6);
}

.top-badge{
  position:absolute;
  top:-14px;
  left:-14px;
  padding:8px 14px;
  font-size:.75rem;
  font-weight:700;
  letter-spacing:.08em;
  border-radius:12px;
  backdrop-filter:blur(6px);
  box-shadow:0 8px 24px rgba(0,0,0,.45);
}

/* 🥇 TOP 1 */
.top-1{
  background:linear-gradient(135deg,#FFD700,#E6B800);
  color:#2a1b12;
  box-shadow:
    0 0 14px rgba(255,215,0,.8),
    0 10px 30px rgba(0,0,0,.5);
}

/* 🥈 TOP 2 */
.top-2{
  background:linear-gradient(135deg,#E0E0E0,#BDBDBD);
  color:#2a1b12;
}

/* 🥉 TOP 3 */
.top-3{
  background:linear-gradient(135deg,#CD7F32,#A05A2C);
  color:#fff;
}

@media(max-width:768px){
  .brand-img{
    width:22px;
  }
  .brand-text{
    font-size:.8rem;
  }
}

/* ===== MENU CAFE MODAL ===== */
.menu-modal{
  position:fixed;
  inset:0;
  z-index:9998;
  display:none;
}

.menu-modal.active{
  display:block;
}

.menu-modal-backdrop{
  position:absolute;
  inset:0;
  background:rgba(10,6,4,.85);
  backdrop-filter:blur(6px);
}

.menu-modal-content{
  position:relative;
  max-width:1100px;
  margin:5vh auto;
  background:rgba(20,13,9,.95);
  border-radius:24px;
  padding:32px;
  max-height:90vh;
  overflow:auto;
  box-shadow:0 30px 80px rgba(0,0,0,.6);
}

.menu-modal h2{
  margin-top:0;
  margin-bottom:24px;
  text-align:center;
}

.menu-modal-close{
  position:absolute;
  top:18px;
  right:22px;
  background:none;
  border:none;
  font-size:2rem;
  color:#fff;
  cursor:pointer;
}

/* GRID MENU */
.menu-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
  gap:22px;
}

.menu-card{
  background:rgba(255,255,255,.05);
  border-radius:18px;
  padding:16px;
  text-align:center;
}

.menu-card img{
  width:100%;
  height:160px;
  object-fit:cover;
  border-radius:14px;
  margin-bottom:12px;
}

.menu-card h4{
  margin:8px 0 4px;
}

.menu-card .price{
  color:var(--gold);
  font-weight:600;
}

.menu-card small{
  color:var(--muted);
}

/* ===== FILTER KATEGORI ===== */
.menu-filter{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-bottom:22px;
  justify-content:center;
}

.menu-filter .filter-btn{
  padding:8px 18px;
  border-radius:30px;
  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.12);
  color:var(--text);
  cursor:pointer;
  font-size:.85rem;
}

.menu-filter .filter-btn.active{
  background:linear-gradient(135deg,var(--gold),var(--gold-soft));
  color:#2a1b12;
  font-weight:600;
}



</style>
</head>

<body>

<!-- MODAL -->
<div id="welcomeModal">
  <h1 class="gold" style="font-family:'Playfair Display',serif;font-size:2.3rem">
    Kami senang menyambut Anda
  </h1>
  <p style="max-width:420px;color:var(--muted)">
    Mari temukan momen santai favorit Anda bersama kopi terbaik kami.
  </p>
  <button id="enterBtn">Mulai</button>
</div>

<audio id="bgAudio" src="/audio/ARCH11.mp3" preload="auto" loop></audio>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">

    <!-- BRAND LOGO + TEXT -->
    <div class="brand brand-logo">
      <img src="{{ asset('img/logo_arch_web.png') }}"
           alt="Arch Coffee Logo"
           class="brand-img">
      <span class="brand-text gold">ARCH COFFEE</span>
    </div>

    <!-- HAMBURGER -->
    <button class="nav-toggle" id="navToggle">☰</button>

    <!-- MENU HAMBURGER -->
    <div class="nav-actions" id="navMenu">
        <a href="#" id="openMenuCafe">Menu Cafe</a>
        <a href="https://www.instagram.com/_archcoffee/" target="_blank">Instagram</a>
        <a href="https://www.instagram.com/ewin.lntp/" target="_blank">Programmer</a>
        <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s" target="_blank">Tutorial</a>
        <a href="{{ route('login') }}" class="nav-login">Login</a>
    </div>

  </div>
</nav>

<!-- PRODUCT SLIDER -->
<section class="product-slider">
  <div class="slider-wrapper">

    @foreach($sliderProducts as $product)
  <div class="slide {{ $loop->first ? 'active' : '' }}"
       style="background-image:url('{{ asset($product->image) }}')">

    <div class="slide-content">

      @if(in_array($product->id, $topProductIds))
          @php
              $rank = array_search($product->id, $topProductIds) + 1;
          @endphp

          <div class="top-badge top-{{ $rank }}">
              TOP {{ $rank }}
          </div>
      @endif

      <h2>{{ $product->name }}</h2>
      <p>Rp {{ number_format($product->price,0,',','.') }}</p>
    </div>
  </div>
@endforeach


  </div>
</section>


<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-text" data-aos="fade-right">
        <h1 class="hero-title gold">Hi, Coffee Lovers!</h1>
        <p class="hero-sub">
            Nikmati kopi istimewa sambil memandang ombak dan merasakan semilir angin laut di Arch Coffee. Lokasi di Kompleks Wisata Pantai Batu Pinagut Kabupaten Bolaang Mongondow Utara.
        </p>
    </div>

    {{-- <div class="hero-visual" data-aos="fade-left">
      <div class="logo-panel">
        <img src="{{ asset('img/logo_arch_landing.png') }}" alt="Logo Arch Coffee" class="logo-img"/>
      </div>
    </div> --}}

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

<!-- ===== MODAL MENU CAFE ===== -->
<div class="menu-modal" id="menuCafeModal">
  <div class="menu-modal-backdrop"></div>

  <div class="menu-modal-content">
    <button class="menu-modal-close" id="closeMenuCafe">&times;</button>

    <h2 class="gold">Menu Cafe</h2>
    <!-- FILTER KATEGORI -->
    <div class="menu-filter">
    <button class="filter-btn active" data-category="all">Semua</button>

    @foreach($categories as $cat)
        <button class="filter-btn" data-category="{{ $cat->id }}">
        {{ $cat->name }}
        </button>
    @endforeach
    </div>


    <div class="menu-grid">
      @foreach($menuProducts as $item)
      <div class="menu-card" data-category="{{ $item->category_id }}">
        @if($item->image)
          <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
        @endif

        <h4>{{ $item->name }}</h4>
        <p class="price">Rp {{ number_format($item->price,0,',','.') }}</p>

        @if($item->description)
          <small>{{ $item->description }}</small>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</div>

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

<script>
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;

    setInterval(() => {
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
    }, 5000);
</script>

<script>
    const navToggle = document.getElementById('navToggle');
    const navMenu   = document.getElementById('navMenu');

    navToggle.addEventListener('click', (e)=>{
    e.stopPropagation();
    navMenu.classList.toggle('active');
    });

    document.addEventListener('click', (e)=>{
    if(!navMenu.contains(e.target) && !navToggle.contains(e.target)){
        navMenu.classList.remove('active');
    }
    });
</script>

<script>
    const openMenuCafe  = document.getElementById('openMenuCafe');
    const menuCafeModal = document.getElementById('menuCafeModal');
    const closeMenuCafe = document.getElementById('closeMenuCafe');

    openMenuCafe.addEventListener('click', (e)=>{
    e.preventDefault();
    menuCafeModal.classList.add('active');
    navMenu.classList.remove('active'); // tutup hamburger
    });

    closeMenuCafe.addEventListener('click', ()=>{
    menuCafeModal.classList.remove('active');
    });

    menuCafeModal.querySelector('.menu-modal-backdrop')
    .addEventListener('click', ()=>{
        menuCafeModal.classList.remove('active');
    });
</script>

<script>
    const filterButtons = document.querySelectorAll('.menu-filter .filter-btn');
    const menuCards = document.querySelectorAll('.menu-card');

    filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {

        // aktifkan tombol
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const category = btn.dataset.category;

        menuCards.forEach(card => {
        if(category === 'all' || card.dataset.category === category){
            card.style.display = 'block';
        }else{
            card.style.display = 'none';
        }
        });

    });
    });
</script>



</body>
</html>
