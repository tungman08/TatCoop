<?php

namespace App\Http\Middleware;

use App;
use Closure;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $language = null)
    {
        App::setLocale(empty($language) ? Config::get('app.locale') : $language);

        return $next($request);
    }
}
