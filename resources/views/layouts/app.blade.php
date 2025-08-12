<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- fontawesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        /* オフキャンバス横幅を1/4に */
        .offcanvas.offcanvas-end {
            width: 25% !important;
            max-width: none;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <p class="d-flex align-items-center justify-content-center m-0 w-100" style="height: 100%">
                    {{ Auth::user()->name }}
                </p>

                <!-- 右側メニュー -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <!-- トグルボタン -->
                            <a id="navbarDropdown" class="nav-link" href="#" role="button"
                               data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu">
                                <i class="fa-solid fa-bars orange"></i>
                            </a>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <!-- オフキャンバスメニュー -->
        @auth
        <div class="offcanvas offcanvas-end bg-orange text-white border-0" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="sideMenuLabel">Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0 d-flex flex-column">
                <nav class="nav flex-column flex-grow-1">
                    <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        Account Information
                    </a>
                    <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-utensils"></i>
                        </span>
                        Menu
                    </a>
                    <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-list-ul"></i>
                        </span>
                        Order List
                    </a>
                    <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </span>
                        Takeout
                    </a>
                    <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-table-cells"></i>
                        </span>
                        Custom
                    </a>
                    <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-layer-group"></i>
                        </span>
                        Category
                    </a>
                    <a href="{{ route('logout') }}" class="nav-link text-white px-3 py-2 d-flex align-items-center mt-auto mb-5"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </span>
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </nav>
            </div>
        </div>

        @endauth

        <main class="d-flex align-items-center">
            @yield('content')
        </main>

        <footer class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="mx-auto">
                <p class="gray">
                    &copy;All Rights are reserved by ordermate
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
