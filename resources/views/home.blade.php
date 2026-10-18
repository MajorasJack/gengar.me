@extends('layouts.site')

@section('content')
    @include('partials.header', ['profile' => config('portfolio.profile'), 'available' => config('portfolio.available')])
    @include('partials.what-i-do', ['areas' => config('portfolio.what_i_do')])
    @include('partials.projects', ['projects' => $projects, 'tools' => config('portfolio.smaller_tools')])
@endsection
