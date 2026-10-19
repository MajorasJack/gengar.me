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
     * Serve the sitemap: the homepage, plus each project's detail page once
     * S3-01 registers the `projects.show` route, so it never lists a 404.
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
