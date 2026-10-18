<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title>{{ config('app.name') }}</title>
    <link rel="preload" href="/fonts/geist-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/geist-mono-latin.woff2" as="font" type="font/woff2" crossorigin>
    @vite('resources/css/app.css')
</head>
<body>
    <a class="skip-link" href="#content">Skip to content</a>
    <div class="wrap">
        <main id="content">
            @yield('content')
        </main>
        <footer class="site-footer">
            built with Laravel + Blade · <a href="https://github.com/MajorasJack/gengar.me">source on GitHub</a>
        </footer>
    </div>
</body>
</html>
