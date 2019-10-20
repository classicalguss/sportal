<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FacilityImage
 *
 * @mixin \Eloquent
 */
class FacilityImage extends Model
{
    protected $table = "facility_images";
    protected $primaryKey = 'id';
    protected $fillable = [
        'facility_id', 'image_id', 'image_type'
    ];

    /**
     * image Types
     */
    const IMAGETYPE_NORMAL = 0;
    const IMAGETYPE_MAIN = 1;
    const IMAGETYPE_LOGO = 2;
}
