<?php

namespace App\Http\Controllers;

use App\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $request->validate([
            'years' => 'array',
            'years.*' => 'digits:4|integer|min:1900|max:'.( date('Y') )
        ]);

        $movies = DB::table('movies')
                    ->whereIn( 'year', $request->years )
                    ->leftJoin('movie_user', 'movies.id', '=', 'movie_user.movie_id')
                    ->select('movies.*', 'movie_user.active', 'movie_user.created_at AS seen_at')
                    ->orderBy( 'gross', 'desc' )
                    ->orderBy( 'rank', 'asc' )
                    ->get();

        $movies = $movies->groupBy( 'year' );

        return $movies;
    }



    public function toggle( Request $request, Movie $movie )
    {
        $user = auth()->user();

        if( $request->active ){
            $user->setMovieSeen( $movie );
        } else {
            $user->setMovieUnseen( $movie );
        }
    }


    public function search( Request $request )
    {
        if( ! $request->searchTerm ) return;

        $movies = Movie::where( 'title', 'like', "%{$request->searchTerm}%" )
                    ->take( 10 )
                    ->orderBy( 'gross', 'desc' )
                    ->get();

        return $movies->count() ? $movies : [[ "title" => "No Results", "year" => date( "y") ]];
    }


    public function recent()
    {
        $user = auth()->user();
        return $user->recentMovies();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
