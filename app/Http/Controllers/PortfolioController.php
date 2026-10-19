<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Portfolio\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class PortfolioController extends Controller
{
    /**
     * Show the homepage.
     */
    public function home(): View
    {
        return view('home', [
            'profile' => config('portfolio.profile'),
            'available' => config('portfolio.available'),
            'areas' => config('portfolio.what_i_do'),
            'projects' => array_map(Project::fromArray(...), config('portfolio.projects')),
            'tools' => config('portfolio.smaller_tools'),
        ]);
    }

    /**
     * Show one featured project's detail page, found by slug in `portfolio.projects`.
     * A slug the project had before a rename redirects permanently to its current
     * page, and an unknown slug gets the 404 page (CONT-8).
     */
    public function project(string $slug): View|RedirectResponse
    {
        $projects = array_map(Project::fromArray(...), config('portfolio.projects'));
        $renamed = array_find($projects, fn (Project $project): bool => in_array($slug, $project->previousSlugs, true));

        if ($renamed !== null) {
            return new RedirectResponse(route('projects.show', $renamed->slug, false), Response::HTTP_MOVED_PERMANENTLY);
        }

        return view('project', [
            'profile' => config('portfolio.profile'),
            'project' => array_find($projects, fn (Project $project): bool => $project->slug === $slug) ?? abort(Response::HTTP_NOT_FOUND),
        ]);
    }

    /**
     * Serve the sitemap: the homepage and each project's detail page.
     * URLs use `portfolio.canonical_host` rather than APP_URL.
     */
    public function sitemap(): Response
    {
        return response()->view('sitemap', [
            'host' => config('portfolio.canonical_host'),
            'paths' => [
                '/',
                ...array_map(fn (array $project): string => sprintf('/projects/%s', $project['slug']), config('portfolio.projects')),
            ],
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
