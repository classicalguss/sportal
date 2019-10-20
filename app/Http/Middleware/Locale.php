<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Session;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = 'en';

        if(Auth::check()){
            $locale = Auth::user()->locale;
        } elseif(Session::has('locale')) {
            $locale = Session::get('locale');
        }

        app()->setLocale($locale);
        return $next($request);
    }
}
