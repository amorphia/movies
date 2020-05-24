<?php

namespace App\Console\Commands;

use App\User;
use App\Movie;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class DataFiddle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiddle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generic command I use as a scratch pad to edit data';

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

        $seens = DB::table( 'seens' )->where('matched', 0 )->get();

        foreach( $seens as $seen ) {

            $this->info( "$seen->title - $seen->year" );

            /*
            $hash = $this->getHash( $seen->title );
            $movie = DB::table( 'numbers' )
                ->where('hash', $hash )
                ->where( 'year', $seen->year )
                ->first();
            if( $movie ){

                DB::table( 'movie_user' )->insertOrIgnore([
                    'movie_id' => $movie->id,
                    'user_id' => 1,
                    'active' => 1
                ]);

                DB::table( 'seens' )
                    ->where( 'id', $seen->id )
                    ->update( ['matched'=> 1] );

                $this->info("{$seen->title} ({$seen->year}) matched {$movie->title} ({$movie->year})" );
            }
*/
        }
    }

    protected function getHash( $string )
    {
        $string = strtolower( str_replace( '&', 'and', $string ) );
        return preg_replace("/[^A-Za-z0-9]/", '', $string );
    }

}
