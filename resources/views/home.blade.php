@extends('layouts.site')

@section('title', sprintf('%s, %s', $profile['name'], $profile['role']))
@section('description', $profile['description'])

@section('content')
    @include('partials.header')
    @include('partials.what-i-do')
    @include('partials.projects')
@endsection
