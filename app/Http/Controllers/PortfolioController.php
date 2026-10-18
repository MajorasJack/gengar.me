<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Portfolio\Project;
use Illuminate\Contracts\View\View;

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
}
