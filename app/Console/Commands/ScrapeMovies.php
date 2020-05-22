<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Scraper;

class ScrapeMovies extends Command
{
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
        $year = $this->argument('year') ?? null;
        $message = $scraper->updateNewMovies( $year );
        $this->info( $message );
    }
}
