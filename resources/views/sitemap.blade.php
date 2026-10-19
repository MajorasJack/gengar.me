<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($paths as $path)
    <url>
        <loc>{{ sprintf('https://%s%s', $host, $path) }}</loc>
    </url>
@endforeach
</urlset>
