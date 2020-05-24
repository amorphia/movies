<?php


namespace App\Services;


abstract class ScraperAbstract
{

    protected $client;
    protected $year;

    abstract public function handle( $console, $client, $year );

}
