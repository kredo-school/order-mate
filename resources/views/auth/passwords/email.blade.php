@extends('layouts.app')

@section('content')
    <div class="login-width">
        {{-- ロゴ --}}
        <div class="logo-area">
            <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo" class="logo-main">
        </div>

        {{-- パスワードリセットメール送信フォーム --}}
        <form method="POST" action="{{ route('password.email') }}" class="form-underline">
            @csrf

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Email --}}
            <div class="mb-3">
                <label for="email">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback" style="display:block;font-size:12px;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- 送信ボタン --}}
            <button type="submit" class="btn btn-l btn-primary w-100">
                Send Password Reset Link
            </button>

            {{-- 戻るボタン --}}
            <a href="{{ route('login') }}" class="btn btn-l btn-outline w-100 mt-2">Back to Login</a>
        </form>

        {{-- SNSアイコン --}}
        <div class="social-row mt-4">
            <a href="#"><img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="LP"></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-instagram fa-lg text-orange"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-x-twitter fa-lg text-orange"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-facebook fa-lg text-orange"></i></a>
        </div>
    </div>
@endsection
