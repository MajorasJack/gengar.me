<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Portfolio\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

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
     * An unknown slug gets the 404 page (CONT-8).
     */
    public function project(string $slug): View
    {
        return view('project', [
            'profile' => config('portfolio.profile'),
            'project' => Project::fromArray(
                array_find(config('portfolio.projects'), fn (array $project): bool => $project['slug'] === $slug) ?? abort(Response::HTTP_NOT_FOUND),
            ),
        ]);
    }

    /**
     * Serve the sitemap: the homepage, plus each project's detail page while
     * the `projects.show` route exists, so it never lists a 404.
     * URLs use `portfolio.canonical_host` rather than APP_URL.
     */
    public function sitemap(): Response
    {
        return response()->view('sitemap', [
            'host' => config('portfolio.canonical_host'),
            'paths' => [
                '/',
                ...Route::has('projects.show')
                    ? array_map(fn (array $project): string => sprintf('/projects/%s', $project['slug']), config('portfolio.projects'))
                    : [],
            ],
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
