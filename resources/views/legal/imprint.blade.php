<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impressum – SEOmaster</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Roboto', Arial, sans-serif; background: #fff; color: #202124; line-height: 1.7; }

        .topbar {
            height: 60px;
            border-bottom: 1px solid #dadce0;
            display: flex; align-items: center;
            padding: 0 40px;
            gap: 16px;
        }
        .topbar-logo {
            font-family: 'Google Sans', sans-serif;
            font-size: 18px; font-weight: 500;
            color: #202124; text-decoration: none;
        }
        .topbar-logo span { color: #1a73e8; }
        .topbar-divider { width: 1px; height: 24px; background: #dadce0; }
        .topbar-title { font-size: 14px; color: #5f6368; }

        .container { max-width: 760px; margin: 0 auto; padding: 48px 40px 80px; }

        h1 {
            font-family: 'Google Sans', sans-serif;
            font-size: 28px; font-weight: 400;
            color: #202124; margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 1px solid #ebebeb;
        }
        h2 {
            font-family: 'Google Sans', sans-serif;
            font-size: 16px; font-weight: 500;
            color: #202124; margin: 28px 0 10px;
        }
        p { font-size: 14px; color: #3c4043; margin-bottom: 12px; }
        a { color: #1a73e8; text-decoration: none; }
        a:hover { text-decoration: underline; }

        .info-block {
            background: #f8f9fa;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 20px 24px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #3c4043;
            line-height: 1.8;
        }

        .footer-nav {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ebebeb;
            display: flex; gap: 24px;
            font-size: 13px;
        }
        .footer-nav a { color: #5f6368; }
        .footer-nav a:hover { color: #1a73e8; }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="{{ url('/') }}" class="topbar-logo">
            <img src="{{ asset('images/logo_seomaster.svg') }}" width="240" alt="Logo">
        </a>
        <div class="topbar-divider"></div>
        <span class="topbar-title">Impressum</span>
    </div>

    <div class="container">
        <h1>Impressum</h1>

        <h2>Angaben gemäß § 5 TMG</h2>
        <div class="info-block">
            kreativ.team GmbH<br>
            Bodenseestraße 26<br>
            88145 Opfenbach
        </div>

        <h2>Geschäftsführung</h2>
        <p>Johannes Poll</p>

        <h2>Kontakt</h2>
        <div class="info-block">
            Telefon: <a href="tel:+4983853259940">+49 8385 3259940</a><br>
            E-Mail: <a href="mailto:post@kreativ.team">post@kreativ.team</a><br>
            Web: <a href="https://www.kreativ.team" target="_blank" rel="noopener">www.kreativ.team</a>
        </div>

        <h2>Handelsregister</h2>
        <div class="info-block">
            Registergericht: Amtsgericht Kempten (Allgäu)<br>
            Registernummer: HRA 13795<br>
            USt-IdNr.: DE312635076
        </div>

        <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
        <p>Johannes Poll<br>Bodenseestraße 26, 88145 Opfenbach</p>

        <h2>Haftungsausschluss</h2>
        <p>Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen.</p>
        <p>Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen.</p>

        <div class="footer-nav">
            <a href="{{ url('/') }}">← Zurück zur Startseite</a>
            <a href="{{ url('/privacy') }}">Datenschutz</a>
            <a href="{{ url('/terms') }}">AGB</a>
        </div>
    </div>
</body>
</html>
