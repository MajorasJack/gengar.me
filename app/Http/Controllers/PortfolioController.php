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
        return view('home', ['projects' => array_map(Project::fromArray(...), config('portfolio.projects'))]);
    }
}
