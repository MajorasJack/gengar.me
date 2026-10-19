@use('App\Portfolio\ProjectStatus')
<section aria-labelledby="running-projects">
    <h2 id="running-projects">running projects</h2>
    <table class="projects" role="table">
        <thead role="rowgroup">
            <tr role="row"><th role="columnheader">project</th><th role="columnheader">host</th><th role="columnheader">what it does</th><th role="columnheader">stack</th><th class="tests" role="columnheader">tests</th></tr>
        </thead>
        <tbody role="rowgroup">
            @foreach ($projects as $project)
                <tr role="row">
                    <td class="name" role="cell"><span @class(['dot', 'off' => $project->status === ProjectStatus::Private]) aria-hidden="true"></span><a href="{{ route('projects.show', $project->slug, false) }}">{{ $project->name }}</a></td>
                    @if ($project->url === null)
                        <td class="host private" role="cell" data-label="host">{{ $project->visibility }}</td>
                    @else
                        <td class="host" role="cell" data-label="host"><a href="{{ $project->url }}">{{ parse_url($project->url, PHP_URL_HOST) }}</a></td>
                    @endif
                    <td role="cell" data-label="what it does">{{ $project->summary }}</td>
                    <td class="stack" role="cell" data-label="stack"><span>@foreach ($project->stack as $item)@if (! $loop->first)&nbsp;· @endif<span>{{ $item }}</span>@endforeach</span></td>
                    <td class="tests" role="cell" data-label="tests">~{{ number_format($project->tests) }}</td>
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
