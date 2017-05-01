<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/auth/login');
            }
        }
        elseif ($guard == 'admins') {
            if (Auth::guard('users')->check()) {
                return redirect()->action('Admin\AdminController@getUnauthorize');
            }
        }
        elseif ($guard == 'users') {
            if (Auth::guard('admins')->check()) {
                return redirect()->action('Website\MemberController@getUnauthorize');
            }
        }

        return $next($request);
    }
}
