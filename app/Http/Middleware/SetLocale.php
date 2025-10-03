<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // デフォルト言語（envのAPP_LOCALE or APP_FALLBACK_LOCALEでもOK）
        $locale = config('app.locale');

        // manager側のセッションを優先チェック
        if (Session::has('manager_locale')) {
            $locale = Session::get('manager_locale');
        }

        // guest側のセッションを上書きチェック
        if (Session::has('guest_locale')) {
            $locale = Session::get('guest_locale');
        }

        // Laravel の言語を切り替え
        App::setLocale($locale);

        return $next($request);
    }
}
