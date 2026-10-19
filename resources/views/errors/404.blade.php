@extends('layouts.site')

@section('title', sprintf('Page not found, %s', config('portfolio.profile.name')))
@section('description', 'There is no page at this address on gengar.me. The homepage lists every project.')

@section('content')
    <header>
        <div class="prompt"><a href="/">{{ config('portfolio.canonical_host') }}</a> ~ 404</div>
        <h1>Nothing lives at this address</h1>
        <p class="intro">The page may have moved, or the link may have a typo. Every project is listed on the homepage.</p>
        <nav class="links" aria-label="Recovery">
            <a class="primary" href="/">Back to the homepage</a>
        </nav>
    </header>
@endsection
