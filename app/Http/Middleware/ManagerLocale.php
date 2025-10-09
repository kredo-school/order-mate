<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ManagerLocale
{
    public function handle($request, Closure $next)
    {
        $locale = config('app.locale'); // envの言語設定をデフォルトにする

         // ログインしている場合、店舗の言語設定を優先する
        if (Auth::check()) {
            $store = Auth::user()->store;
            if ($store && $store->language) {
                $locale = $store->language;
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
