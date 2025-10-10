<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

// use App\Mail\WelcomeMail;
// use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/manager';

    public function __construct()
    {
        // LPや登録画面もLPLocaleで多言語対応
        $this->middleware('LPLocale')->only(['showRegistrationForm']);
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);
    }

    protected function create(array $data)
    {
        Log::info('Register data:', $data);
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ✅ RequestまたはSessionから取得（Request優先）
        $lang = $data['lang'] ?? Session::get('lp_locale', config('app.locale'));

        Store::create([
            'user_id'    => $user->id,
            'store_name' => $user->name,
            'language'   => $lang,
        ]);

        // Mail::to($user->email)->send(new WelcomeMail());

        return $user;
    }
}
