<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
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
        $user = User::find(Auth::guard()->id());
        $id = !is_null($request->route('member')) ? intval($request->route('member')) : intval($request->route('id'));

        if ($user->member_id != $id) {
            return redirect()->route('website.member.unauthorize');
        }
        
        return $next($request);
    }
}
