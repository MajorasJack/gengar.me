<header>
    <div class="prompt"><b>{{ $profile['email'] }}</b> ~ {{ $profile['location'] }} · {{ $profile['remote'] }}</div>
    <h1>{{ $profile['name'] }}, {{ $profile['role'] }}</h1>
    <p class="intro">{{ $profile['intro'] }}</p>
    <nav class="links" aria-label="Contact">
        <a class="primary" href="{{ $profile['cv'] }}">Download CV</a>
        <a href="mailto:{{ $profile['email'] }}">{{ $profile['email'] }}</a>
        <a href="{{ $profile['linkedin'] }}">LinkedIn</a>
        <a href="{{ $profile['github'] }}">GitHub</a>
    </nav>
</header>
