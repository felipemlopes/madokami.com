<?php

namespace Madokami\Http\Middleware;

use Closure;
use Madokami\Models\Ban;
use Illuminate\Session\Store as SessionStore;

class CheckBans
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
        $ip = $request->getClientIp();
        $count = Ban::where('ip', '=', $ip)->count();
        if ($count > 0) {
            return response('banned', 403, [ 'Content-Type' => 'text/plain' ]);
        }

        return $next($request);
    }
}
