<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGB – SEOmaster</title>
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
        <span class="topbar-title">Allgemeine Geschäftsbedingungen</span>
    </div>

    <div class="container">
        <h1>Allgemeine Geschäftsbedingungen (AGB)</h1>

        <div class="info-block">
            <strong>kreativ.team GmbH</strong><br>
            Bodenseestraße 26, 88145 Opfenbach<br>
            E-Mail: <a href="mailto:post@kreativ.team">post@kreativ.team</a>
        </div>

        <h2>§ 1 Geltungsbereich</h2>
        <p>Diese Allgemeinen Geschäftsbedingungen gelten für alle Verträge zwischen der kreativ.team GmbH (nachfolgend „Anbieter") und den Nutzern der SaaS-Plattform SEOmaster (nachfolgend „Nutzer"). Abweichende Bedingungen des Nutzers werden nicht anerkannt, es sei denn, der Anbieter stimmt ihrer Geltung ausdrücklich schriftlich zu.</p>

        <h2>§ 2 Leistungsbeschreibung</h2>
        <p>SEOmaster ist eine webbasierte Software-as-a-Service (SaaS)-Plattform zur KI-gestützten Optimierung von SEO-Inhalten in Shopware 6 Online-Shops. Der Anbieter stellt dem Nutzer über das Internet Zugang zur Plattform bereit.</p>
        <p>Der Leistungsumfang richtet sich nach dem jeweils gebuchten Abonnement-Plan (Starter, Professional, Agency). Eine Beschreibung der einzelnen Leistungen ist auf der Website einsehbar.</p>

        <h2>§ 3 Vertragsschluss & Registrierung</h2>
        <p>Der Vertrag kommt durch die Registrierung des Nutzers und die Freischaltung des Kontos durch den Anbieter zustande. Der Nutzer muss volljährig und geschäftsfähig sein. Bei gewerblicher Nutzung garantiert der Nutzer, zur Vertretung des Unternehmens berechtigt zu sein.</p>

        <h2>§ 4 Abonnement & Zahlung</h2>
        <p>Die Nutzung kostenpflichtiger Pläne erfolgt auf Abonnement-Basis. Preise sind auf der Website ausgewiesen und verstehen sich als Nettopreise zzgl. der gesetzlichen MwSt.</p>
        <ul>
            <li>Abonnements verlängern sich automatisch, sofern nicht rechtzeitig gekündigt wird</li>
            <li>Kündigung ist jederzeit zum Ende der laufenden Abrechnungsperiode möglich</li>
            <li>Die Zahlung erfolgt über PayPal</li>
            <li>Bei Zahlungsverzug behält sich der Anbieter die Sperrung des Zugangs vor</li>
        </ul>

        <h2>§ 5 Nutzungsrechte</h2>
        <p>Der Anbieter gewährt dem Nutzer ein einfaches, nicht übertragbares Recht zur Nutzung der Plattform für die Dauer des Vertragsverhältnisses. Eine Weitergabe der Zugangsdaten an Dritte oder eine gewerbliche Weitervermietung ist nicht gestattet.</p>

        <h2>§ 6 API-Nutzung & Drittdienste</h2>
        <p>Die Plattform nutzt Drittdienste (insbesondere OpenAI für KI-Funktionen). Der Nutzer stellt sicher, dass die von ihm zur Verarbeitung bereitgestellten Daten (Produkttexte, Kategorienamen, Bilder) keine Rechte Dritter verletzen. Der Anbieter haftet nicht für die Verfügbarkeit oder Qualität von Drittdiensten.</p>

        <h2>§ 7 Datensicherheit & API-Zugangsdaten</h2>
        <p>Der Nutzer ist verantwortlich für die Sicherheit seiner eingetragenen API-Zugangsdaten (Shopware, OpenAI). Der Anbieter speichert diese verschlüsselt und gibt sie nicht an Dritte weiter. Der Nutzer sollte API-Keys mit minimalen notwendigen Berechtigungen erstellen.</p>

        <h2>§ 8 Verfügbarkeit & Wartung</h2>
        <p>Der Anbieter strebt eine Verfügbarkeit der Plattform von 99% im Jahresmittel an. Hiervon ausgenommen sind Wartungsarbeiten, die nach Möglichkeit außerhalb der Hauptnutzungszeiten durchgeführt werden. Ein Anspruch auf ununterbrochene Verfügbarkeit besteht nicht.</p>

        <h2>§ 9 Haftungsbeschränkung</h2>
        <p>Der Anbieter haftet unbeschränkt bei Vorsatz und grober Fahrlässigkeit sowie bei Schäden aus der Verletzung des Lebens, des Körpers oder der Gesundheit. Im Übrigen ist die Haftung auf den vertragstypisch vorhersehbaren Schaden begrenzt.</p>
        <p>Der Anbieter übernimmt keine Haftung für durch KI generierte Inhalte. Der Nutzer ist verpflichtet, generierte SEO-Texte vor der Veröffentlichung zu prüfen.</p>

        <h2>§ 10 Kündigung</h2>
        <p>Beide Parteien können das Vertragsverhältnis jederzeit zum Ende der laufenden Abrechnungsperiode kündigen. Das Recht zur außerordentlichen Kündigung aus wichtigem Grund bleibt unberührt. Nach Kündigung werden alle Nutzerdaten innerhalb von 30 Tagen gelöscht.</p>

        <h2>§ 11 Änderungen der AGB</h2>
        <p>Der Anbieter behält sich vor, diese AGB mit einer Ankündigungsfrist von 30 Tagen zu ändern. Widerspricht der Nutzer nicht innerhalb dieser Frist, gelten die geänderten AGB als akzeptiert. Auf das Widerspruchsrecht wird im Rahmen der Ankündigung hingewiesen.</p>

        <h2>§ 12 Anwendbares Recht & Gerichtsstand</h2>
        <p>Es gilt das Recht der Bundesrepublik Deutschland unter Ausschluss des UN-Kaufrechts. Gerichtsstand für alle Streitigkeiten aus diesem Vertragsverhältnis ist, soweit gesetzlich zulässig, Kempten (Allgäu).</p>

        <p style="font-size:12px; color:#70757a; margin-top: 24px;">Stand: Februar 2026</p>

        <div class="footer-nav">
            <a href="{{ url('/') }}">← Zurück zur Startseite</a>
            <a href="{{ url('/imprint') }}">Impressum</a>
            <a href="{{ url('/privacy') }}">Datenschutz</a>
        </div>
    </div>
</body>
</html>
