<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/manager';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated($request, $user)
    {
        // ✅ Manager
        if ($user->role == User::ROLE_MANAGER) {
            if ($user->store) {
                session(['manager_locale' => $user->store->language]);
                app()->setLocale($user->store->language);
            }
    
            return redirect()->route('manager.home');
        }
    
        // ✅ Admin
        if ($user->role == User::ROLE_ADMIN) {
            return redirect()->route('admin.index');
        }
    
        // デフォルト
        return redirect('/manager');
    }
    
    protected function loggedOut($request)
    {
        return redirect('/login'); // ログアウト後に飛ばしたいURL
    }
}
