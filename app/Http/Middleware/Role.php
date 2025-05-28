<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle($request, Closure $next, ...$roles)
{
    $roles = ['admin', 'staff', 'hafiz']; 
    if (in_array(Auth::user()->role, $roles)) {
        return $next($request);
    }

    return abort(401, 'Unauthorized');
}
}
