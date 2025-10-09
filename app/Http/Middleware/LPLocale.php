<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LPLocale
{
    public function handle($request, Closure $next)
    {
        // URLに ?lang= があれば更新
        if ($request->has('lang')) {
            Session::put('lp_locale', $request->get('lang'));
        }

        // 既にSessionに言語があればそれを適用、なければデフォルト
        $locale = Session::get('lp_locale', config('app.locale'));
        App::setLocale($locale);

        // すべてのレスポンスでセッション維持
        return $next($request);
    }
}
