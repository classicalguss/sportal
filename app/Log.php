<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Log
 *
 * @mixin \Eloquent
 */
class Log extends Model
{
    protected $table = "logs";
    protected $primaryKey = "id";
    protected $fillable = [
        'title', 'status', 'message', 'results'
    ];

}
