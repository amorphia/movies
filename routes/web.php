<?php

use Illuminate\Support\Facades\Route;
use Weidner\Goutte;
use App\Movie;
use Illuminate\Support\Facades\DB;

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

Route::get( '/test/{year}', function( $year ){

    $crawler = Scraper::request('GET', "https://www.boxofficemojo.com/year/{$year}/?grossesOption=totalGrosses&sort=grossToDate");
    $movies = [];

    $rows = $crawler->filter('.mojo-body-table tr')->each( function( $node ) use ( &$movies, $year ){

        // skip header rows and keep just the cells
        if( $node->children()->eq(1)->nodeName() !== 'td' ) return;


        $rank = (int)$node->children('.mojo-field-type-rank')->text();
        $title = $node->children('.mojo-field-type-release')->text();
        $release = $node->children('.mojo-field-type-date')->eq(0)->text();

        $theaters = $node->children('.mojo-field-type-positive_integer')->eq(1)->text();
        $theaters= (int)preg_replace("/[^0-9]/", "", $theaters);

        $gross = $node->children('.mojo-field-type-money')->eq(1)->text();
        $gross = (int)preg_replace("/[^0-9]/", "", $gross );

        $timestamp = new \DateTime();

        if( $rank > 300
            || stripos( $title, 'Re-release') !== false
            || $theaters < 50  ) return;

        $movies[] = [
            'title' => $title,
            'year' => $year,
            'gross' => $gross,
            'release' => $release,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ];

    });


    /*
    $rows = $crawler->filter('.mojo-body-table tr')->each( function( $node ) use ( &$movies ){

        // skip header rows and keep just the cells
        if( $node->children()->eq(1)->nodeName() !== 'td' ) return;


        $rank = (int)$node->children('.mojo-field-type-rank')->text();
        $title = $node->children('.mojo-field-type-release')->text();
        $release = $node->children('.mojo-field-type-date')->text();
        $gross = $node->children('.mojo-field-type-money')->eq(2)->text();
        $gross = (int)preg_replace("/[^0-9]/", "", $gross );

        //if( $rank > 300 || stripos( $title, 'Re-release' ) !== false ) return;

        $movies[] = [
            'title' => $title,
            'gross' => $gross,
            'release' => $release,
        ];

    });
    */

    foreach( $movies as $movie ){
        DB::table( 'movies' )->insertOrIgnore( $movies );
    }

    dd( $movies );

});

// Landing
Route::get('/', 'LandingController@index' );

Auth::routes();

Route::get('login/{provider}', 'Auth\LoginController@redirect');
Route::get('login/{provider}/callback','Auth\LoginController@Callback');

// csrf token
Route::get( '/admin/csrf', 'CsrfController@csrf' )->name( 'csrf' );

// Home
Route::get('/home', 'HomeController@index')->name('home');
