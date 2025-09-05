@extends('layouts.app')

@section('content')
    <div class="page-center"> {{-- 背景は透明・中央寄せ --}}
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
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ $email ?? old('email') }}" required autofocus>
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
            <button type="submit" class="btn btn-l btn-primary w-100">RESET PASSWORD</button>
            <a href="{{ route('login') }}" class="btn btn-l  btn-outline w-100 mt-2">Back to Login</a>
        </form>

        {{-- 下部リンク --}}
        <div class="link-row justify-content-end mt-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        {{-- SNSアイコン（オプション） --}}
        <div class="social-row mt-4">
            <a href="#"><img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="LP"></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-instagram fa-lg text-orange"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-x-twitter fa-lg text-orange"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-facebook fa-lg text-orange"></i></a>
        </div>
    </div>
@endsection
