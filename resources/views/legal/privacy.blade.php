<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datenschutzerklärung – SEOmaster</title>
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
            color: #202124; margin: 32px 0 10px;
        }
        h3 {
            font-size: 14px; font-weight: 500;
            color: #202124; margin: 20px 0 8px;
        }
        p { font-size: 14px; color: #3c4043; margin-bottom: 12px; }
        ul { font-size: 14px; color: #3c4043; margin: 0 0 12px 20px; }
        li { margin-bottom: 4px; }
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

        .toc {
            background: #e8f0fe;
            border: 1px solid #c5d8fb;
            border-radius: 8px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }
        .toc-title {
            font-size: 13px; font-weight: 500;
            color: #1a73e8; margin-bottom: 10px;
            text-transform: uppercase; letter-spacing: .04em;
        }
        .toc a {
            display: block;
            font-size: 13px; color: #1a73e8;
            padding: 2px 0;
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
        <a href="{{ url('/') }}" class="topbar-logo">SEO<span>master</span></a>
        <div class="topbar-divider"></div>
        <span class="topbar-title">Datenschutzerklärung</span>
    </div>

    <div class="container">
        <h1>Datenschutzerklärung</h1>

        <div class="toc">
            <div class="toc-title">Inhalt</div>
            <a href="#verantwortlicher">1. Verantwortlicher</a>
            <a href="#erhebung">2. Erhebung und Speicherung personenbezogener Daten</a>
            <a href="#account">3. Nutzerkonto & Vertragsabwicklung</a>
            <a href="#ki">4. KI-Dienste (OpenAI)</a>
            <a href="#zahlung">5. Zahlungsabwicklung (PayPal)</a>
            <a href="#hosting">6. Hosting & Server</a>
            <a href="#rechte">7. Ihre Rechte</a>
            <a href="#cookies">8. Cookies</a>
            <a href="#kontakt">9. Kontakt</a>
        </div>

        <h2 id="verantwortlicher">1. Verantwortlicher</h2>
        <div class="info-block">
            kreativ.team GmbH<br>
            Bodenseestraße 26<br>
            88145 Opfenbach<br><br>
            Geschäftsführer: Johannes Poll<br>
            E-Mail: <a href="mailto:post@kreativ.team">post@kreativ.team</a><br>
            Telefon: <a href="tel:+4983853259940">+49 8385 3259940</a>
        </div>

        <h2 id="erhebung">2. Erhebung und Speicherung personenbezogener Daten</h2>
        <p>Beim Besuch unserer Website werden automatisch Informationen in Server-Logfiles gespeichert, die Ihr Browser übermittelt. Dies sind:</p>
        <ul>
            <li>IP-Adresse (anonymisiert)</li>
            <li>Datum und Uhrzeit der Anfrage</li>
            <li>Browsertyp und -version</li>
            <li>Betriebssystem</li>
            <li>Referrer-URL</li>
        </ul>
        <p>Diese Daten werden nicht mit anderen Datenquellen zusammengeführt und nach 7 Tagen gelöscht.</p>

        <h2 id="account">3. Nutzerkonto & Vertragsabwicklung</h2>
        <p>Bei der Registrierung erheben wir folgende Daten:</p>
        <ul>
            <li>Name und E-Mail-Adresse</li>
            <li>Passwort (verschlüsselt gespeichert)</li>
            <li>Shopware-API-Zugangsdaten (verschlüsselt)</li>
        </ul>
        <p>Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung). Die Daten werden für die Dauer der Vertragsbeziehung gespeichert, danach nach steuerrechtlichen Vorschriften bis zu 10 Jahre.</p>

        <h2 id="ki">4. KI-Dienste (OpenAI)</h2>
        <p>SEOmaster nutzt die API von OpenAI LLC, 3180 18th St, San Francisco, CA 94110, USA zur Generierung von SEO-Texten. Dabei werden Produktdaten, Kategorienamen und Bildinformationen an OpenAI übermittelt.</p>
        <p>Personenbezogene Daten Ihrer Kunden werden dabei <strong>nicht</strong> übertragen. OpenAI verarbeitet die Daten gemäß ihrer <a href="https://openai.com/policies/privacy-policy" target="_blank" rel="noopener">Datenschutzrichtlinie</a>. Es besteht ein Data Processing Agreement (DPA) mit OpenAI.</p>

        <h2 id="zahlung">5. Zahlungsabwicklung (PayPal)</h2>
        <p>Für die Abonnement-Abwicklung nutzen wir PayPal (Europe) S.à r.l. et Cie, S.C.A., 22-24 Boulevard Royal, L-2449 Luxembourg. PayPal verarbeitet Zahlungsdaten gemäß seiner <a href="https://www.paypal.com/de/webapps/mpp/ua/privacy-full" target="_blank" rel="noopener">Datenschutzerklärung</a>.</p>

        <h2 id="hosting">6. Hosting & Server</h2>
        <p>SEOmaster wird auf Servern der IONOS SE, Elgendorfer Str. 57, 56410 Montabaur, Deutschland gehostet. Alle Daten werden ausschließlich auf Servern in Deutschland verarbeitet. Mit IONOS besteht ein Auftragsverarbeitungsvertrag gemäß Art. 28 DSGVO.</p>

        <h2 id="rechte">7. Ihre Rechte</h2>
        <p>Sie haben gegenüber uns folgende Rechte hinsichtlich der Sie betreffenden personenbezogenen Daten:</p>
        <ul>
            <li><strong>Auskunft</strong> (Art. 15 DSGVO)</li>
            <li><strong>Berichtigung</strong> (Art. 16 DSGVO)</li>
            <li><strong>Löschung</strong> (Art. 17 DSGVO)</li>
            <li><strong>Einschränkung der Verarbeitung</strong> (Art. 18 DSGVO)</li>
            <li><strong>Datenübertragbarkeit</strong> (Art. 20 DSGVO)</li>
            <li><strong>Widerspruch</strong> (Art. 21 DSGVO)</li>
        </ul>
        <p>Sie haben außerdem das Recht, sich bei einer Datenschutz-Aufsichtsbehörde über die Verarbeitung Ihrer personenbezogenen Daten zu beschweren. Zuständige Aufsichtsbehörde: Bayerisches Landesamt für Datenschutzaufsicht (BayLDA), Promenade 18, 91522 Ansbach.</p>

        <h2 id="cookies">8. Cookies</h2>
        <p>SEOmaster verwendet ausschließlich technisch notwendige Cookies für die Session-Verwaltung (Login). Es werden keine Tracking- oder Marketing-Cookies eingesetzt.</p>

        <h2 id="kontakt">9. Kontakt Datenschutz</h2>
        <p>Bei Fragen zum Datenschutz wenden Sie sich an:</p>
        <div class="info-block">
            kreativ.team GmbH<br>
            z. Hd. Datenschutz<br>
            Bodenseestraße 26, 88145 Opfenbach<br>
            E-Mail: <a href="mailto:post@kreativ.team">post@kreativ.team</a>
        </div>

        <p style="font-size:12px; color:#70757a; margin-top: 16px;">Stand: Februar 2026</p>

        <div class="footer-nav">
            <a href="{{ url('/') }}">← Zurück zur Startseite</a>
            <a href="{{ url('/imprint') }}">Impressum</a>
            <a href="{{ url('/terms') }}">AGB</a>
        </div>
    </div>
</body>
</html>
