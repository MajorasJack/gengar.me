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
        </dl>
    </header>
    {{-- Detail copy is optional per section, so a project without it shows only the facts above. --}}
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
