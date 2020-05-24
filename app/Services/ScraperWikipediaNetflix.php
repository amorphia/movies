<?php


namespace App\Services;


use App\Movie;
use Illuminate\Support\Facades\DB;

class ScraperWikipediaNetflix extends ScraperAbstract
{
    protected $client;
    protected $console;

    public function handle( $console, $client, $year = null )
    {
        $this->client = $client;
        $this->console = $console;

        $this->updateNetflixMovies();
    }


    public function updateNetflixMovies()
    {
        // get our new movies
        $movies = $this->moviesArray();
        $this->console->question( PHP_EOL . "Netflix Movies:" . PHP_EOL );

        // grab out existing movies
        $existing_movies = Movie::where( 'type', 'streaming' )->pluck( 'title' )->toArray();

        foreach( $movies as $movie ){
            // if we haven't already saved this movie insert it
            if( !in_array( $movie['title'], $existing_movies ) ){
                DB::table( 'movies' )->insertOrIgnore( $movie );
                $this->console->info( $movie['title'] );
            }
        }
    }

    public function moviesArray(){

        // load crawler
        $crawler = $this->client->request('GET', "https://en.wikipedia.org/wiki/List_of_original_films_distributed_by_Netflix");

        $movies = [];

        // filter through table rows
        $rows = $crawler->filter('#dramafilms tr')->each( function( $node ) use ( &$movies ){
            if( $result = $this->processMovieRow( $node ) ){
                $movies[] = $result;
            }
        });

        return $movies;
    }

    protected function processMovieRow( $node )
    {

        // skip rows without 5 kids
        if( $node->children()->count() !== 5 ) return;

        // skip rows that don't look like a movie row (in this case we are looking at the release date column )
        // or movie rows from future releases
        $date = strtotime( $node->children()->eq(2)->text() );
        $release = date('Y-m-d', $date );
        if( ! $date || $release > date( 'Y-m-d' ) ) return;

        $title = $node->children()->eq(0)->text();
        $title = remove_bs( $title );
        $reference = get_match( "\[[0-9]+\]", $title );
        $title = trim( str_replace( $reference, '', $title ) );

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
