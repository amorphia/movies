<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Movie;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $max = Movie::max( 'year' );
        $min = Movie::min( 'year' );

        $seen_total = auth()->user()->seenTotal();

        return view( 'home', compact( 'max', 'min', 'seen_total' ) );
    }
}
