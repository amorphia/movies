<?php


namespace App\Services;


use App\Movie;
use Illuminate\Support\Facades\DB;

class ScraperMovieInsider extends ScraperAbstract
{
    protected $client;
    protected $console;
    protected $services = [
        'Hulu' => 'https://www.movieinsider.com/c4528/hulu',
        'Netflix' => 'https://www.movieinsider.com/c3899/netflix',
        'Disney+' => 'https://www.movieinsider.com/c4595/disney-plus-streaming',
        'Amazon' => 'https://www.movieinsider.com/c3223/amazon-studios',
        'HBO Max' => 'https://www.movieinsider.com/c4801/hbo-max'
    ];


    public function handle( $console, $client, $year = null )
    {
        $this->client = $client;
        $this->console = $console;

        foreach( $this->services as $service => $url ){
            $this->updateServiceMovies( $service, $url );
        }

    }


    public function updateServiceMovies( $service, $url )
    {
        // get our new movies
        $movies = $this->moviesArray( $url );

        if( $this->console ) $this->console->line( PHP_EOL . "{$service} Movies:" . PHP_EOL );

        // grab out existing movies
        $existing_movies = Movie::where( 'type', 'streaming' )->pluck( 'title' )->toArray();

        foreach( $movies as $movie ){
            // if we haven't already saved this movie insert it
            if( !in_array( $movie['title'], $existing_movies ) ){
                DB::table( 'movies' )->insertOrIgnore( $movie );
                if( $this->console ) $this->console->info( $movie['title'] );
            }
        }
    }

    public function moviesArray( $url ){

        // load crawler
        $crawler = $this->client->request('GET', $url );
        $movies = [];

        // filter through table rows
        $rows = $crawler->filter('#items tr')->each( function( $node ) use ( &$movies ){
            if( $result = $this->processMovieRow( $node ) ){
                $movies[] = $result;
            }
        });

        return $movies;
    }

    protected function processMovieRow( $node )
    {

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
