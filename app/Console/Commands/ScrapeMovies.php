<?php

namespace App\Console\Commands;

use App\Movie;
use Illuminate\Console\Command;
use App\Services\Scraper;

class ScrapeMovies extends Command
{

    protected $scraper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:movies {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape Movies from box office mojo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle( Scraper $scraper )
    {
        $this->scraper = $scraper;
        $year_array = [];

        if( $this->argument('year') ){
            $year_array[] = $this->argument('year');
        } else {
            $min = Movie::min( 'year' );
            $max = Movie::max( 'year' );

            for( $i = $min; $i <= $max; $i++ ){
                $year_array[] = $i;
            }
        }

        foreach( $year_array as $year ){
            $this->scrapeYear( $year );
        }
        
    }

    protected function scrapeYear( $year )
    {
        $message = $this->scraper->updateNewMovies( $year );
        $this->info( $message );
    }
}
