<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class RoleMiddleware
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
		if(Auth::guest() || !$request->user()->hasRole($roles))
		{
			return redirect()->to('/');
		}

		return $next($request);
	}
}