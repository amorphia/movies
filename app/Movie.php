<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

    protected $guarded = [];

    /**
     *
     *  RELATIONSHIPS
     *
     */

    public function users()
    {
        return $this->belongsToMany( User::class )
            ->withPivot( 'active', 'created_at' )
            ->withTimestamps();
    }
}
