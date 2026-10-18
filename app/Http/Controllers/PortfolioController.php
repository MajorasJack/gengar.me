<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PortfolioController extends Controller
{
    /**
     * Show the homepage.
     */
    public function home(): View
    {
        return view('home');
    }
}
