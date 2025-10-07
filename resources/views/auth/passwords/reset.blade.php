@extends('layouts.app')

@section('content')
    <div class="login-width">
        {{-- ロゴ --}}
        <div class="logo-area">
            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo" class="logo-main">
        </div>

        {{-- パスワードリセットフォーム --}}
        <form method="POST" action="{{ route('password.update') }}" class="form-underline">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="hidden" name="email" value="{{ request()->email }}">

                @error('email')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password">New Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password">
                <small class="form-text text-muted" style="font-size:12px; line-height:1.2;">
                    * Password must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.
                </small>
                @error('password')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password Confirm --}}
            <div class="mb-3">
                <label for="password-confirm">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                    autocomplete="new-password">
            </div>

            {{-- 送信ボタン --}}
            <button type="submit" class="btn btn-l btn-primary w-100 mb-2">RESET PASSWORD</button>
            <a href="{{ route('login') }}" class="btn btn-l btn-outline w-100">Back to Login</a>
        </form>

        {{-- SNSアイコン --}}
        <div class="social-row mt-4 text-center">
            <a href="#"><img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="LP"
                    style="height:36px;"></a>
            <a href="#" class="social-icon mx-2"><i class="fa-brands fa-instagram fa-lg text-orange"></i></a>
            <a href="#" class="social-icon mx-2"><i class="fa-brands fa-x-twitter fa-lg text-orange"></i></a>
            <a href="#" class="social-icon mx-2"><i class="fa-brands fa-facebook fa-lg text-orange"></i></a>
        </div>
    </div>
@endsection
