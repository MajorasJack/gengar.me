@use('App\Portfolio\ProjectStatus')
<section aria-labelledby="running-projects">
    <h2 id="running-projects">running projects</h2>
    <table class="projects">
        <thead>
            <tr><th>project</th><th>host</th><th>what it does</th><th>stack</th><th class="tests">tests</th></tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td class="name"><span @class(['dot', 'off' => $project->status === ProjectStatus::Private]) aria-hidden="true"></span>{{ $project->name }}</td>
                    @if ($project->url === null)
                        <td class="host private" data-label="host">{{ $project->visibility }}</td>
                    @else
                        <td class="host" data-label="host"><a href="{{ $project->url }}">{{ parse_url($project->url, PHP_URL_HOST) }}</a></td>
                    @endif
                    <td data-label="what it does">{{ $project->summary }}</td>
                    <td class="stack" data-label="stack">{{ implode(' · ', $project->stack) }}</td>
                    <td class="tests" data-label="tests">~{{ number_format($project->tests) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
<section aria-labelledby="smaller-tools">
    <h2 id="smaller-tools">smaller tools</h2>
    <ul class="tools">
        @foreach ($tools as $tool)
            <li><b>{{ $tool['name'] }}</b> {{ $tool['description'] }}</li>
        @endforeach
    </ul>
</section>
