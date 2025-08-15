<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Arch Coffee Manajemen</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;800&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>

  <!-- AOS (Animate On Scroll) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#0a0f12;
      --bg2:#0f2027;
      --neon:#00faff;
      --neon-soft:#00d7e6;
      --text:#e8f6fc;
      --muted:#a9c1c9;
    }

    /* ===== Base ===== */
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:'Poppins',sans-serif;
      color:var(--text);
      background: radial-gradient(1000px 700px at 10% -10%, #072129 0%, transparent 60%),
                  radial-gradient(800px 600px at 90% 10%, #052028 0%, transparent 65%),
                  linear-gradient(180deg,var(--bg) 0%, #0b1217 100%);
      overflow-x:hidden;
    }

    a{text-decoration:none}

    /* ===== Glow helpers ===== */
    .neon{
      color:var(--neon);
      text-shadow:0 0 8px var(--neon),0 0 18px var(--neon-soft);
    }
    .soft-glow{
      box-shadow:0 0 12px var(--neon-soft),0 0 28px rgba(0,250,255,.35);
    }

    /* ===== Nav (glass sticky) ===== */
    .nav{
      position:sticky;top:0;z-index:50;
      backdrop-filter: blur(12px);
      background:rgba(7,16,20,.45);
      border-bottom:1px solid rgba(255,255,255,.06);
    }
    .nav-inner{
      max-width:1200px;margin:auto;
      display:flex;align-items:center;justify-content:space-between;
      padding:14px 22px;
    }
    .brand{font-family:'Orbitron',sans-serif;letter-spacing:.12em}
    .nav-links a{
      color:var(--text);opacity:.9;margin-left:18px;font-weight:600;
      transition:.25s ease;
    }
    .nav-links a:hover{color:var(--neon);text-shadow:0 0 8px var(--neon)}

    /* ===== Parallax decor ===== */
    .orb, .orb2{
      position:fixed;inset:auto;z-index:-1;pointer-events:none;
      filter: blur(50px);opacity:.2;transform:translateZ(0);
      transition: transform .2s linear;
    }
    .orb{top:10vh;left:-10vw;width:45vmax;height:45vmax;background:radial-gradient(circle at 30% 30%, rgba(0,255,255,.55), transparent 60%)}
    .orb2{bottom:-10vh;right:-8vw;width:50vmax;height:50vmax;background:radial-gradient(circle at 70% 70%, rgba(0,180,255,.45), transparent 60%)}

    /* ===== Hero ===== */
    .hero{
      position:relative;min-height:92vh;display:flex;align-items:center;
      padding:64px 22px;
    }
    .hero-inner{
      max-width:1200px;margin:auto;
      display:flex;align-items:center;justify-content:space-between;gap:36px;flex-wrap:wrap;
    }
    .hero-text{flex:1 1 480px}
    .hero-title{
      font-family:'Orbitron',sans-serif;font-weight:800;line-height:1.05;margin:0 0 10px;
      font-size:clamp(2.4rem, 4.2vw + 1rem, 4.6rem);
    }
    .hero-sub{color:var(--muted);line-height:1.7;max-width:720px}

    /* CTA button with dynamic hover + ripple */
    .btn-cta{
      position:relative;display:inline-block;margin-top:22px;padding:14px 28px;border-radius:40px;
      color:#071418;background:var(--neon);font-weight:700;letter-spacing:.02em;
      transition:transform .2s ease, box-shadow .25s ease, background .25s ease;
      box-shadow:0 0 0 rgba(0,250,255,0);
      overflow:hidden;
    }
    .btn-cta:hover{
      transform:translateY(-3px);
      box-shadow:0 12px 28px rgba(0,250,255,.22),0 0 20px rgba(0,250,255,.5);
      background:#7ff9ff;
    }
    .btn-cta:after{
      content:"";position:absolute;inset:auto;left:0;right:0;top:0;height:0;
      background:radial-gradient(circle at var(--mx,50%) -10px, rgba(255,255,255,.85), transparent 35%);
      transition:height .25s ease;
    }
    .btn-cta:hover:after{height:140%}

    /* Logo panel (glass + glow) */
    .hero-visual{
      flex:0 0 auto;max-width:46%;min-width:320px;
    }
    .logo-panel{
      background:linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.1);
      border-radius:16px;padding:14px;
      backdrop-filter: blur(6px);
      transition:transform .25s ease, box-shadow .25s ease;
      box-shadow: 0 0 24px rgba(0,250,255,.25), inset 0 0 0 1px rgba(255,255,255,.05);
    }
    .logo-panel:hover{transform:translateY(-4px);box-shadow:0 0 40px rgba(0,250,255,.4)}
    .logo-img{
      width:100%;height:auto;display:block;border-radius:12px;
      /* pastikan logo tidak gelap */
      filter: drop-shadow(0 0 10px rgba(0,250,255,.6)) drop-shadow(0 0 18px rgba(0,250,255,.35)) brightness(1.02) contrast(1.05);
    }

    /* ===== Map ===== */
    .map-wrap{padding:40px 22px 70px}
    .map-inner{max-width:1200px;margin:auto;text-align:center}
    .map-title{
      font-family:'Orbitron',sans-serif;margin:0 0 18px;
      font-size:clamp(1.4rem, 1vw + 1rem, 2rem)
    }
    .map-frame{
      width:100%;height:460px;border:0;border-radius:14px;
      box-shadow:0 0 20px rgba(0,250,255,.3), 0 0 50px rgba(0,250,255,.15);
      transition: box-shadow .3s ease, transform .25s ease;
    }
    .map-frame:hover{box-shadow:0 0 30px rgba(0,255,255,.6),0 0 70px rgba(0,255,255,.35); transform:translateY(-3px)}

    /* ===== Footer ===== */
    .foot{padding:36px 22px;border-top:1px solid rgba(255,255,255,.06);text-align:center;color:var(--muted)}

    /* ===== Responsive ===== */
    @media (max-width: 900px){
      .hero-inner{flex-direction:column}
      .hero-visual{order:-1;max-width:86%}
      .hero-text{text-align:center}
    }

    /* Respect reduced motion */
    @media (prefers-reduced-motion: reduce){
      .orb,.orb2,.btn-cta,.logo-panel,.map-frame{transition:none}
    }
  </style>
</head>
<body>

  <!-- Parallax decorative glows -->
  <div class="orb" aria-hidden="true"></div>
  <div class="orb2" aria-hidden="true"></div>

  <!-- NAV -->
  <nav class="nav">
    <div class="nav-inner">
      <div class="brand neon">ARCH COFFEE MANAJEMEN</div>
      <div class="nav-links">
        <a href="https://www.instagram.com/_archcoffee/" target="_blank" rel="noopener">Instagram</a>
        <a href="https://www.instagram.com/ewin.lntp/" target="_blank" rel="noopener">Programmer</a>
        <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s" target="_blank" rel="noopener">Tutorial</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <!-- Text -->
      <div class="hero-text" data-aos="fade-right">
        <h1 class="hero-title neon">HELLO,Coffee Lovers!</h1>
        <p class="hero-sub">
          Nikmati kopi istimewa sambil memandang ombak dan merasakan semilir angin laut di Arch Coffee.
          Cocok untuk bekerja, bersantai, atau berkumpul bersama teman. Kami juga menyajikan hidangan ringan hingga makanan berat untuk melengkapi waktu santai Anda.<Br><Br>Lokasi di Kompleks Wisata Pantai Batu Pinagut Kabupaten Bolaang Mongondow Utara. Scroll ke bawah untuk melihat lokasi Kami di peta.
        </p>
        <a href="{{ route('login') }}" class="btn-cta" id="loginBtn">Login</a>
      </div>

      <!-- Visual / Logo (tetap di kanan di desktop) -->
      <div class="hero-visual" data-aos="fade-left">
        <div class="logo-panel soft-glow">
          <img src="{{ asset('img/logo_arch_landing.png') }}" alt="Logo Arch Coffee" class="logo-img"/>
        </div>
      </div>
    </div>
  </section>

  <!-- MAP -->
  <section class="map-wrap">
    <div class="map-inner" data-aos="zoom-in">
      <h2 class="map-title neon">Lokasi Arch Coffee</h2>
      <iframe
        class="map-frame"
        src="https://maps.google.com/maps?q=0.9153337773,123.2724709&z=18&output=embed"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </section>

  <footer class="foot">© {{ date('Y') }} Arch Coffee. All rights reserved.</footer>

  <!-- AOS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

  <script>
    // Init AOS
    AOS.init({ duration: 1100, once: true, offset: 90 });

    // Button hover ripple follows mouse
    const btn = document.getElementById('loginBtn');
    if (btn) {
      btn.addEventListener('mousemove', (e) => {
        const r = btn.getBoundingClientRect();
        const mx = ((e.clientX - r.left) / r.width) * 100;
        btn.style.setProperty('--mx', mx + '%');
      });
    }

    // Lightweight parallax on orbs & hero based on scroll
    const orb = document.querySelector('.orb');
    const orb2 = document.querySelector('.orb2');
    const parallax = () => {
      const y = window.scrollY || window.pageYOffset;
      if (orb)  orb.style.transform  = `translateY(${y * .15}px)`;
      if (orb2) orb2.style.transform = `translateY(${y * -.10}px)`;
    };
    parallax();
    window.addEventListener('scroll', parallax, {passive:true});

    // Subtle parallax tilt on mouse for logo panel
    const panel = document.querySelector('.logo-panel');
    if (panel) {
      panel.addEventListener('mousemove', (e) => {
        const r = panel.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width/2) / r.width;
        const y = (e.clientY - r.top  - r.height/2) / r.height;
        panel.style.transform = `rotateX(${ -y * 6 }deg) rotateY(${ x * 8 }deg) translateY(-2px)`;
      });
      panel.addEventListener('mouseleave', () => {
        panel.style.transform = '';
      });
    }
  </script>
</body>
</html>
