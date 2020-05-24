<?php


namespace App\Services;

use App\Movie;
use Illuminate\Support\Facades\DB;

class Scraper
{

    protected $client = null;
    protected $scrapers = [];

    public function __construct( \Goutte\Client $client,
                                 ScraperBoxOfficeMojo $boxOfficeMojo,
                                 ScraperWikipediaNetflix $wikipediaNetflix,
                                 ScraperWikipediaDisney $wikipediaDisney )
    {
        $this->client = $client;
        $this->scrapers[] = $boxOfficeMojo;
        $this->scrapers[] = $wikipediaNetflix;
        $this->scrapers[] = $wikipediaDisney;
    }


    public function scrape( $year, $console )
    {
        foreach ( $this->scrapers as $scraper ){
            $output = $scraper->handle( $console, $this->client, $year );
        }
    }

}
