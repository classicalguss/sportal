<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VenueVenues
 *
 * @mixin \Eloquent
 */
class VenueVenues extends Model
{
    protected $table = "venue_venues";
    protected $primaryKey = "parent_id";
    protected $fillable = [
        'parent_id', 'child_id'
    ];
}
