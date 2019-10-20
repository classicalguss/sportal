<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VenueImage
 *
 * @mixin \Eloquent
 */
class VenueImage extends Model
{
    protected $table = "venue_images";
    protected $primaryKey = "id";
    protected $fillable = [
        'venue_id', 'image_id', 'image_type'
    ];

    /**
     * image Types
     */
    public const IMAGETYPE_NORMAL = 0;
    public const IMAGETYPE_MAIN = 1;
    public const IMAGETYPE_LOGO = 2;
}
