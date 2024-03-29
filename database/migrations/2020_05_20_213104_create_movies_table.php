<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'title' );
            $table->integer( 'year' );
            $table->bigInteger('gross' )->nullable();
            $table->integer( 'rank' )->nullable();
            $table->string( 'release' )->nullable();
            $table->string( 'type' )->default( 'theater' );
            $table->timestamps();
            $table->unique(['title', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
