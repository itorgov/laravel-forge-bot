<?php

namespace App\Http\Middleware;

use App\Facades\TelegramBot;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        TelegramBot::authenticate($request);

        if (Auth::check()) {
            return $next($request);
        }

        return response('OK');
    }
}
