@extends('layouts.site')

@section('title', sprintf('Uses, %s', $profile['name']))
@section('description', sprintf('The tools %s uses for PHP and Laravel development, and how %s is built and hosted.', $profile['name'], config('portfolio.canonical_host')))

@section('content')
    <header>
        <div class="prompt"><a href="/">{{ config('portfolio.canonical_host') }}</a> ~ uses</div>
        <h1>Uses</h1>
        <p class="intro">The tools I work with day to day, and how this site is built and hosted.</p>
    </header>
    @foreach ($sections as $section)
        <section aria-labelledby="uses-{{ $loop->index }}">
            <h2 id="uses-{{ $loop->index }}">{{ $section['title'] }}</h2>
            <dl class="facts">
                @foreach ($section['items'] as $item)
                    <div>
                        <dt>{{ $item['name'] }}</dt>
                        <dd>{{ $item['note'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @endforeach
@endsection
