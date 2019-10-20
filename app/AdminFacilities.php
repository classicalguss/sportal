<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AdminFacilities
 *
 * @mixin \Eloquent
 */
class AdminFacilities extends Model
{
    protected $table = 'admin_facilities';
    protected $fillable = [
        'facility_id', 'admin_id'
    ];
}
