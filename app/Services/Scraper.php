<?php


namespace App\Services;

use App\Movie;
use Illuminate\Support\Facades\DB;

class Scraper
{

    protected $client = null;

    public function __construct( \Goutte\Client $client )
    {
        $this->client = $client;
    }


    public function updateNewMovies( $year = null )
    {
        $year = $year ?? date('Y' );

        $movies = $this->newMoviesArray( $year );

        $message = "New Movies:" . PHP_EOL . PHP_EOL;
        $existingMovies = Movie::where( 'year', $year )->pluck( 'title' )->toArray();

        foreach( $movies as $movie ){

            if( !in_array( $movie['title'], $existingMovies ) ){
                DB::table( 'movies' )->insertOrIgnore( $movies );
                $message .= $movie['title'] . PHP_EOL;
            }
        }

        return $message;
    }

    public function newMoviesArray( $year ){

        $crawler = $this->client->request('GET', "https://www.boxofficemojo.com/year/{$year}/?grossesOption=totalGrosses&sort=grossToDate");
        $movies = [];

        $rows = $crawler->filter('.mojo-body-table tr')->each( function( $node ) use ( &$movies, $year ){
            if( $result = $this->processNewMovieRow( $node, $year ) ){
                $movies[] = $result;
            }
        });

        return $movies;
    }

    protected function processNewMovieRow( $node, $year )
    {
        // skip header rows
        if( $node->children()->eq(1)->nodeName() !== 'td' ) return;

        // format row data
        $rank = (int)$node->children('.mojo-field-type-rank')->text();
        $title = $node->children('.mojo-field-type-release')->text();
        $release = $node->children('.mojo-field-type-date')->eq(0)->text();

        $theaters = $node->children('.mojo-field-type-positive_integer')->eq(1)->text();
        $theaters= (int)preg_replace("/[^0-9]/", "", $theaters);

        $gross = $node->children('.mojo-field-type-money')->eq(1)->text();
        $gross = (int)preg_replace("/[^0-9]/", "", $gross );

        $timestamp = new \DateTime();

        // filter out da junk
        if( $rank > 300
            || $node->filter( 'span.a-size-small' )->count() > 0
            || $theaters < 50  ) return;

        // return the movie array
        return [
            'title' => $title,
            'year' => $year,
            'gross' => $gross,
            'release' => $release,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ];
    }



    public function updateMovieGross( $year = null )
    {
        $year = $year ?? date('Y' );

        $moviesGross = $this->moviesGrossArray( $year );
        // get all the movies from this year and last
        $existingMovies = Movie::where( 'year', '>=', ($year - 1) )->pluck( 'title' )->toArray();

        $message = 'Updated Gross:' .PHP_EOL . PHP_EOL;

        foreach( $moviesGross as $movie ){
            // if that movie exists to update
            if( in_array( $movie['title'], $existingMovies ) ){

                DB::table('movies')
                    ->where( 'title',  $movie['title'] )
                    ->where( 'release',  $movie['release'] )
                    ->update( [ 'gross' => $movie['gross'] ] );

                $message .= "{$movie['title']} - {$movie['gross']}" . PHP_EOL ;
            }
        }

        return $message;

    }


    protected function moviesGrossArray( $year ){

        $crawler = $this->client->request('GET', "https://www.boxofficemojo.com/year/{$year}/");
        $movies = [];

        $rows = $crawler->filter('.mojo-body-table tr')->each( function( $node ) use ( &$movies, $year ){
            if( $result = $this->processGrossMovieRow( $node ) ){
                $movies[] = $result;
            }
        });

        return $movies;
    }

    protected function processGrossMovieRow( $node )
    {
        // skip header rows
        if( $node->children()->eq(1)->nodeName() !== 'td' ) return;

        // format row data
        $rank = (int)$node->children('.mojo-field-type-rank')->text();
        $title = $node->children('.mojo-field-type-release')->text();
        $release = $node->children('.mojo-field-type-date')->eq(0)->text();

        $theaters = $node->children('.mojo-field-type-positive_integer')->eq(0)->text();
        $theaters= (int)preg_replace("/[^0-9]/", "", $theaters);

        $gross = $node->children('.mojo-field-type-money')->eq(2)->text();
        $gross = (int)preg_replace("/[^0-9]/", "", $gross );


        // filter out da junk
        if( $rank > 400
            || stripos( $title, 'Re-release') !== false
            || $theaters < 50  ) return;

        // return the movie array
        return [
            'title' => $title,
            'gross' => $gross,
            'release' => $release,
        ];
    }

}
