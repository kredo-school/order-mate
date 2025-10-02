<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Store;

class GuestLocale
{
    public function handle($request, Closure $next)
    {
        $locale = Session::get('guest_locale');

        if (!$locale) {
            $storeName = $request->route('storeName');
            if ($storeName) {
                $store = Store::where('store_name', $storeName)->first();
                if ($store && $store->language) {
                    $locale = $store->language;
                    Session::put('guest_locale', $locale);
                }
            }
        }

        $locale ??= config('app.locale'); // fallback
        App::setLocale($locale);

        return $next($request);
    }
}
