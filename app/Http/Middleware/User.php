<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->token;
        $user = \App\Http\Controllers\UserController::authenticate($token);
        if ($token == "" || $token == null || !$user) {
            return response()->json(['code' => 403, 'message' => "Anda harus login dahulu"]);
        }
        return $next($request);
    }
}
