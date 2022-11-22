<?php


namespace App\Services;


use App\Movie;
use Illuminate\Support\Facades\DB;

class ScraperRottenTomatoes extends ScraperAbstract
{
    protected $client;
    protected $console;
    protected $url = "https://www.rottentomatoes.com/browse/movies_at_home/sort:newest?page=5";
    protected $targetYear;


    public function handle( $console, $client, $year = null )
    {
        $this->client = $client;
        $this->console = $console;
        $this->targetYear = intval($year) ?? intval(date("Y"));
        $this->updateServiceMovies( $this->url );
    }


    public function updateServiceMovies( $url )
    {
        // get our new movies
        $movies = $this->moviesArray( $url );

        if( $this->console ) $this->console->line( PHP_EOL . "Streaming Movies:" . PHP_EOL );

        $yearsArray = [$this->targetYear, $this->targetYear - 1];

        // grab out existing movies
        $existing_movies = Movie::whereIn("year", $yearsArray)
            ->pluck( 'title' )
            ->toArray();

        // if we have already saved this movie filter it out
        $movies = array_filter($movies, function($movie) use ($existing_movies){
            return !in_array( $movie['title'], $existing_movies );
        });

        foreach( $movies as $movie ){
            DB::table( 'movies' )->insertOrIgnore( $movie );
            if( $this->console ) $this->console->info( $movie['title'] );
        }
    }

    public function moviesArray( $url ){

        // load crawler
        $crawler = $this->client->request('GET', $url );
        $movies = [];

        // filter through table rows
        $rows = $crawler->filter("[data-qa='discovery-media-list-item-caption']")->each( function( $node ) use ( &$movies ){
            if( $result = $this->processMovieRow( $node ) ){
                $movies[] = $result;
            }
        });

        return $movies;
    }

    protected function processMovieRow( $node )
    {
        $date = $node->filter("[data-qa='discovery-media-list-item-start-date']")->text();
        $title = $node->filter("[data-qa='discovery-media-list-item-title']")->text();

        // if our title or date includes double curly braces, this row isn't rendered right
        if(str_contains($title, "{{") || str_contains($date, "{{")) return;

        // skip rows missing a date column, or in the future
        $date = str_replace("Streaming ", "", $date);
        $date = strtotime( $date );
        $release = date('Y-m-d', $date );

        if( ! $date || $release > date( 'Y-m-d' ) ) return;

        $year = date( 'Y', $date );
        $timestamp = new \DateTime();

        // return the movie array
        return [
            'title' => $title,
            'year' => $year,
            'release' => $release,
            'type' => 'streaming',
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ];
    }

}
