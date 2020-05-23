<?php

namespace App\Console\Commands;

use App\Movie;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class importSeens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:seens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import seen movies';

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
    public function handle()
    {
        $this->info("Starting Import");

        $seens = DB::table( 'seens' )->get();
        $user = User::where( 'email', 'jeremy@jeremykalgreen.com' )->first();

        // start progress bar
        $bar = $this->output->createProgressBar( $seens->count() );
        $bar->start();

        foreach( $seens as $seen ){

            $title = explode( '(', $seen->title );
            $title = trim( $title[0] );

            $movie = Movie::where( 'title', 'like', "{$title}%" )->where( 'year', $seen->year )->first();

            if( $movie ){
                $user->setMovieSeen( $movie );
            } else {
                $this->error("No match for {$movie->title} - {$seen->year}");
            }

            // display output
            $bar->advance();
        }

        // stop progress bar
        $bar->finish();

    }
}
