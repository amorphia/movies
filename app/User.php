<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Movie;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




    /**
     *
     *  METHODS
     *
     */


    public function isAdmin()
    {
        return $this->role === 'admin';
    }


    public function recentMovies()
    {
        return $this->movies()->orderBy( 'movie_user.created_at', 'desc' )->take( 50 )->get();
    }


    public function seenTotal()
    {
        return $this->movies()->wherePivot( 'active', 1 )->count();
    }


    public function setMovieSeen( $movie )
    {
        // if we haven't ever set the relation yet
        if( ! $this->movies->contains( $movie ) ){
            $this->movies()->attach( $movie, [
                'active' => 1
            ]);
        } else {
        // if we've already set this relation
            $this->movies()->updateExistingPivot( $movie, [
                'active' => 1
            ]);
        }
    }

    public function setMovieUnseen( $movie )
    {
        // if we've already set this relation
        if( $this->movies->contains( $movie ) ) {
            $this->movies()->updateExistingPivot( $movie, [
                'active' => 0
            ]);
        }
    }

    /**
     *
     *  RELATIONSHIPS
     *
     */
    public function movies()
    {
        return $this->belongsToMany( Movie::class )
            ->withPivot( 'active', 'created_at' )
            ->withTimestamps();
    }
}
