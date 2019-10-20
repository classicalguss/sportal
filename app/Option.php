<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Option
 *
 * @mixin \Eloquent
 */
class Option extends Model
{
    protected $table = 'options';
    protected $primaryKey = 'id';
}
