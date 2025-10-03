<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ManagerLocale
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $store = $user->store; // User → Store 関係がある前提
            $locale = $store?->language ?? config('app.locale');
            App::setLocale($locale);
        }

        return $next($request);
    }
}
