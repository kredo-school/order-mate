<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class GuestLocale
{
    public function handle($request, Closure $next)
    {
        // セッションから guest_locale を取得、なければデフォルト
        $locale = Session::get('guest_locale', config('app.locale'));
        App::setLocale($locale);

        return $next($request);
    }
}
