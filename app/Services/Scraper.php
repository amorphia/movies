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
                                 ScraperNetflix $netflix,
                                 ScraperWikipediaDisney $wikipediaDisney )
    {
        $this->client = $client;
        $this->scrapers[] = $boxOfficeMojo;
        $this->scrapers[] = $netflix;
        $this->scrapers[] = $wikipediaDisney;
    }


    public function scrape( $year, $console )
    {
        foreach ( $this->scrapers as $scraper ){
            $output = $scraper->handle( $console, $this->client, $year );
        }
    }

}
