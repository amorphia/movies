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

        $seens = DB::table( 'seens' )->orderBy( 'year', 'desc' )->get();
        $user = User::where( 'email', 'jeremy@jeremykalgreen.com' )->first();

        foreach( $seens as $seen ){

            $title = explode( '(', $seen->title );
            $title = trim( $title[0] );

            $movie = Movie::where( 'title', 'like', "{$title}%" )->where( 'year', $seen->year )->first();

            if( !$movie )
                $this->error("{$seen->title} - {$seen->year}");
            }

    }
}
