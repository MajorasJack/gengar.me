@extends('layouts.site')

@section('title', sprintf('%s, %s', $project->name, $profile['name']))
@section('description', $project->summary)

@section('content')
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->summary }}</p>
    @if ($project->url === null)
        <p class="host private">{{ $project->visibility }}</p>
    @else
        <p class="host"><a href="{{ $project->url }}">{{ parse_url($project->url, PHP_URL_HOST) }}</a></p>
    @endif
@endsection
