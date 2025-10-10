<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/manager';

    public function __construct()
    {
        // LPやログイン画面でもLPLocaleを適用
        $this->middleware('LPLocale')->only(['showLoginForm']);
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated($request, $user)
    {
        // ✅ Managerログイン時にストア言語へ切り替え
        if ($user->role == User::ROLE_MANAGER) {
            if ($user->store) {
                $lang = $user->store->language ?? config('app.locale');
                Session::put('lp_locale', $lang);
                Session::put('manager_locale', $lang);
                App::setLocale($lang);
            }

            return redirect()->route('manager.home');
        }

        // ✅ Admin
        if ($user->role == User::ROLE_ADMIN) {
            return redirect()->route('admin.index');
        }

        // その他はデフォルト
        return redirect($this->redirectTo);
    }

    protected function loggedOut($request)
    {
        // ✅ ログアウト後はLP言語に戻す
        $lang = Session::get('lp_locale', config('app.locale'));
        App::setLocale($lang);

        return redirect('/login');
    }
}
