@extends('layouts.app')

@section('content')
    <div class="login-width"> {{-- 再利用可能・背景は透明 --}}
        {{-- ロゴ --}}
        <div class="logo-area">
            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo" class="logo-main">
        </div>

        {{-- フォーム（下線スタイル適用） --}}
        <form method="POST" action="{{ route('register') }}" class="form-underline">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label for="name">Name</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password">Password
                </label>
                <small class="form-text text-muted" style="line-height:1;">
                    * Password must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.
                </small>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="mb-3">
                <label for="password-confirm">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                    autocomplete="new-password">
            </div>

            {{-- 送信ボタン --}}
            <button type="submit" class="btn btn-l btn-primary w-100">REGISTER</button>
            <a href="{{ route('login') }}" class="btn btn-l btn-outline w-100 mt-2">Log in</a>
        </form>

        {{-- 下部リンク --}}
        <div class="link-row justify-content-end">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        {{-- SNSアイコン --}}
        <div class="social-row">
            <a href="#">
                <img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="LP">
            </a>
            <a href="#" class="social-icon">
                <i class="fa-brands fa-instagram fa-lg text-orange"></i>
            </a>
            <a href="#" class="social-icon">
                <i class="fa-brands fa-x-twitter fa-lg text-orange"></i>
            </a>
            <a href="#" class="social-icon">
                <i class="fa-brands fa-facebook fa-lg text-orange"></i>
            </a>
        </div>
    </div>
@endsection
