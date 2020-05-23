<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Movie;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/test', function(){

    $seens = DB::table( 'seens' )->get();

    $set = [];

    foreach( $seens as $seen ){
        $movie = Movie::where( 'title', $seen->title )->where( 'year', $seen->year )->get();
        if( !$movie ){
            auth()->user()->setMovieSeen( $movie );
            $set[] = $seen;
        }
    }

    return $set;

});

// Landing
Route::get('/', 'LandingController@index' );

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout' );

Route::get('login/{provider}', 'Auth\LoginController@redirect');
Route::get('login/{provider}/callback','Auth\LoginController@Callback');

// csrf token
Route::get( '/admin/csrf', 'CsrfController@csrf' )->name( 'csrf' );

// Home
Route::get('/home', 'HomeController@index')->name('home');

// Movies
Route::post( '/movies/fetch', 'MovieController@index' );
Route::post( '/movies/search', 'MovieController@search' );
Route::get( '/movies/recent', 'MovieController@recent' );
Route::post( '/movie/{movie}/toggle', 'MovieController@toggle' );
