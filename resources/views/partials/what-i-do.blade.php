<section aria-labelledby="what-i-do">
    <h2 id="what-i-do">what I do</h2>
    <div class="grid">
        @foreach ($areas as $area)
            <div>
                <h3>{{ $area['title'] }}</h3>
                <p>{{ $area['description'] }}</p>
            </div>
        @endforeach
    </div>
</section>
