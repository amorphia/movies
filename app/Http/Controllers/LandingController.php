<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index(){
        return view( 'welcome' );
    }
}
