@php
    $rp = fn ($v) => number_format((float) $v, 0, ',', '.');
    $slides = collect($sliderProducts)->filter(fn ($p) => !empty($p->image))->values();
    $topIds = $topProductIds ?? [];
    $maxQty = max(1, (int) collect($topProducts)->max('total_qty'));
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=0.9153337773,123.2724709';
    $slideData = json_encode($slides->map(fn ($p) => [
        'name' => $p->name,
        'price' => 'Rp ' . number_format((float) $p->price, 0, ',', '.'),
        'rank' => in_array($p->id, $topIds) ? array_search($p->id, $topIds) + 1 : null,
    ])->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1A110D">
    <meta name="description" content="ARCH Coffee — kopi di tepi pantai Batu Pinagut, Boroko Timur, Bolaang Mongondow Utara. Lihat menu, minuman terlaris, dan lokasi kami.">
    <meta property="og:title" content="ARCH Coffee — Batu Pinagut">
    <meta property="og:description" content="Kopi, ombak, dan angin laut di Pantai Batu Pinagut, Bolaang Mongondow Utara.">
    @if ($slides->first())
        <meta property="og:image" content="{{ asset($slides->first()->image) }}">
    @endif
    <title>ARCH Coffee — Kopi di tepi pantai Batu Pinagut</title>
    <link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT@0,9..144,400..700,0..100;1,9..144,400..600,0..100&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --night: #140D0A;
            --roast: #1E1410;
            --roast-2: #2B1D17;
            --roast-3: #3A2A22;
            --porcelain: #EEF0EC;
            --paper: #FBFAF7;
            --ink: #221A16;
            --ink-2: #4A403A;
            --muted: #7A706A;
            --cream: #EADFD5;
            --sand: #A8968A;
            --line: rgba(234, 223, 213, .14);
            --line-dark: #DEDFD9;
            --red: #D7141A;
            --red-600: #B70F14;
            --gold: #C9A15A;
            --display: 'Fraunces', Georgia, serif;
            --ui: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --wrap: 1200px;
            --ease: cubic-bezier(.2, .8, .2, 1);
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
        body {
            margin: 0; font-family: var(--ui); font-size: 16px; line-height: 1.6;
            color: var(--ink); background: var(--porcelain);
            -webkit-font-smoothing: antialiased; font-feature-settings: "tnum" 1;
        }
        img { max-width: 100%; display: block; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; cursor: pointer; }
        :focus-visible { outline: 2px solid var(--red); outline-offset: 3px; border-radius: 6px; }
        .wrap { width: 100%; max-width: var(--wrap); margin: 0 auto; padding: 0 24px; }
        .gate[hidden], .sound[hidden], .dish[hidden], .menu__more[hidden] { display: none !important; }
        .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            height: 50px; padding: 0 24px; border-radius: 999px; border: 1px solid transparent;
            font-weight: 600; font-size: 15px; white-space: nowrap;
            transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s var(--ease);
        }
        .btn:active { transform: scale(.98); }
        .btn--red { background: var(--red); color: #fff; }
        .btn--red:hover { background: var(--red-600); }
        .btn--ghost { color: #fff; border-color: rgba(255, 255, 255, .35); background: rgba(255, 255, 255, .04); }
        .btn--ghost:hover { border-color: #fff; background: rgba(255, 255, 255, .1); }
        .btn--dark { background: var(--roast-2); color: #fff; }
        .btn--dark:hover { background: var(--roast-3); }
        .btn--line { color: var(--ink); border-color: var(--line-dark); background: #fff; }
        .btn--line:hover { border-color: var(--ink-2); }

        /* ---------- Navigasi ---------- */
        .nav {
            position: fixed; inset: 0 0 auto 0; z-index: 50;
            transition: background .3s ease, border-color .3s ease, backdrop-filter .3s ease;
            border-bottom: 1px solid transparent;
        }
        .nav.is-solid {
            background: rgba(20, 13, 10, .82);
            -webkit-backdrop-filter: saturate(150%) blur(14px); backdrop-filter: saturate(150%) blur(14px);
            border-bottom-color: var(--line);
        }
        .nav__inner { height: 76px; display: flex; align-items: center; gap: 28px; }
        .brand { display: flex; align-items: center; gap: 12px; color: #fff; }
        .brand img { width: 40px; height: 40px; border-radius: 50%; }
        .brand strong { font-family: var(--display); font-size: 22px; font-weight: 600; letter-spacing: .01em; }
        .nav__links { display: flex; gap: 4px; margin-left: auto; }
        .nav__links a {
            padding: 8px 14px; border-radius: 999px; color: rgba(255, 255, 255, .82); font-size: 14.5px; font-weight: 500;
            transition: color .2s ease, background .2s ease;
        }
        .nav__links a:hover { color: #fff; background: rgba(255, 255, 255, .08); }
        .nav__cta { height: 42px; padding: 0 18px; font-size: 14px; }
        .nav__burger {
            display: none; margin-left: auto; width: 44px; height: 44px; border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .22); background: rgba(255, 255, 255, .04); color: #fff;
        }

        .drawer {
            position: fixed; inset: 0; z-index: 60; pointer-events: none;
        }
        .drawer__backdrop { position: absolute; inset: 0; background: rgba(10, 6, 4, .6); opacity: 0; transition: opacity .3s ease; }
        .drawer__panel {
            position: absolute; top: 0; right: 0; bottom: 0; width: min(86vw, 360px);
            background: var(--roast); color: var(--cream); padding: 22px 24px 28px;
            display: flex; flex-direction: column; gap: 6px;
            transform: translateX(100%); transition: transform .35s var(--ease);
        }
        .drawer.is-open { pointer-events: auto; }
        .drawer.is-open .drawer__backdrop { opacity: 1; }
        .drawer.is-open .drawer__panel { transform: none; }
        .drawer__head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .drawer__close { width: 44px; height: 44px; border-radius: 12px; border: 1px solid var(--line); background: transparent; color: #fff; font-size: 18px; }
        .drawer__panel > a:not(.btn) {
            padding: 14px 4px; border-bottom: 1px solid var(--line);
            font-family: var(--display); font-size: 24px; color: #fff;
            display: flex; justify-content: space-between; align-items: center;
        }
        .drawer__panel > a:not(.btn) i { font-size: 14px; color: var(--sand); }
        .drawer__panel .btn { margin-top: auto; }

        /* ---------- Hero ---------- */
        .hero {
            position: relative; min-height: 100svh; min-height: 100vh;
            display: flex; align-items: flex-end;
            background: var(--night); color: #fff; overflow: hidden; isolation: isolate;
        }
        .hero__slides { position: absolute; inset: 0; z-index: -2; }
        .hero__slide {
            position: absolute; inset: 0; background-size: cover; background-position: center;
            opacity: 0; transform: scale(1.08);
            transition: opacity 1.4s ease, transform 7s linear;
        }
        .hero__slide.is-active { opacity: 1; transform: scale(1); }
        .hero::before {
            content: ""; position: absolute; inset: 0; z-index: -1;
            background:
                linear-gradient(180deg, rgba(20, 13, 10, .55) 0%, rgba(20, 13, 10, 0) 26%),
                linear-gradient(0deg, rgba(20, 13, 10, .96) 0%, rgba(20, 13, 10, .72) 34%, rgba(20, 13, 10, .18) 70%),
                linear-gradient(90deg, rgba(20, 13, 10, .7) 0%, rgba(20, 13, 10, 0) 60%);
        }
        .hero--empty { background: radial-gradient(800px 500px at 20% 20%, rgba(215, 20, 26, .28), transparent 60%), var(--night); }

        .hero__inner { width: 100%; padding-top: 140px; padding-bottom: 56px; }
        .hero__grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(0, .65fr); gap: 48px; align-items: end; }
        .hero__title {
            margin: 0; font-family: var(--display); font-weight: 500;
            font-size: clamp(46px, 7.4vw, 108px); line-height: .98; letter-spacing: -.03em;
            font-variation-settings: "opsz" 144, "SOFT" 40;
            text-wrap: balance;
        }
        .hero__title em { font-style: italic; font-weight: 400; color: var(--cream); }
        .hero__lead { margin: 26px 0 0; max-width: 46ch; font-size: 17px; color: rgba(234, 223, 213, .86); }
        .hero__actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 32px; }

        .now {
            position: relative; padding: 22px 22px 18px; border-radius: 22px;
            background: rgba(30, 20, 16, .55); border: 1px solid var(--line);
            -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px);
        }
        .now__seal {
            position: absolute; top: -18px; right: 20px;
            width: 64px; height: 64px; border-radius: 50%;
            display: grid; place-items: center; text-align: center;
            background: var(--red); color: #fff; line-height: 1;
            box-shadow: 0 0 0 6px rgba(215, 20, 26, .18);
            transform: rotate(-8deg); transition: transform .4s var(--ease), opacity .3s ease;
        }
        .now__seal small { display: block; font-size: 10px; font-weight: 600; opacity: .85; }
        .now__seal b { display: block; font-family: var(--display); font-size: 24px; font-weight: 600; }
        .now__seal[hidden] { display: grid; opacity: 0; transform: rotate(-30deg) scale(.6); }
        .now__label { font-size: 13px; color: var(--sand); }
        .now__name {
            margin: 4px 0 2px; font-family: var(--display); font-size: 28px; font-weight: 500; line-height: 1.15;
            min-height: 2.3em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .now__price { font-size: 17px; font-weight: 600; color: var(--gold); }
        .now__bar { display: flex; align-items: center; gap: 12px; margin-top: 18px; }
        .now__track { flex: 1; height: 2px; background: rgba(255, 255, 255, .16); border-radius: 2px; overflow: hidden; }
        .now__fill { height: 100%; width: 0; background: #fff; }
        .now__count { font-size: 13px; color: var(--sand); font-variant-numeric: tabular-nums; }
        .now__nav { width: 36px; height: 36px; border-radius: 50%; border: 1px solid var(--line); background: transparent; color: #fff; }
        .now__nav:hover { background: rgba(255, 255, 255, .1); }

        .hero__meta {
            display: flex; gap: 28px; flex-wrap: wrap; margin-top: 48px; padding-top: 22px;
            border-top: 1px solid var(--line); color: rgba(234, 223, 213, .8); font-size: 14px;
        }
        .hero__meta span { display: inline-flex; align-items: center; gap: 10px; }
        .hero__meta i { color: var(--gold); }

        /* Masuk halaman: satu urutan animasi */
        .rise { opacity: 0; transform: translateY(18px); animation: rise .9s var(--ease) forwards; }
        .rise.d1 { animation-delay: .1s; } .rise.d2 { animation-delay: .22s; } .rise.d3 { animation-delay: .34s; } .rise.d4 { animation-delay: .5s; }
        @keyframes rise { to { opacity: 1; transform: none; } }

        /* ---------- Section umum ---------- */
        .section { padding: 112px 0; }
        .section__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap; margin-bottom: 44px; }
        .section__title {
            margin: 0; font-family: var(--display); font-weight: 500;
            font-size: clamp(34px, 4.4vw, 60px); line-height: 1.02; letter-spacing: -.02em;
            font-variation-settings: "opsz" 144, "SOFT" 30;
        }
        .section__lead { margin: 14px 0 0; max-width: 52ch; color: var(--muted); font-size: 16.5px; }

        /* ---------- Terlaris ---------- */
        .best { background: var(--roast); color: var(--cream); position: relative; overflow: hidden; }
        .best .section__title { color: #fff; }
        .best .section__lead { color: var(--sand); }
        .range { display: inline-flex; padding: 4px; gap: 4px; border-radius: 999px; background: rgba(255, 255, 255, .06); border: 1px solid var(--line); }
        .range a { padding: 8px 16px; border-radius: 999px; font-size: 14px; font-weight: 600; color: var(--sand); transition: color .2s ease, background .2s ease; }
        .range a:hover { color: #fff; }
        .range a.is-on { background: #fff; color: var(--ink); }

        .best__grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 48px; align-items: start; }
        .champ { position: relative; border-radius: 26px; overflow: hidden; aspect-ratio: 4 / 5; background: var(--roast-2); }
        .champ img { width: 100%; height: 100%; object-fit: cover; }
        .champ::after { content: ""; position: absolute; inset: 0; background: linear-gradient(0deg, rgba(20, 13, 10, .92) 0%, rgba(20, 13, 10, 0) 55%); }
        .champ__body { position: absolute; left: 28px; right: 28px; bottom: 26px; z-index: 1; }
        .champ__rank { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: var(--red); color: #fff; font-size: 13px; font-weight: 700; }
        .champ__name { margin: 14px 0 4px; font-family: var(--display); font-size: clamp(30px, 3.2vw, 44px); line-height: 1.05; color: #fff; font-weight: 500; }
        .champ__meta { color: var(--cream); font-size: 15px; }
        .champ__meta b { color: var(--gold); }
        .champ--empty { display: grid; place-items: center; color: var(--sand); text-align: center; padding: 24px; }

        /* struk terlaris */
        .receipt {
            position: relative; background: var(--paper); color: var(--ink);
            border-radius: 6px 6px 0 0; padding: 30px 30px 34px;
            box-shadow: 0 40px 80px -30px rgba(0, 0, 0, .7);
            transform: rotate(.4deg);
        }
        .receipt::after {
            content: ""; position: absolute; left: 0; right: 0; bottom: -12px; height: 12px;
            background:
                linear-gradient(-45deg, transparent 8px, var(--paper) 0) 0 0 / 16px 12px repeat-x,
                linear-gradient(45deg, transparent 8px, var(--paper) 0) 0 0 / 16px 12px repeat-x;
        }
        .receipt__head { text-align: center; padding-bottom: 18px; border-bottom: 1.5px dashed #CFCBC4; }
        .receipt__head img { width: 44px; height: 44px; border-radius: 50%; margin: 0 auto 10px; }
        .receipt__head strong { display: block; font-family: var(--display); font-size: 22px; font-weight: 600; }
        .receipt__head span { font-size: 13px; color: var(--muted); }
        .receipt ol { list-style: none; margin: 0; padding: 0; }
        .receipt li {
            display: grid; grid-template-columns: 30px minmax(0, 1fr) auto; gap: 2px 12px; align-items: baseline;
            padding: 14px 0; border-bottom: 1px dashed #E2DED7;
        }
        .receipt li:last-child { border-bottom: 0; }
        .receipt__no { font-family: var(--display); font-size: 18px; color: var(--muted); }
        .receipt li:nth-child(-n+3) .receipt__no { color: var(--red); font-weight: 600; }
        .receipt__item { font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .receipt__qty { font-weight: 700; font-variant-numeric: tabular-nums; }
        .receipt__sub { grid-column: 2 / 4; display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--muted); }
        .receipt__bar { flex: 1; height: 3px; background: #ECE9E3; border-radius: 3px; overflow: hidden; }
        .receipt__bar span { display: block; height: 100%; background: var(--roast-3); border-radius: inherit; }
        .receipt li:first-child .receipt__bar span { background: var(--red); }
        .receipt__foot { margin-top: 16px; padding-top: 16px; border-top: 1.5px dashed #CFCBC4; text-align: center; font-size: 13px; color: var(--muted); }
        .receipt__empty { padding: 36px 0; text-align: center; color: var(--muted); }

        /* ---------- Menu ---------- */
        .menu { background: var(--porcelain); }
        .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px; }
        .tab {
            height: 42px; padding: 0 18px; border-radius: 999px; border: 1px solid var(--line-dark);
            background: #fff; color: var(--ink-2); font-weight: 600; font-size: 14px;
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }
        .tab:hover { border-color: var(--ink-2); color: var(--ink); }
        .tab.is-on { background: var(--roast-2); border-color: var(--roast-2); color: #fff; }
        .tab span { opacity: .6; font-weight: 500; margin-left: 4px; }

        .menu__grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 22px; }
        .dish {
            display: flex; flex-direction: column; background: #fff; border-radius: 20px; overflow: hidden;
            border: 1px solid var(--line-dark);
            transition: transform .3s var(--ease), box-shadow .3s ease;
        }
        .dish:hover { transform: translateY(-3px); box-shadow: 0 24px 48px -24px rgba(34, 26, 22, .35); }
        .dish[hidden] { display: none; }
        .dish__media { position: relative; aspect-ratio: 4 / 3; background: #E4E7E2; overflow: hidden; }
        .dish__media img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s var(--ease); }
        .dish:hover .dish__media img { transform: scale(1.04); }
        .dish__ph { position: absolute; inset: 0; display: grid; place-items: center; color: #B4B1AA; font-size: 28px; }
        .dish__tag { position: absolute; top: 12px; left: 12px; padding: 5px 10px; border-radius: 999px; background: var(--red); color: #fff; font-size: 12px; font-weight: 700; }
        .dish__body { padding: 18px 18px 20px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
        .dish__row { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; }
        .dish__name { margin: 0; font-family: var(--display); font-size: 21px; font-weight: 500; line-height: 1.2; }
        .dish__price { font-weight: 700; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .dish__desc { margin: 0; font-size: 14px; color: var(--muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .menu__more { display: flex; justify-content: center; margin-top: 36px; }

        /* ---------- Lokasi ---------- */
        .visit { background: var(--paper); }
        .visit__grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); gap: 40px; align-items: stretch; }
        .visit__info { display: flex; flex-direction: column; }
        .facts { list-style: none; margin: 32px 0 0; padding: 0; border-top: 1px solid var(--line-dark); }
        .facts li { display: grid; grid-template-columns: 40px 1fr; gap: 14px; padding: 18px 0; border-bottom: 1px solid var(--line-dark); }
        .facts i { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: var(--porcelain); color: var(--red); }
        .facts strong { display: block; font-size: 15px; }
        .facts span, .facts a { font-size: 14.5px; color: var(--muted); }
        .facts a:hover { color: var(--ink); }
        .visit__actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; }
        .map { position: relative; min-height: 460px; border-radius: 26px; overflow: hidden; background: #E4E7E2; border: 1px solid var(--line-dark); }
        .map iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; filter: saturate(.85) contrast(1.02); }

        /* ---------- Footer ---------- */
        .foot { background: var(--night); color: var(--sand); padding: 64px 0 36px; }
        .foot__top { display: flex; justify-content: space-between; align-items: flex-start; gap: 32px; flex-wrap: wrap; padding-bottom: 36px; border-bottom: 1px solid var(--line); }
        .foot__claim { margin: 18px 0 0; max-width: 30ch; font-family: var(--display); font-size: 26px; line-height: 1.2; color: #fff; font-weight: 400; }
        .foot__links { display: grid; grid-template-columns: repeat(2, minmax(140px, auto)); gap: 10px 48px; font-size: 15px; }
        .foot__links a { color: var(--cream); display: inline-flex; align-items: center; gap: 10px; }
        .foot__links a:hover { color: #fff; }
        .foot__links i { width: 16px; color: var(--sand); }
        .foot__bottom { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; padding-top: 24px; font-size: 13.5px; }

        /* ---------- Sambutan & musik ---------- */
        .gate {
            position: fixed; inset: 0; z-index: 100; display: grid; place-items: center; padding: 24px;
            background: radial-gradient(700px 500px at 50% 35%, rgba(215, 20, 26, .2), transparent 65%), rgba(20, 13, 10, .97);
            color: #fff; text-align: center; transition: opacity .7s ease, visibility .7s ease;
        }
        .gate.is-gone { opacity: 0; visibility: hidden; }
        .gate img { width: 76px; height: 76px; border-radius: 50%; margin: 0 auto 22px; box-shadow: 0 0 0 10px rgba(215, 20, 26, .14); }
        .gate h2 { margin: 0; font-family: var(--display); font-weight: 500; font-size: clamp(32px, 5vw, 48px); line-height: 1.08; letter-spacing: -.02em; }
        .gate p { margin: 14px auto 28px; max-width: 38ch; color: var(--sand); }
        .gate__actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .gate .skip { background: none; border: 0; color: var(--sand); font-size: 14px; text-decoration: underline; text-underline-offset: 4px; margin-top: 16px; }
        .gate .skip:hover { color: #fff; }

        .sound {
            position: fixed; right: 20px; bottom: 20px; z-index: 40;
            display: inline-flex; align-items: center; gap: 10px;
            height: 46px; padding: 0 16px 0 12px; border-radius: 999px;
            background: rgba(20, 13, 10, .82); color: #fff; border: 1px solid var(--line);
            -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px);
            font-size: 13.5px; font-weight: 600;
        }
        .eq { display: inline-flex; align-items: flex-end; gap: 2px; width: 18px; height: 14px; }
        .eq i { width: 3px; height: 4px; background: var(--gold); border-radius: 1px; }
        .sound.is-playing .eq i { animation: eq 1s ease-in-out infinite; }
        .sound.is-playing .eq i:nth-child(2) { animation-delay: .2s; }
        .sound.is-playing .eq i:nth-child(3) { animation-delay: .4s; }
        .sound.is-playing .eq i:nth-child(4) { animation-delay: .1s; }
        @keyframes eq { 0%, 100% { height: 4px; } 50% { height: 14px; } }

        /* ---------- Responsif ---------- */
        @media (max-width: 991px) {
            .nav__links, .nav__cta { display: none; }
            .nav__burger { display: inline-grid; place-items: center; }
            .hero__grid { grid-template-columns: 1fr; gap: 32px; }
            .best__grid, .visit__grid { grid-template-columns: 1fr; }
            .champ { aspect-ratio: 16 / 11; }
            .receipt { transform: none; }
            .section { padding: 84px 0; }
        }
        @media (max-width: 575px) {
            .wrap { padding: 0 18px; }
            .hero__inner { padding-top: 112px; padding-bottom: 36px; }
            .hero__lead { font-size: 16px; }
            .hero__actions .btn { flex: 1 1 auto; }
            .hero__meta { gap: 12px 22px; margin-top: 32px; }
            .receipt { padding: 24px 20px 28px; }
            .map { min-height: 340px; }
            .foot__links { grid-template-columns: 1fr; }
            .sound span.sound__text { display: none; }
            .sound { padding: 0 14px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .rise { opacity: 1; transform: none; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>

<body>
    {{-- Sambutan: muncul sekali per sesi, memberi izin memutar musik --}}
    <div class="gate" id="gate" role="dialog" aria-modal="true" aria-labelledby="gateTitle" hidden>
        <div>
            <img src="{{ asset('img/logo_arch_web.png') }}" alt="ARCH Coffee">
            <h2 id="gateTitle">Selamat datang di ARCH Coffee</h2>
            <p>Duduk santai, dengarkan ombak, dan temukan kopi favorit Anda hari ini.</p>
            <div class="gate__actions">
                <button type="button" class="btn btn--red" data-enter="music"><i class="fas fa-music"></i> Masuk dengan musik</button>
            </div>
            <button type="button" class="skip" data-enter="silent">Masuk tanpa musik</button>
        </div>
    </div>

    <audio id="bgAudio" src="{{ asset('audio/ARCH11.mp3') }}" preload="none" loop></audio>

    {{-- Navigasi --}}
    <header class="nav" id="nav">
        <div class="wrap nav__inner">
            <a href="#top" class="brand" aria-label="ARCH Coffee, ke atas">
                <img src="{{ asset('img/logo_arch_web.png') }}" alt="">
                <strong>ARCH Coffee</strong>
            </a>
            <nav class="nav__links" aria-label="Navigasi">
                <a href="#terlaris">Terlaris</a>
                <a href="#menu">Menu</a>
                <a href="#lokasi">Lokasi</a>
                <a href="https://www.instagram.com/_archcoffee/" target="_blank" rel="noopener">Instagram</a>
            </nav>
            <a href="{{ route('login') }}" class="btn btn--ghost nav__cta"><i class="fas fa-lock"></i> Masuk staf</a>
            <button type="button" class="nav__burger" id="openDrawer" aria-label="Buka menu navigasi" aria-controls="drawer" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <div class="drawer" id="drawer" aria-hidden="true">
        <div class="drawer__backdrop" data-close-drawer></div>
        <div class="drawer__panel" role="dialog" aria-label="Menu navigasi">
            <div class="drawer__head">
                <span class="brand"><img src="{{ asset('img/logo_arch_web.png') }}" alt=""><strong>ARCH</strong></span>
                <button type="button" class="drawer__close" data-close-drawer aria-label="Tutup menu"><i class="fas fa-xmark"></i></button>
            </div>
            <a href="#terlaris" data-close-drawer>Terlaris <i class="fas fa-arrow-down"></i></a>
            <a href="#menu" data-close-drawer>Menu <i class="fas fa-arrow-down"></i></a>
            <a href="#lokasi" data-close-drawer>Lokasi <i class="fas fa-arrow-down"></i></a>
            <a href="https://www.instagram.com/_archcoffee/" target="_blank" rel="noopener">Instagram <i class="fas fa-arrow-up-right-from-square"></i></a>
            <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s" target="_blank" rel="noopener">Tutorial <i class="fas fa-arrow-up-right-from-square"></i></a>
            <a href="{{ route('login') }}" class="btn btn--red"><i class="fas fa-lock"></i> Masuk staf</a>
        </div>
    </div>

    {{-- Hero --}}
    <section class="hero {{ $slides->isEmpty() ? 'hero--empty' : '' }}" id="top">
        <div class="hero__slides" aria-hidden="true">
            @foreach ($slides as $i => $product)
                <div class="hero__slide {{ $i === 0 ? 'is-active' : '' }}"
                    @if ($i === 0) style="background-image:url('{{ asset($product->image) }}')"
                    @else data-bg="{{ asset($product->image) }}" @endif></div>
            @endforeach
        </div>

        <div class="wrap hero__inner">
            <div class="hero__grid">
                <div>
                    <h1 class="hero__title rise d1">Kopi, ombak, <em>dan angin laut</em> Batu Pinagut.</h1>
                    <p class="hero__lead rise d2">
                        Nikmati kopi istimewa sambil memandang ombak di Kompleks Wisata Pantai Batu Pinagut,
                        Kabupaten Bolaang Mongondow Utara.
                    </p>
                    <div class="hero__actions rise d3">
                        <a href="#menu" class="btn btn--red">Lihat menu</a>
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="btn btn--ghost"><i class="fas fa-location-arrow"></i> Petunjuk arah</a>
                    </div>
                </div>

                @if ($slides->isNotEmpty())
                    @php
                        $first = $slides->first();
                        $firstRank = in_array($first->id, $topIds) ? array_search($first->id, $topIds) + 1 : null;
                    @endphp
                    <aside class="now rise d4" aria-live="polite">
                        <div class="now__seal" id="nowSeal" @unless ($firstRank) hidden @endunless>
                            <span><small>TOP</small><b id="nowRank">{{ $firstRank ?? 1 }}</b></span>
                        </div>
                        <div class="now__label">{{ $firstRank ? 'Favorit pelanggan' : 'Dari dapur & bar kami' }}</div>
                        <p class="now__name" id="nowName">{{ $first->name }}</p>
                        <div class="now__price" id="nowPrice">Rp {{ $rp($first->price) }}</div>
                        <div class="now__bar">
                            <button type="button" class="now__nav" data-slide="-1" aria-label="Menu sebelumnya"><i class="fas fa-chevron-left"></i></button>
                            <div class="now__track"><div class="now__fill" id="nowFill"></div></div>
                            <span class="now__count"><span id="nowIndex">1</span> / {{ $slides->count() }}</span>
                            <button type="button" class="now__nav" data-slide="1" aria-label="Menu berikutnya"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </aside>
                @endif
            </div>

            <div class="hero__meta rise d4">
                <span><i class="fas fa-location-dot"></i> Pantai Batu Pinagut, Boroko Timur</span>
                <span><i class="fas fa-mug-hot"></i> {{ count($menuProducts) }} menu kopi, minuman & makanan</span>
                <span><i class="fab fa-instagram"></i> @_archcoffee</span>
            </div>
        </div>
    </section>

    {{-- Terlaris --}}
    @php
        $champ = collect($topProducts)->first();
        $champImg = $champ->image ?? null;
        $rangeLabel = [7 => '7 hari', 30 => '30 hari', 90 => '90 hari'][$range] ?? $range . ' hari';
    @endphp
    <section class="section best" id="terlaris">
        <div class="wrap">
            <div class="section__head">
                <div>
                    <h2 class="section__title">Yang paling sering dipesan</h2>
                    <p class="section__lead">Diambil langsung dari mesin kasir kami, {{ $rangeLabel }} terakhir.</p>
                </div>
                <nav class="range" aria-label="Rentang waktu">
                    @foreach ([7 => '7 hari', 30 => '30 hari', 90 => '90 hari'] as $r => $label)
                        <a href="{{ route('landing', ['range' => $r]) }}#terlaris" class="{{ $range == $r ? 'is-on' : '' }}"
                            @if ($range == $r) aria-current="true" @endif>{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            <div class="best__grid">
                @if ($champ)
                    <figure class="champ" style="margin:0">
                        @if ($champImg)
                            <img src="{{ asset($champImg) }}" alt="{{ $champ->name }}" loading="lazy">
                        @endif
                        <figcaption class="champ__body">
                            <span class="champ__rank"><i class="fas fa-crown"></i> Nomor satu</span>
                            <h3 class="champ__name">{{ $champ->name }}</h3>
                            <div class="champ__meta"><b>Rp {{ $rp($champ->unit_price) }}</b> · {{ $rp($champ->total_qty) }} kali dipesan</div>
                        </figcaption>
                    </figure>
                @else
                    <div class="champ champ--empty">
                        <p>Belum ada pesanan pada rentang ini.<br>Coba pilih rentang waktu yang lebih panjang.</p>
                    </div>
                @endif

                <div class="receipt" aria-label="Daftar menu terlaris">
                    <div class="receipt__head">
                        <img src="{{ asset('img/logo_arch_web.png') }}" alt="">
                        <strong>ARCH Coffee</strong>
                        <span>Struk terlaris · {{ $rangeLabel }} · {{ now()->translatedFormat('d M Y') }}</span>
                    </div>
                    @if (collect($topProducts)->isEmpty())
                        <div class="receipt__empty">Belum ada data penjualan.</div>
                    @else
                        <ol>
                            @foreach ($topProducts as $i => $item)
                                <li>
                                    <span class="receipt__no">{{ $i + 1 }}</span>
                                    <span class="receipt__item">{{ $item->name }}</span>
                                    <span class="receipt__qty">{{ $rp($item->total_qty) }}×</span>
                                    <span class="receipt__sub">
                                        <span>Rp {{ $rp($item->unit_price) }}</span>
                                        <span class="receipt__bar" aria-hidden="true"><span style="width: {{ max(3, ($item->total_qty / $maxQty) * 100) }}%"></span></span>
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                    <div class="receipt__foot">Terima kasih sudah mampir</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Menu --}}
    @php
        $menuCounts = collect($menuProducts)->countBy('category_id');
        $initialLimit = 12;
    @endphp
    <section class="section menu" id="menu">
        <div class="wrap">
            <div class="section__head">
                <div>
                    <h2 class="section__title">Menu ARCH</h2>
                    <p class="section__lead">Semua harga dalam rupiah. Pesan langsung di kasir kami.</p>
                </div>
            </div>

            <div class="tabs" role="tablist" aria-label="Kategori menu">
                <button type="button" class="tab is-on" role="tab" aria-selected="true" data-cat="all">Semua <span>{{ count($menuProducts) }}</span></button>
                @foreach ($categories as $cat)
                    <button type="button" class="tab" role="tab" aria-selected="false" data-cat="{{ $cat->id }}">
                        {{ $cat->name }} <span>{{ $menuCounts[$cat->id] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>

            @if (collect($menuProducts)->isEmpty())
                <p class="section__lead">Menu sedang diperbarui. Silakan tanyakan langsung ke barista kami.</p>
            @else
                <div class="menu__grid" id="menuGrid">
                    @foreach ($menuProducts as $i => $item)
                        @php $rank = in_array($item->id, $topIds) ? array_search($item->id, $topIds) + 1 : null; @endphp
                        <article class="dish" data-cat="{{ $item->category_id }}" @if ($i >= $initialLimit) hidden data-more @endif>
                            <div class="dish__media">
                                @if ($item->image)
                                    <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" loading="lazy">
                                @else
                                    <span class="dish__ph" aria-hidden="true"><i class="fas fa-mug-hot"></i></span>
                                @endif
                                @if ($rank)
                                    <span class="dish__tag">Terlaris #{{ $rank }}</span>
                                @endif
                            </div>
                            <div class="dish__body">
                                <div class="dish__row">
                                    <h3 class="dish__name">{{ $item->name }}</h3>
                                    <span class="dish__price">Rp {{ $rp($item->price) }}</span>
                                </div>
                                @if ($item->description)
                                    <p class="dish__desc">{{ $item->description }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                @if (count($menuProducts) > $initialLimit)
                    <div class="menu__more">
                        <button type="button" class="btn btn--line" id="showAll">Tampilkan semua {{ count($menuProducts) }} menu</button>
                    </div>
                @endif
            @endif
        </div>
    </section>

    {{-- Lokasi --}}
    <section class="section visit" id="lokasi">
        <div class="wrap visit__grid">
            <div class="visit__info">
                <h2 class="section__title">Mampir ke tepi pantai</h2>
                <p class="section__lead">ARCH berada di dalam kompleks wisata Pantai Batu Pinagut, Boroko Timur. Datang untuk kopi, tinggal untuk senjanya.</p>
                <ul class="facts">
                    <li>
                        <i class="fas fa-location-dot"></i>
                        <div><strong>Kompleks Wisata Pantai Batu Pinagut</strong><span>Boroko Timur, Kaidipang, Kab. Bolaang Mongondow Utara</span></div>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <div><strong>Telepon</strong><a href="tel:+6282195110639">0821 9511 0639</a></div>
                    </li>
                    <li>
                        <i class="fab fa-instagram"></i>
                        <div><strong>Instagram</strong><a href="https://www.instagram.com/_archcoffee/" target="_blank" rel="noopener">@_archcoffee</a></div>
                    </li>
                </ul>
                <div class="visit__actions">
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="btn btn--dark"><i class="fas fa-location-arrow"></i> Buka di Google Maps</a>
                    <a href="tel:+6282195110639" class="btn btn--line"><i class="fas fa-phone"></i> Telepon</a>
                </div>
            </div>
            <div class="map">
                <iframe title="Peta lokasi ARCH Coffee"
                    src="https://maps.google.com/maps?q=0.9153337773,123.2724709&z=17&output=embed"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="foot">
        <div class="wrap">
            <div class="foot__top">
                <div>
                    <span class="brand"><img src="{{ asset('img/logo_arch_web.png') }}" alt=""><strong>ARCH Coffee</strong></span>
                    <p class="foot__claim">Sampai jumpa di tepi pantai.</p>
                </div>
                <div class="foot__links">
                    <a href="#menu"><i class="fas fa-mug-hot"></i> Menu</a>
                    <a href="https://www.instagram.com/_archcoffee/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i> Instagram</a>
                    <a href="#lokasi"><i class="fas fa-location-dot"></i> Lokasi</a>
                    <a href="https://www.youtube.com/watch?v=b13WkfMTXeU&t=105s" target="_blank" rel="noopener"><i class="fab fa-youtube"></i> Tutorial</a>
                    <a href="{{ route('login') }}"><i class="fas fa-lock"></i> Masuk staf</a>
                    <a href="https://www.instagram.com/ewin.lntp/" target="_blank" rel="noopener"><i class="fas fa-code"></i> Programmer</a>
                </div>
            </div>
            <div class="foot__bottom">
                <span>&copy; {{ date('Y') }} Arch Coffee — Mr. Suwanto Goma. Hak cipta dilindungi.</span>
                <span>Dirancang oleh <a href="https://www.instagram.com/ewin.lntp/" target="_blank" rel="noopener" style="color:var(--cream)">Ewin Lantapa</a></span>
            </div>
        </div>
    </footer>

    <button type="button" class="sound" id="soundBtn" aria-pressed="false" hidden>
        <span class="eq" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span class="sound__text">Musik mati</span>
    </button>

    <script>
        (function () {
            'use strict';
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* Navigasi solid setelah menggulir */
            var nav = document.getElementById('nav');
            var onScroll = function () { nav.classList.toggle('is-solid', window.scrollY > 40); };
            window.addEventListener('scroll', onScroll, { passive: true }); onScroll();

            /* Drawer mobile */
            var drawer = document.getElementById('drawer');
            var openBtn = document.getElementById('openDrawer');
            var setDrawer = function (open) {
                drawer.classList.toggle('is-open', open);
                drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
                openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.body.style.overflow = open ? 'hidden' : '';
            };
            openBtn.addEventListener('click', function () { setDrawer(true); });
            drawer.querySelectorAll('[data-close-drawer]').forEach(function (el) {
                el.addEventListener('click', function () { setDrawer(false); });
            });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setDrawer(false); });

            /* Musik + sambutan (sekali per sesi) */
            var audio = document.getElementById('bgAudio');
            var gate = document.getElementById('gate');
            var soundBtn = document.getElementById('soundBtn');
            var store = {
                get: function (k) { try { return sessionStorage.getItem(k); } catch (e) { return null; } },
                set: function (k, v) { try { sessionStorage.setItem(k, v); } catch (e) {} }
            };
            var setSoundUi = function (playing) {
                soundBtn.classList.toggle('is-playing', playing);
                soundBtn.setAttribute('aria-pressed', playing ? 'true' : 'false');
                soundBtn.querySelector('.sound__text').textContent = playing ? 'Musik menyala' : 'Musik mati';
                soundBtn.setAttribute('aria-label', playing ? 'Matikan musik' : 'Nyalakan musik');
            };
            var play = function () {
                audio.preload = 'auto';
                var p = audio.play();
                if (p && p.then) p.then(function () { setSoundUi(true); }).catch(function () { setSoundUi(false); });
                else setSoundUi(true);
            };
            var enter = function (withMusic) {
                gate.classList.add('is-gone');
                setTimeout(function () { gate.hidden = true; }, 700);
                soundBtn.hidden = false;
                store.set('arch.entered', '1');
                if (withMusic) play(); else setSoundUi(false);
            };
            if (store.get('arch.entered')) {
                soundBtn.hidden = false; setSoundUi(false);
            } else {
                gate.hidden = false;
                gate.querySelector('[data-enter="music"]').focus();
            }
            gate.querySelectorAll('[data-enter]').forEach(function (b) {
                b.addEventListener('click', function () { enter(b.getAttribute('data-enter') === 'music'); });
            });
            soundBtn.addEventListener('click', function () {
                if (audio.paused) play(); else { audio.pause(); setSoundUi(false); }
            });

            /* Slider hero */
            var slides = Array.prototype.slice.call(document.querySelectorAll('.hero__slide'));
            var data = {!! $slideData !!};
            if (slides.length) {
                var current = 0, timer = null, DURATION = 6000;
                var nameEl = document.getElementById('nowName');
                var priceEl = document.getElementById('nowPrice');
                var idxEl = document.getElementById('nowIndex');
                var seal = document.getElementById('nowSeal');
                var rankEl = document.getElementById('nowRank');
                var fill = document.getElementById('nowFill');
                var label = document.querySelector('.now__label');

                var loadBg = function (i) {
                    var s = slides[i];
                    if (s && s.dataset.bg) { s.style.backgroundImage = "url('" + s.dataset.bg + "')"; delete s.dataset.bg; }
                };
                var runFill = function () {
                    if (!fill) return;
                    fill.style.transition = 'none'; fill.style.width = '0';
                    void fill.offsetWidth;
                    if (slides.length > 1 && !reduce) { fill.style.transition = 'width ' + DURATION + 'ms linear'; fill.style.width = '100%'; }
                };
                var go = function (i) {
                    slides[current].classList.remove('is-active');
                    current = (i + slides.length) % slides.length;
                    loadBg(current); loadBg(current + 1);
                    slides[current].classList.add('is-active');
                    var d = data[current] || {};
                    if (nameEl) nameEl.textContent = d.name || '';
                    if (priceEl) priceEl.textContent = d.price || '';
                    if (idxEl) idxEl.textContent = current + 1;
                    if (seal) { seal.hidden = !d.rank; if (d.rank) rankEl.textContent = d.rank; }
                    if (label) label.textContent = d.rank ? 'Favorit pelanggan' : 'Dari dapur & bar kami';
                    runFill();
                };
                var start = function () {
                    clearInterval(timer);
                    if (slides.length > 1 && !reduce) timer = setInterval(function () { go(current + 1); }, DURATION);
                };
                loadBg(1); runFill(); start();
                document.querySelectorAll('[data-slide]').forEach(function (b) {
                    b.addEventListener('click', function () { go(current + parseInt(b.getAttribute('data-slide'), 10)); start(); });
                });
                document.addEventListener('visibilitychange', function () { if (document.hidden) clearInterval(timer); else start(); });
            }

            /* Filter menu per kategori + tampilkan semua */
            var tabs = document.querySelectorAll('.tab');
            var dishes = document.querySelectorAll('.dish');
            var showAll = document.getElementById('showAll');
            var expanded = false, activeCat = 'all';
            var applyFilter = function () {
                dishes.forEach(function (d) {
                    var match = activeCat === 'all' || d.dataset.cat === activeCat;
                    var limited = activeCat === 'all' && !expanded && d.hasAttribute('data-more');
                    d.hidden = !match || limited;
                });
                if (showAll) showAll.parentElement.hidden = activeCat !== 'all' || expanded;
            };
            tabs.forEach(function (t) {
                t.addEventListener('click', function () {
                    tabs.forEach(function (x) { x.classList.remove('is-on'); x.setAttribute('aria-selected', 'false'); });
                    t.classList.add('is-on'); t.setAttribute('aria-selected', 'true');
                    activeCat = t.dataset.cat;
                    applyFilter();
                });
            });
            if (showAll) showAll.addEventListener('click', function () { expanded = true; applyFilter(); });
        })();
    </script>
</body>

</html>
