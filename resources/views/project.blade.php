@extends('layouts.site')

@section('title', sprintf('%s, %s', $project->name, $profile['name']))
@section('description', $project->summary)

@section('content')
    <header>
        <div class="prompt"><a href="/">{{ config('portfolio.canonical_host') }}</a> ~ projects / {{ $project->slug }}</div>
        <h1>{{ $project->name }}</h1>
        <p class="intro">{{ $project->summary }}</p>
        <dl class="facts">
            <div>
                <dt>host</dt>
                @if ($project->url === null)
                    <dd class="private">{{ $project->visibility }}</dd>
                @else
                    <dd class="host"><a href="{{ $project->url }}">{{ parse_url($project->url, PHP_URL_HOST) }}</a></dd>
                @endif
            </div>
            <div>
                <dt>stack</dt>
                <dd class="stack">@foreach ($project->stack as $item)@if (! $loop->first)&nbsp;· @endif<span>{{ $item }}</span>@endforeach</dd>
            </div>
            <div>
                <dt>tests</dt>
                <dd>~{{ number_format($project->tests) }}, checked {{ date('j F Y', strtotime($project->testsCheckedAt)) }}</dd>
            </div>
            @isset($project->source)
                <div>
                    <dt>source</dt>
                    <dd class="host"><a href="{{ $project->source }}">{{ ltrim(parse_url($project->source, PHP_URL_PATH), '/') }}</a></dd>
                </div>
            @endisset
        </dl>
    </header>
    @isset($project->screenshot)
        <figure class="screenshot">
            <img src="{{ $project->screenshot['src'] }}" alt="{{ $project->screenshot['alt'] }}" width="{{ $project->screenshot['width'] }}" height="{{ $project->screenshot['height'] }}" fetchpriority="high" decoding="async">
        </figure>
    @endisset
    {{-- Detail copy is optional per section, so a project without it shows only the facts above. --}}
    @isset($project->detail['overview'])
        <section class="prose" aria-labelledby="overview">
            <h2 id="overview">what it is</h2>
            <p>{{ $project->detail['overview'] }}</p>
        </section>
    @endisset
    @isset($project->detail['problem'])
        <section class="prose" aria-labelledby="problem">
            <h2 id="problem">the problem</h2>
            <p>{{ $project->detail['problem'] }}</p>
        </section>
    @endisset
    @isset($project->detail['built'])
        <section class="prose" aria-labelledby="built">
            <h2 id="built">what was built</h2>
            <p>{{ $project->detail['built'] }}</p>
        </section>
    @endisset
    @isset($project->detail['notes'])
        <section class="prose" aria-labelledby="notes">
            <h2 id="notes">engineering notes</h2>
            <ul>
                @foreach ($project->detail['notes'] as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
        </section>
    @endisset
@endsection
