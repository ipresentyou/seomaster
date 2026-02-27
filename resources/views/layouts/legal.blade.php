<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – SEOmaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500&family=DM+Serif+Display&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root { --blue: #1a73e8; --text-1: #202124; --text-2: #3c4043; --border: #dadce0; --bg-soft: #f8f9fa; }
        body { font-family: 'Roboto', sans-serif; color: var(--text-2); line-height: 1.8; margin: 0; }
        nav { padding: 0 40px; height: 64px; display: flex; align-items: center; border-bottom: 1px solid var(--border); }
        .nav-brand { font-family: 'Google Sans', sans-serif; font-size: 20px; font-weight: 500; text-decoration: none; color: var(--text-1); }
        .nav-brand span { color: var(--blue); }
        .content { max-width: 800px; margin: 60px auto; padding: 0 20px; }
        h1 { font-family: 'DM Serif Display', serif; color: var(--text-1); font-size: 44px; margin-bottom: 32px; }
        h2 { color: var(--text-1); margin-top: 40px; border-bottom: 1px solid var(--border); }
        .box { background: var(--bg-soft); border: 1px solid var(--border); padding: 32px; border-radius: 8px; }
    </style>
</head>
<body>
<nav>
    <a href="{{ url('/') }}" class="nav-brand">shopware6<span>seomaster</span></a>
</nav>
<main class="content">
    @yield('content')
</main>
</body>
</html>