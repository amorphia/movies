<?php


namespace App\Services;


use App\Movie;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\PHP;

class ScraperBoxOfficeMojo extends ScraperAbstract
{

    protected $client;
    protected $console;

    public function handle( $console, $client, $year )
    {
        $this->client = $client;
        $this->console = $console;

        $this->updateNewMovies( $year );
        $this->updateMovieGross( $year );
    }

    public function updateNewMovies( $year )
    {
        // get our new movies
        $movies = $this->newMoviesArray( $year );
        $this->console->question( "New Movies:" . PHP_EOL );

        // grab out existing movies
        $existing_movies = Movie::where( 'year', $year )->pluck( 'title' )->toArray();

        foreach( $movies as $movie ){
            // if we haven't already saved this movie insert it
            if( !in_array( $movie['title'], $existing_movies ) ){
                DB::table( 'movies' )->insertOrIgnore( $movie );
                $this->console->info( $movie['title'] );
            }
        }
    }

    public function newMoviesArray( $year ){

        // load crawler
        $crawler = $this->client->request('GET', "https://www.boxofficemojo.com/year/{$year}/?grossesOption=totalGrosses&sort=grossToDate");

        $movies = [];

        // filter through table rows
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
        $title = remove_bs( $title );

        $theaters = $node->children('.mojo-field-type-positive_integer')->eq(0)->text();
        $theaters= (int)preg_replace("/[^0-9]/", "", $theaters);

        $gross = $node->children('.mojo-field-type-money')->eq(1)->text();
        $gross = $this->formatGross( $gross );

        $timestamp = new \DateTime();

        // filter out da junk
        if( $rank > 500 || $theaters < 35  ) return;

        // return the movie array
        return [
            'title' => $title,
            'year' => $year,
            'gross' => $gross,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ];
    }



    public function updateMovieGross( $year )
    {

        $this->console->question( PHP_EOL . "Updated Grosses:" . PHP_EOL );

        // get an array of movie grosses for the year
        $movies_gross = $this->moviesGrossArray( $year );

        // get all the movies from this year and last
        $existing_movies = Movie::where( 'year', '>=', ($year - 1) )->get();

        foreach( $movies_gross as $movie ){

            // search the existing movies array for a movie with the same title and a lesser gross
            $existing = $existing_movies->where( 'title', $movie['title'] )->first();

            // if such a movie exists, update it
            if( $existing && $existing->gross < $movie['gross'] ){

                $start = '$' . number_format( $existing->gross );
                $end = '$' . number_format( $movie['gross'] );
                $this->console->info( "{$movie['title']}: {$start} => {$end}" );

                $existing->update([ 'gross' => $movie['gross'] ] );
            }
        }
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
        $title = $node->children('.mojo-field-type-release')->text();
        $title = remove_bs( $title );

        $gross = $node->children('.mojo-field-type-money')->eq(2)->text();
        $gross = $this->formatGross( $gross );


        // return the movie array
        return [
            'title' => $title,
            'gross' => $gross,
        ];
    }

    protected function formatGross( $string )
    {
        return (int)preg_replace("/[^0-9]/", "", $string );
    }

}
