@extends('layouts.app')

@section('content')
    <div class="page-center"> {{-- 再利用可能・背景は透明 --}}
        {{-- ロゴ --}}
        <div class="logo-area">
            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo" class="logo-main">
        </div>

        {{-- フォーム（下線スタイル適用） --}}
        <form method="POST" action="{{ route('login') }}" class="form-underline">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email">Username</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- 送信ボタン（既存の .btn .btn-primary を活用） --}}
            <button type="submit" class="btn btn-primary w-100">LOG IN</button>
            <a href="{{ route('register') }}" class="btn btn-outline w-100 mt-2">Register</a>


        </form>

        {{-- 下部リンク --}}
        <div class="link-row justify-content-end">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        {{-- SNSアイコン --}}
        <div class="social-row">
            {{-- LPロゴ --}}
            <a href="#">
                <img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="LP">
            </a>

            {{-- Instagram --}}
            <a href="#" class="social-icon">
                <i class="fa-brands fa-instagram fa-lg text-orange"></i>
            </a>

            {{-- X --}}
            <a href="#" class="social-icon">
                <i class="fa-brands fa-x-twitter fa-lg text-orange"></i>
            </a>

            {{-- Facebook --}}
            <a href="#" class="social-icon">
                <i class="fa-brands fa-facebook fa-lg text-orange"></i>

            </a>
        </div>
    </div>
@endsection
