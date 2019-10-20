<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VenueType
 *
 * @mixin \Eloquent
 */
class VenueType extends Model
{
    protected $table = "venue_types";
    protected $primaryKey = "id";
    protected $fillable = [
        'venue_id', 'type_id'
    ];
}
