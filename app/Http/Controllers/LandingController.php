<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Landing');
    }
}