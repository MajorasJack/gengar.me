@extends('layouts.site')

@section('content')
    @include('partials.header', ['profile' => config('portfolio.profile')])
@endsection
