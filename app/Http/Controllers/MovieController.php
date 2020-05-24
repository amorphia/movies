<?php

namespace App\Http\Controllers;

use App\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
                    ->where( 'movies.active', 1 )
                    ->orderBy( 'gross', 'desc' )
                    ->orderBy( 'rank', 'asc' )
                    ->orderBy( 'title' )
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
                    ->where( 'active' , 1 )
                    ->take( 10 )
                    ->orderBy( 'gross', 'desc' )
                    ->get();

        if( $movies->count() ){
            return $movies;
        }

        $no_results_content = auth()->user()->id !== 1 ? "No Results" : "<a href='/new'>No Results</a>";
        return $movies->count() ? $movies : [[ "title" => $no_results_content, "year" => date( "y") ]];
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
        checkAdmin();

        return view( 'movies.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        checkAdmin();

        $validated = $request->validate([
            'title' => Rule::unique('movies' )->where( function ( $query ) use ( $request ) {
                return $query->where( 'year', $request->year );
            }),
            'year' => 'required|digits:4|integer|min:1930|max:' . (date('Y') + 1 ),
            'gross' => 'nullable',
            'type' => 'in:theater,streaming'
        ]);

        $movie = Movie::create( $validated );

        if( $request->seen ){
            auth()->user()->setMovieSeen( $movie );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Movie $movie)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function edit(Movie $movie)
    {
        checkAdmin();
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
        checkAdmin();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        checkAdmin();

        $movie->update([ 'active' => 0 ]);
    }
}
