<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SEOmaster' }} – SEO Platform</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; background: #f8f9fa; font-family: 'Roboto', sans-serif;">
    
    <!-- Simple Navigation -->
    <nav style="background: white; border-bottom: 1px solid #dadce0; padding: 0 40px; height: 64px; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ url('/') }}" style="text-decoration: none;">
            <img src="{{ asset('images/logo_seomaster.svg') }}" width="240" alt="Logo">
        </a>
        <div style="display: flex; gap: 20px; align-items: center;">
            <a href="{{ url('/') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">Startseite</a>
            <a href="{{ route('login') }}" style="color: #1a73e8; text-decoration: none; font-size: 14px; font-weight: 500;">Anmelden</a>
            <a href="{{ route('register') }}" style="background: #1a73e8; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;">Kostenlos starten</a>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div style="padding: 20px 40px 0;">
        @if(session('success'))
            <div style="background: #e6f4ea; border-left: 4px solid #1e8e3e; color: #137333; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fce8e6; border-left: 4px solid #d93025; color: #c5221f; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                ❌ {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div style="background: #e8f0fe; border-left: 4px solid #1a73e8; color: #174ea6; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                ℹ️ {{ session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background: #fce8e6; border-left: 4px solid #d93025; color: #c5221f; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                <strong>❌ Fehler:</strong>
                <ul style="margin-top:6px; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main style="padding: 20px 40px 40px;">
        @yield('content')
    </main>

    <!-- Simple Footer -->
    <footer style="background: white; border-top: 1px solid #dadce0; padding: 40px; text-align: center; margin-top: 40px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; max-width: 800px; margin: 0 auto 40px;">
            <div>
                <h4 style="margin-bottom: 12px; color: #202124;">Produkt</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ url('/#features') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">Features</a>
                    <a href="{{ url('/#pricing') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">Preise</a>
                    <a href="{{ route('register') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">Registrieren</a>
                </div>
            </div>
            <div>
                <h4 style="margin-bottom: 12px; color: #202124;">Rechtliches</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('legal.impressum') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">Impressum</a>
                    <a href="{{ route('legal.datenschutz') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">Datenschutz</a>
                    <a href="{{ route('legal.agb') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">AGB</a>
                </div>
            </div>
            <div>
                <h4 style="margin-bottom: 12px; color: #202124;">Kontakt</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('contact.index') }}" style="color: #5f6368; text-decoration: none; font-size: 14px;">📧 Kontaktformular</a>
                </div>
            </div>
        </div>
        <div style="color: #80868b; font-size: 12px; padding-top: 20px; border-top: 1px solid #dadce0;">
            © 2026 SEOmaster. Alle Rechte vorbehalten. Made in Germany
        </div>
    </footer>

</body>
</html>
