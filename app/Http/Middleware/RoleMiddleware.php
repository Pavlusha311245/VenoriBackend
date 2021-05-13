<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class RoleMiddleware
 * @package App\Http\Middleware
 */
class RoleMiddleware
{
    /**
     * Handle an incoming Permissions request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @return mixed
     2*/
    public function handle($request, Closure $next, $role)
    {
        if(!auth()->check())
        {
            return response()->json(['message' => 'You are not logged in.']);
        }

        if(!auth()->user()->hasRole($role))
        {
            return response()->json(['message' => 'You are have not access.']);
        }

        return $next($request);
    }
}
