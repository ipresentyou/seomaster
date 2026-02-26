<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEOmaster – KI-SEO für Shopware</title>
    <meta name="description" content="SEOmaster optimiert automatisch Meta-Titel, Beschreibungen, Keywords und Alt-Texte in Ihrem Shopware-Shop – mit echter KI, in Sekunden.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=DM+Serif+Display&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --blue: #1a73e8;
            --blue-dark: #1557b0;
            --blue-light: #e8f0fe;
            --green: #1e8e3e;
            --red: #d93025;
            --text-1: #202124;
            --text-2: #3c4043;
            --text-3: #5f6368;
            --text-4: #80868b;
            --border: #dadce0;
            --bg: #fff;
            --bg-soft: #f8f9fa;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.08);
            --shadow: 0 2px 6px rgba(0,0,0,.1), 0 1px 2px rgba(0,0,0,.06);
            --shadow-lg: 0 4px 24px rgba(0,0,0,.12);
            --radius: 8px;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Roboto', sans-serif;
            color: var(--text-1);
            background: var(--bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── NAV ────────────────────────────────────────────── */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 0 40px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .nav-brand {
            font-family: 'Google Sans', sans-serif;
            font-size: 20px; font-weight: 500;
            color: var(--text-1);
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .nav-brand span { color: var(--blue); }
        .nav-logo {
            width: 32px; height: 32px;
            background: var(--blue);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px;
        }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link {
            font-size: 14px; color: var(--text-2);
            text-decoration: none; padding: 8px 16px;
            border-radius: 4px; font-weight: 500;
            transition: background .15s;
        }
        .nav-link:hover { background: var(--bg-soft); }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 24px; border-radius: 4px;
            font-size: 14px; font-weight: 500;
            cursor: pointer; text-decoration: none;
            border: none; transition: all .2s;
            font-family: 'Roboto', sans-serif;
        }
        .btn-primary {
            background: var(--blue); color: #fff;
        }
        .btn-primary:hover {
            background: var(--blue-dark);
            box-shadow: 0 1px 3px rgba(26,115,232,.4);
        }
        .btn-outline {
            background: #fff; color: var(--blue);
            border: 1px solid var(--blue);
        }
        .btn-outline:hover { background: var(--blue-light); }
        .btn-lg { padding: 14px 32px; font-size: 15px; border-radius: 6px; }
        .btn-xl { padding: 16px 40px; font-size: 16px; border-radius: 6px; }

        /* ── HERO ───────────────────────────────────────────── */
        .hero {
            padding: 80px 40px 100px;
            text-align: center;
            background: linear-gradient(180deg, #f0f4ff 0%, #fff 100%);
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 800px 400px at 50% -100px, rgba(26,115,232,.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--blue-light); color: var(--blue);
            padding: 6px 16px; border-radius: 100px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 32px;
            border: 1px solid rgba(26,115,232,.2);
        }
        .hero-badge::before { content: '✦'; font-size: 10px; }
        .hero h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(40px, 6vw, 72px);
            line-height: 1.1;
            letter-spacing: -.02em;
            color: var(--text-1);
            max-width: 800px;
            margin: 0 auto 24px;
        }
        .hero h1 em {
            font-style: normal;
            color: var(--blue);
        }
        .hero-sub {
            font-size: clamp(16px, 2vw, 20px);
            color: var(--text-3);
            max-width: 560px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }
        .hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .hero-trust {
            margin-top: 48px;
            font-size: 13px; color: var(--text-4);
            display: flex; align-items: center; justify-content: center; gap: 24px;
            flex-wrap: wrap;
        }
        .hero-trust-item { display: flex; align-items: center; gap: 6px; }
        .trust-icon { color: var(--green); font-size: 14px; }

        /* MOCKUP */
        .hero-mockup {
            margin: 60px auto 0;
            max-width: 900px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 40px rgba(0,0,0,.12), 0 0 0 1px var(--border);
            overflow: hidden;
            text-align: left;
        }
        .mockup-bar {
            background: var(--bg-soft);
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .mockup-dot { width: 12px; height: 12px; border-radius: 50%; }
        .mockup-url {
            flex: 1; background: #fff; border: 1px solid var(--border);
            border-radius: 100px; padding: 4px 16px;
            font-size: 12px; color: var(--text-3);
            margin: 0 12px;
        }
        .mockup-body { padding: 24px; }
        .mockup-row {
            display: flex; gap: 12px; align-items: flex-start;
            padding: 16px 0; border-bottom: 1px solid #f1f3f4;
        }
        .mockup-row:last-child { border-bottom: none; }
        .mockup-thumb {
            width: 60px; height: 60px; border-radius: 6px;
            background: linear-gradient(135deg, #e8f0fe, #d2e3fc);
            flex-shrink: 0; display: flex; align-items: center;
            justify-content: center; font-size: 24px;
        }
        .mockup-info { flex: 1; }
        .mockup-title { font-size: 14px; font-weight: 500; color: var(--text-1); margin-bottom: 4px; }
        .mockup-desc { font-size: 12px; color: var(--text-3); margin-bottom: 8px; line-height: 1.5; }
        .mockup-tags { display: flex; gap: 6px; flex-wrap: wrap; }
        .tag {
            font-size: 11px; padding: 2px 8px; border-radius: 100px;
            font-weight: 500;
        }
        .tag-green { background: #e6f4ea; color: var(--green); }
        .tag-blue { background: var(--blue-light); color: var(--blue); }
        .tag-orange { background: #fff3e0; color: #e65100; }
        .mockup-actions { display: flex; gap: 6px; margin-top: 8px; }
        .mockup-btn {
            font-size: 11px; padding: 4px 10px; border-radius: 4px;
            border: 1px solid var(--border); background: #fff;
            color: var(--text-2); cursor: pointer;
        }
        .mockup-btn-ai {
            background: var(--blue); color: #fff; border-color: var(--blue);
        }
        .ai-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: linear-gradient(135deg, #1a73e8, #4285f4);
            color: #fff; font-size: 10px; font-weight: 600;
            padding: 2px 8px; border-radius: 100px; letter-spacing: .05em;
        }

        /* ── SECTION BASE ───────────────────────────────────── */
        section { padding: 80px 40px; }
        .section-inner { max-width: 1100px; margin: 0 auto; }
        .section-tag {
            font-size: 12px; font-weight: 600; letter-spacing: .1em;
            text-transform: uppercase; color: var(--blue);
            margin-bottom: 12px;
        }
        .section-title {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.15; letter-spacing: -.02em;
            color: var(--text-1); margin-bottom: 16px;
        }
        .section-sub {
            font-size: 17px; color: var(--text-3);
            max-width: 600px; line-height: 1.7;
        }

        /* ── PROBLEM ────────────────────────────────────────── */
        .problem { background: var(--bg-soft); }
        .problem-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 32px; margin-top: 48px;
        }
        .problem-card {
            background: #fff; border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 28px; box-shadow: var(--shadow-sm);
        }
        .problem-card-title {
            font-size: 16px; font-weight: 500; color: var(--text-1);
            margin-bottom: 12px; display: flex; align-items: center; gap: 10px;
        }
        .problem-icon {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .icon-red { background: #fce8e6; }
        .icon-orange { background: #fff3e0; }
        .problem-card p { font-size: 14px; color: var(--text-3); line-height: 1.7; }

        .solution-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 32px; margin-top: 32px;
        }
        .solution-card {
            background: #fff; border-radius: var(--radius);
            border: 1px solid rgba(26,115,232,.2);
            padding: 28px;
            background: linear-gradient(135deg, #fff 0%, #f0f4ff 100%);
        }
        .solution-card-title {
            font-size: 16px; font-weight: 500; color: var(--blue);
            margin-bottom: 10px; display: flex; align-items: center; gap: 10px;
        }
        .icon-blue { background: var(--blue-light); }
        .solution-card p { font-size: 14px; color: var(--text-2); line-height: 1.7; }

        .vs-divider {
            display: flex; align-items: center; gap: 20px;
            margin: 40px 0 32px;
        }
        .vs-line { flex: 1; height: 1px; background: var(--border); }
        .vs-badge {
            font-family: 'Google Sans', sans-serif;
            font-size: 13px; font-weight: 500;
            color: var(--text-3); padding: 6px 16px;
            border: 1px solid var(--border); border-radius: 100px;
        }

        /* ── FEATURES ───────────────────────────────────────── */
        .features-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 24px; margin-top: 56px;
        }
        .feature-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 28px;
            transition: box-shadow .2s, border-color .2s;
        }
        .feature-card:hover {
            box-shadow: var(--shadow-lg);
            border-color: rgba(26,115,232,.3);
        }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 10px;
            background: var(--blue-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 16px;
        }
        .feature-title {
            font-family: 'Google Sans', sans-serif;
            font-size: 16px; font-weight: 500;
            color: var(--text-1); margin-bottom: 10px;
        }
        .feature-desc { font-size: 14px; color: var(--text-3); line-height: 1.7; }
        .feature-highlight {
            border-color: var(--blue);
            background: linear-gradient(135deg, #fff 0%, #f0f4ff 100%);
        }

        /* ── PRICING ────────────────────────────────────────── */
        .pricing { background: var(--bg-soft); }
        .pricing-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 24px; margin-top: 56px; align-items: stretch;
        }
        .pricing-card {
            background: #fff; border-radius: 12px;
            border: 1px solid var(--border);
            padding: 32px; display: flex; flex-direction: column;
            box-shadow: var(--shadow-sm);
        }
        .pricing-card-featured {
            border-color: var(--blue);
            box-shadow: 0 8px 32px rgba(26,115,232,.15);
            position: relative;
        }
        .pricing-popular {
            position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
            background: var(--blue); color: #fff;
            font-size: 12px; font-weight: 600; letter-spacing: .05em;
            padding: 4px 16px; border-radius: 100px;
            text-transform: uppercase;
        }
        .pricing-plan {
            font-family: 'Google Sans', sans-serif;
            font-size: 13px; font-weight: 500;
            color: var(--text-3); letter-spacing: .08em;
            text-transform: uppercase; margin-bottom: 8px;
        }
        .pricing-price {
            font-family: 'DM Serif Display', serif;
            font-size: 48px; line-height: 1; color: var(--text-1);
            margin-bottom: 4px;
        }
        .pricing-price sup { font-size: 24px; vertical-align: super; }
        .pricing-period { font-size: 13px; color: var(--text-4); margin-bottom: 20px; }
        .pricing-desc { font-size: 14px; color: var(--text-3); margin-bottom: 24px; line-height: 1.6; }
        .pricing-features { list-style: none; margin-bottom: 32px; flex: 1; }
        .pricing-features li {
            font-size: 14px; color: var(--text-2);
            padding: 8px 0; border-bottom: 1px solid #f1f3f4;
            display: flex; align-items: center; gap: 10px;
        }
        .pricing-features li:last-child { border-bottom: none; }
        .check { color: var(--green); font-size: 16px; flex-shrink: 0; }
        .cross { color: var(--text-4); font-size: 16px; flex-shrink: 0; }

        /* ── FAQ ────────────────────────────────────────────── */
        .faq-list { margin-top: 48px; max-width: 720px; }
        .faq-item {
            border: 1px solid var(--border); border-radius: var(--radius);
            margin-bottom: 12px; overflow: hidden;
        }
        .faq-q {
            padding: 20px 24px;
            font-size: 15px; font-weight: 500; color: var(--text-1);
            cursor: pointer; display: flex; justify-content: space-between; align-items: center;
            background: #fff; transition: background .15s;
            list-style: none; user-select: none;
        }
        .faq-q:hover { background: var(--bg-soft); }
        .faq-q::after { content: '+'; font-size: 20px; color: var(--text-3); font-weight: 300; }
        details[open] .faq-q::after { content: '−'; }
        .faq-a {
            padding: 0 24px 20px;
            font-size: 14px; color: var(--text-3); line-height: 1.8;
            background: #fff;
        }

        /* ── CTA ────────────────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, #1a73e8 0%, #1557b0 100%);
            text-align: center; padding: 80px 40px;
        }
        .cta-section h2 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(28px, 4vw, 48px);
            color: #fff; margin-bottom: 16px; letter-spacing: -.02em;
        }
        .cta-section p { font-size: 18px; color: rgba(255,255,255,.8); margin-bottom: 40px; }
        .btn-white { background: #fff; color: var(--blue); }
        .btn-white:hover { background: #f0f4ff; }
        .btn-ghost { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3); }
        .btn-ghost:hover { background: rgba(255,255,255,.25); }
        .cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .cta-note { margin-top: 20px; font-size: 13px; color: rgba(255,255,255,.6); }

        /* ── FOOTER ─────────────────────────────────────────── */
        footer {
            background: var(--text-1); color: rgba(255,255,255,.6);
            padding: 48px 40px 32px;
        }
        .footer-inner { max-width: 1100px; margin: 0 auto; }
        .footer-top {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 40px; gap: 40px; flex-wrap: wrap;
        }
        .footer-brand {
            font-family: 'Google Sans', sans-serif;
            font-size: 18px; color: #fff; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
        }
        .footer-brand-icon {
            width: 28px; height: 28px; background: var(--blue);
            border-radius: 6px; display: flex; align-items: center;
            justify-content: center; font-size: 14px;
        }
        .footer-tagline { font-size: 13px; margin-top: 6px; }
        .footer-links { display: flex; gap: 60px; flex-wrap: wrap; }
        .footer-col h4 { color: #fff; font-size: 13px; font-weight: 500; margin-bottom: 12px; }
        .footer-col a {
            display: block; font-size: 13px; color: rgba(255,255,255,.5);
            text-decoration: none; margin-bottom: 8px; transition: color .15s;
        }
        .footer-col a:hover { color: rgba(255,255,255,.9); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.1);
            padding-top: 24px; font-size: 12px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
        }

        /* ── RESPONSIVE ─────────────────────────────────────── */
        @media (max-width: 768px) {
            nav { padding: 0 20px; }
            .nav-links .nav-link { display: none; }
            section { padding: 60px 20px; }
            .hero { padding: 60px 20px 80px; }
            .problem-grid, .solution-grid, .features-grid, .pricing-grid { grid-template-columns: 1fr; }
            .hero-trust { gap: 12px; }
        }

        /* ── ANIMATIONS ─────────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero-badge { animation: fadeUp .5s ease both; }
        .hero h1 { animation: fadeUp .5s .1s ease both; }
        .hero-sub { animation: fadeUp .5s .2s ease both; }
        .hero-cta { animation: fadeUp .5s .3s ease both; }
        .hero-trust { animation: fadeUp .5s .4s ease both; }
        .hero-mockup { animation: fadeUp .6s .5s ease both; }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="nav-brand">
        <div class="nav-logo">📊</div>
        SEO<span>master</span>
    </a>
    <div class="nav-links">
        <a href="#features" class="nav-link">Features</a>
        <a href="#pricing" class="nav-link">Preise</a>
        <a href="#faq" class="nav-link">FAQ</a>
        <a href="{{ route('login') }}" class="nav-link">Anmelden</a>
        <a href="{{ route('register') }}" class="btn btn-primary">Kostenlos starten →</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">Exklusiv für Shopware 6</div>
    <h1>SEO-Optimierung,<br>die <em>wirklich</em> funktioniert</h1>
    <p class="hero-sub">
        SEOmaster analysiert Ihren Shop und generiert per KI
        perfekte Meta-Titel, Beschreibungen, Keywords und Alt-Texte —
        in wenigen Minuten statt wochenlanger Handarbeit.
    </p>
    <div class="hero-cta">
        <a href="{{ route('register') }}" class="btn btn-primary btn-xl">Jetzt kostenlos testen →</a>
        <a href="#features" class="btn btn-outline btn-lg">Features ansehen</a>
    </div>
    <div class="hero-trust">
        <div class="hero-trust-item"><span class="trust-icon">✓</span> Keine Kreditkarte nötig</div>
        <div class="hero-trust-item"><span class="trust-icon">✓</span> 14 Tage gratis testen</div>
        <div class="hero-trust-item"><span class="trust-icon">✓</span> Direkte Shopware-API-Anbindung</div>
        <div class="hero-trust-item"><span class="trust-icon">✓</span> DSGVO-konform</div>
    </div>

    <!-- MOCKUP -->
    <div class="hero-mockup">
        <div class="mockup-bar">
            <div class="mockup-dot" style="background:#ff5f57"></div>
            <div class="mockup-dot" style="background:#febc2e"></div>
            <div class="mockup-dot" style="background:#28c840"></div>
            <div class="mockup-url">seomaster.app/projects/1/seo/products</div>
        </div>
        <div class="mockup-body">
            <div class="mockup-row">
                <div class="mockup-thumb">👟</div>
                <div class="mockup-info">
                    <div class="mockup-title">Premium Laufschuh X500</div>
                    <div class="mockup-desc">Leichter Laufschuh mit optimaler Dämpfung für lange Distanzen. Ideal für tägliches Training.</div>
                    <div class="mockup-tags">
                        <span class="tag tag-green">✓ Meta Title</span>
                        <span class="tag tag-green">✓ Meta Desc</span>
                        <span class="tag tag-blue">✨ KI optimiert</span>
                    </div>
                    <div class="mockup-actions">
                        <button class="mockup-btn">✏️ Bearbeiten</button>
                        <button class="mockup-btn mockup-btn-ai">✨ KI regenerieren</button>
                    </div>
                </div>
            </div>
            <div class="mockup-row">
                <div class="mockup-thumb">👜</div>
                <div class="mockup-info">
                    <div class="mockup-title">Leder-Handtasche Classic</div>
                    <div class="mockup-desc" style="color:#d93025">❌ Kein Meta Title · Keine Meta Description · Keine Keywords</div>
                    <div class="mockup-tags">
                        <span class="tag tag-orange">⚠ SEO fehlt</span>
                    </div>
                    <div class="mockup-actions">
                        <button class="mockup-btn">✏️ Bearbeiten</button>
                        <button class="mockup-btn mockup-btn-ai">✨ Jetzt optimieren</button>
                    </div>
                </div>
            </div>
            <div class="mockup-row">
                <div class="mockup-thumb">🎧</div>
                <div class="mockup-info">
                    <div class="mockup-title">Wireless Kopfhörer Pro</div>
                    <div class="mockup-desc">Noise-Cancelling Kopfhörer mit 30h Akkulaufzeit und Premium-Sound.</div>
                    <div class="mockup-tags">
                        <span class="tag tag-green">✓ Meta Title</span>
                        <span class="tag tag-green">✓ Keywords</span>
                        <span class="tag tag-green">✓ Alt-Texte</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PROBLEM / LÖSUNG -->
<section class="problem" id="problem">
    <div class="section-inner">
        <div class="section-tag">Das Problem</div>
        <h2 class="section-title">SEO im E-Commerce kostet Zeit,<br>die Sie nicht haben</h2>
        <p class="section-sub">Hunderte Produkte, Kategorien und Bilder — alle brauchen SEO-Texte. Manuell ist das schlicht nicht möglich.</p>

        <div class="problem-grid">
            <div class="problem-card">
                <div class="problem-card-title">
                    <div class="problem-icon icon-red">⏱️</div>
                    Zeitaufwand
                </div>
                <p>Ein Produkt manuell zu optimieren dauert 15–30 Minuten. Bei 500 Produkten sind das über 100 Stunden — jedes Jahr neu.</p>
            </div>
            <div class="problem-card">
                <div class="problem-card-title">
                    <div class="problem-icon icon-red">🔍</div>
                    Unsichtbarkeit
                </div>
                <p>Ohne optimierte Meta-Daten ranken Ihre Produkte nicht. Potenzielle Kunden finden Ihre Konkurrenz — nicht Sie.</p>
            </div>
            <div class="problem-card">
                <div class="problem-card-title">
                    <div class="problem-icon icon-orange">🖼️</div>
                    Alt-Texte fehlen
                </div>
                <p>Fehlende Alt-Texte kosten Google-Bildsuche-Traffic und Accessibility-Punkte — und werden häufig komplett vergessen.</p>
            </div>
            <div class="problem-card">
                <div class="problem-card-title">
                    <div class="problem-icon icon-orange">📊</div>
                    Kein Überblick
                </div>
                <p>Welche Produkte haben SEO-Lücken? In Shopware gibt es keine zentrale Übersicht — man tappt im Dunkeln.</p>
            </div>
        </div>

        <div class="vs-divider">
            <div class="vs-line"></div>
            <div class="vs-badge">SEOmaster löst das</div>
            <div class="vs-line"></div>
        </div>

        <div class="solution-grid">
            <div class="solution-card">
                <div class="solution-card-title">
                    <div class="problem-icon icon-blue">⚡</div>
                    In Minuten statt Stunden
                </div>
                <p>KI analysiert Ihre Seite und generiert sofort optimierte SEO-Texte — für alle Produkte auf einmal, wenn gewünscht.</p>
            </div>
            <div class="solution-card">
                <div class="solution-card-title">
                    <div class="problem-icon icon-blue">🎯</div>
                    Zielgruppen-spezifisch
                </div>
                <p>Definieren Sie Ihre Brand-Voice einmalig — alle generierten Texte klingen wie Sie, nicht wie eine generische KI.</p>
            </div>
            <div class="solution-card">
                <div class="solution-card-title">
                    <div class="problem-icon icon-blue">🌐</div>
                    Mehrsprachig
                </div>
                <p>Optimieren Sie alle Sprachversionen Ihres Shops — SEOmaster erkennt die Sprache automatisch.</p>
            </div>
            <div class="solution-card">
                <div class="solution-card-title">
                    <div class="problem-icon icon-blue">📋</div>
                    Google Search Console Style
                </div>
                <p>Behalten Sie den Überblick: Sehen Sie sofort, wo SEO-Lücken sind, und schließen Sie diese mit einem Klick.</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features">
    <div class="section-inner">
        <div class="section-tag">Features</div>
        <h2 class="section-title">Alles was Ihr Shop braucht,<br>um auf Seite 1 zu landen</h2>
        <p class="section-sub">Drei mächtige Editoren, direkt verbunden mit Ihrer Shopware API — ohne Plugin, ohne Agentur.</p>

        <div class="features-grid">
            <div class="feature-card feature-highlight">
                <div class="feature-icon">🛍️</div>
                <div class="feature-title">Produkt-SEO Editor</div>
                <p class="feature-desc">Optimieren Sie Meta-Titel, Meta-Description, Keywords und Produktbeschreibungen für alle Produkte. KI analysiert die Live-Seite und schreibt kontextrelevante Texte.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <div class="feature-title">Kategorie-SEO Editor</div>
                <p class="feature-desc">Strukturierte Kategorietexte, die Kunden und Google gleichermaßen überzeugen. Inklusive SEO-Text-Generator für die Kategoriebeschreibung.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🖼️</div>
                <div class="feature-title">Bild Alt-Text Editor</div>
                <p class="feature-desc">Alle Bilder ohne Alt-Text auf einen Blick. KI beschreibt das Bild automatisch — SEO-optimiert, auf Wunsch in jeder Sprache.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔗</div>
                <div class="feature-title">Direkte Shopware-API</div>
                <p class="feature-desc">Keine Plugin-Installation notwendig. SEOmaster verbindet sich direkt per API — Änderungen werden sofort in Shopware gespeichert.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <div class="feature-title">KI mit Brand-Voice</div>
                <p class="feature-desc">Hinterlegen Sie Ihren SEO-Prompt einmalig pro Projekt. Alle generierten Texte klingen konsistent nach Ihrer Marke — nicht nach ChatGPT.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <div class="feature-title">Mehrsprachiger Support</div>
                <p class="feature-desc">Shopware Sprachversionen werden automatisch erkannt. Optimieren Sie Deutsch, Englisch und mehr — pro Sprache unabhängig.</p>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="pricing" id="pricing">
    <div class="section-inner">
        <div class="section-tag">Preise</div>
        <h2 class="section-title" style="text-align:center">Transparent. Fair. Skalierbar.</h2>
        <p class="section-sub" style="margin:0 auto; text-align:center">Starten Sie kostenlos — upgraden Sie wenn Sie wachsen. Keine Einrichtungsgebühr, keine versteckten Kosten.</p>

        <div class="pricing-grid">
            <div class="pricing-card">
                <div class="pricing-plan">Starter</div>
                <div class="pricing-price"><sup>€</sup>0</div>
                <div class="pricing-period">/ Monat, für immer</div>
                <div class="pricing-desc">Perfekt zum Ausprobieren und für kleine Shops.</div>
                <ul class="pricing-features">
                    <li><span class="check">✓</span> 1 Projekt / Shop</li>
                    <li><span class="check">✓</span> 50 SEO-Generierungen/Monat</li>
                    <li><span class="check">✓</span> Produkt & Kategorie Editor</li>
                    <li><span class="check">✓</span> Alt-Text Generator</li>
                    <li><span class="cross">—</span> Batch-Optimierung</li>
                    <li><span class="cross">—</span> Priority Support</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-outline" style="text-align:center; justify-content:center">Kostenlos starten</a>
            </div>

            <div class="pricing-card pricing-card-featured">
                <div class="pricing-popular">Empfohlen</div>
                <div class="pricing-plan">Professional</div>
                <div class="pricing-price"><sup>€</sup>49</div>
                <div class="pricing-period">/ Monat, jährlich abgerechnet</div>
                <div class="pricing-desc">Für wachsende Shops mit mehreren Sprachen und Projekten.</div>
                <ul class="pricing-features">
                    <li><span class="check">✓</span> 5 Projekte / Shops</li>
                    <li><span class="check">✓</span> 500 SEO-Generierungen/Monat</li>
                    <li><span class="check">✓</span> Alle Editoren</li>
                    <li><span class="check">✓</span> Batch-Optimierung</li>
                    <li><span class="check">✓</span> Mehrsprachig</li>
                    <li><span class="check">✓</span> Priority Support</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary" style="text-align:center; justify-content:center">Jetzt starten →</a>
            </div>

            <div class="pricing-card">
                <div class="pricing-plan">Agency</div>
                <div class="pricing-price"><sup>€</sup>149</div>
                <div class="pricing-period">/ Monat, jährlich abgerechnet</div>
                <div class="pricing-desc">Für Agenturen, die mehrere Kundenshops verwalten.</div>
                <ul class="pricing-features">
                    <li><span class="check">✓</span> Unbegrenzte Projekte</li>
                    <li><span class="check">✓</span> 2.000 Generierungen/Monat</li>
                    <li><span class="check">✓</span> White-Label Option</li>
                    <li><span class="check">✓</span> Team-Zugang</li>
                    <li><span class="check">✓</span> API-Zugang</li>
                    <li><span class="check">✓</span> Dedicated Support</li>
                </ul>
                <a href="mailto:hello@seomaster.app" class="btn btn-outline" style="text-align:center; justify-content:center">Kontakt aufnehmen</a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section id="faq">
    <div class="section-inner">
        <div class="section-tag">Häufige Fragen</div>
        <h2 class="section-title">Alles was Sie wissen müssen</h2>

        <div class="faq-list">
            <details class="faq-item">
                <summary class="faq-q">Brauche ich ein Shopware-Plugin installieren?</summary>
                <p class="faq-a">Nein. SEOmaster verbindet sich direkt über die Shopware 6 REST API mit Ihrem Shop. Sie brauchen lediglich einen API-Zugang (Client ID + Secret) — kein Plugin, kein FTP-Zugriff.</p>
            </details>
            <details class="faq-item">
                <summary class="faq-q">Welche Shopware-Version wird unterstützt?</summary>
                <p class="faq-a">SEOmaster unterstützt Shopware 6 ab Version 6.4. Die API-Schnittstelle ist kompatibel mit allen aktuellen Shopware 6 Versionen, inklusive Cloud und Self-Hosted.</p>
            </details>
            <details class="faq-item">
                <summary class="faq-q">Wie gut sind die KI-generierten Texte wirklich?</summary>
                <p class="faq-a">Sehr gut — besonders wenn Sie einen Brand-Prompt hinterlegen. Die KI analysiert den Live-Inhalt Ihrer Seite (H1, Produktbeschreibung, Preise) und schreibt darauf abgestimmte Texte. Sie können jeden generierten Text vor dem Speichern noch anpassen.</p>
            </details>
            <details class="faq-item">
                <summary class="faq-q">Werden Änderungen sofort in Shopware gespeichert?</summary>
                <p class="faq-a">Ja. Ein Klick auf "Speichern" schreibt die SEO-Daten direkt via PATCH-Request in Ihre Shopware-Datenbank. Kein Export, kein Import, kein Warten.</p>
            </details>
            <details class="faq-item">
                <summary class="faq-q">Funktioniert SEOmaster auch mit mehrsprachigen Shops?</summary>
                <p class="faq-a">Absolut. SEOmaster erkennt alle in Shopware konfigurierten Sprachen und Sales Channels. Sie können für jede Sprache separat optimieren — die KI schreibt automatisch in der richtigen Sprache.</p>
            </details>
            <details class="faq-item">
                <summary class="faq-q">Kann ich meinen eigenen OpenAI-Key nutzen?</summary>
                <p class="faq-a">Ja. Im Professional und Agency Plan können Sie Ihren eigenen OpenAI API Key hinterlegen. So behalten Sie die volle Kontrolle über Kosten und Datenverarbeitung.</p>
            </details>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2>Bereit, Ihren Shop sichtbarer zu machen?</h2>
    <p>Starten Sie heute — kostenlos, ohne Kreditkarte, sofort einsatzbereit.</p>
    <div class="cta-btns">
        <a href="{{ route('register') }}" class="btn btn-white btn-xl">Kostenlos starten →</a>
        <a href="#faq" class="btn btn-ghost btn-lg">Mehr erfahren</a>
    </div>
    <p class="cta-note">14 Tage alle Features gratis · Danach Starter-Plan kostenlos</p>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div>
                <div class="footer-brand">
                    <div class="footer-brand-icon">📊</div>
                    SEOmaster
                </div>
                <div class="footer-tagline">KI-SEO für Shopware 6</div>
            </div>
            <div class="footer-links">
                <div class="footer-col">
                    <h4>Produkt</h4>
                    <a href="#features">Features</a>
                    <a href="#pricing">Preise</a>
                    <a href="{{ route('register') }}">Registrieren</a>
                    <a href="{{ route('login') }}">Anmelden</a>
                </div>
                <div class="footer-col">
                    <h4>Rechtliches</h4>
                    <a href="/datenschutz">Datenschutz</a>
                    <a href="/impressum">Impressum</a>
                    <a href="/agb">AGB</a>
                </div>
                <div class="footer-col">
                    <h4>Kontakt</h4>
                    <a href="mailto:hello@seomaster.app">hello@seomaster.app</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 SEOmaster. Alle Rechte vorbehalten.</span>
            <span>Made with ☕ in Germany</span>
        </div>
    </div>
</footer>

</body>
</html>
