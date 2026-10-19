<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title>@yield('title', config('app.name'))</title>
    {{-- Pages that set a description get the full set of search and sharing tags (NFR-7). --}}
    @hasSection('description')
        @php
            $canonical = sprintf('https://%s/%s', config('portfolio.canonical_host'), ltrim(request()->path(), '/'));
            $person = [
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => config('portfolio.profile.name'),
                'jobTitle' => ucfirst(config('portfolio.profile.role')),
                'url' => sprintf('https://%s/', config('portfolio.canonical_host')),
                'sameAs' => [config('portfolio.profile.linkedin'), config('portfolio.profile.github')],
            ];
        @endphp
        <meta name="description" content="@yield('description')">
        <link rel="canonical" href="{{ $canonical }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('portfolio.canonical_host') }}">
        <meta property="og:title" content="@yield('title')">
        <meta property="og:description" content="@yield('description')">
        <meta property="og:url" content="{{ $canonical }}">
        <meta property="og:image" content="https://{{ config('portfolio.canonical_host') }}/og.png">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ config('portfolio.profile.name') }}, {{ config('portfolio.profile.role') }}">
        <meta name="twitter:card" content="summary_large_image">
        <script type="application/ld+json">@json($person, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)</script>
    @endif
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
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
