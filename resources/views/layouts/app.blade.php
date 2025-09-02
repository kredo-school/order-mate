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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- fontawesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        /* デフォルト: PCやタブレットは25%幅 */
        body .offcanvas.offcanvas-end {
            --bs-offcanvas-width: 25% !important;
            width: 25% !important;
        }

        /* スマホは全幅 */
        @media (max-width: 768px) {
            body .offcanvas.offcanvas-end {
                --bs-offcanvas-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand navbar-light shadow-sm mb-4">
            <div class="container m-0">
                <a class="navbar-brand"
                    href="{{ isset($table) ? route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) : url('/') }}">
                    <img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="Ordermate Logo" class="logo">
                </a>

                <p class="d-flex align-items-center justify-content-center m-0 w-100" style="height: 100%">
                    {{ $userStore->store_name ?? '' }}
                </p>

                <!-- 右側メニュー -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <!-- 未ログイン時：翻訳アイコンでオフキャンバス -->
                            <a id="navbarDropdownGuest" class="nav-link" href="#" role="button"
                                data-bs-toggle="offcanvas" data-bs-target="#langMenu" aria-controls="langMenu">
                                <i class="fa-solid fa-language fa-2x text-orange"></i>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <!-- トグルボタン -->
                            <a id="navbarDropdown" class="nav-link" href="#" role="button" data-bs-toggle="offcanvas"
                                data-bs-target="#sideMenu" aria-controls="sideMenu">
                                <i class="fa-solid fa-bars fa-2x text-orange"></i>
                            </a>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        <!-- 未ログイン時のオフキャンバス（言語切替メニュー） -->
        @guest
            <div class="offcanvas offcanvas-end bg-orange text-white border-0" tabindex="-1" id="langMenu"
                aria-labelledby="langMenuLabel" style="background-color: var(--primary-orange) !important;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="langMenuLabel">Language</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0 d-flex flex-column">
                    <div class="nav flex-column flex-grow-1">
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-flag me-2"></i> Japanese
                        </a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-flag me-2"></i> English
                        </a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-flag me-2"></i> Chinese
                        </a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-flag me-2"></i> Korean
                        </a>
                    </div>
                </div>
            </div>
        @endguest

        <!-- オフキャンバスメニュー -->
        @auth
            <div class="offcanvas offcanvas-end bg-orange text-white border-0" tabindex="-1" id="sideMenu"
                aria-labelledby="sideMenuLabel" style="background-color: var(--primary-orange) !important;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sideMenuLabel">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0 d-flex flex-column">
                    <div class="nav flex-column flex-grow-1">
                        <a href="{{route('manager.stores.index')}}" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            Store Information
                        </a>
                        <a href="{{ route('manager.products.index') }}"
                            class="nav-link text-white px-3 py-2 d-flex align-items-center">
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
                        <a href="{{ route('manager.custom.index') }}"
                            class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                                <i class="fa-solid fa-table-cells"></i>
                            </span>
                            Custom
                        </a>
                        <a href="{{ route('manager.categories.index') }}"
                            class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            Category
                        </a>
                        <a href="{{ route('logout') }}"
                            class="nav-link text-white px-3 py-2 d-flex align-items-center mt-auto mb-5"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </span>
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        @endauth

        <main class="">
            @yield('content')
        </main>

        <footer class="shadow-sm bg-light-mode">
            {{-- QRゲストの注文ページだけ表示 --}}
            @if (request()->routeIs('guest.*'))
                <div class="container-fluid d-flex justify-content-between align-items-center py-2">
                    {{-- 左側（Total Price）--}}
                    <div>
                        <span class="fw-bold">Total: </span>
                        <span id="total-price">-</span>
                    </div>
        
                    {{-- 右側（リンク4つ）--}}
                    <div class="d-flex gap-3">
                        <a href="#" class="nav-link p-0">Order History</a>
                        <a href="{{route('guest.call', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid])}}" class="nav-link p-0">Call Staff</a>
                        <a href="#" class="nav-link p-0">Checkout</a>
                        <a href="#" class="nav-link p-0">Payment</a>
                    </div>
                </div>
            @endif
        
            {{-- 下段（共通 Copyright） --}}
            <div class="text-center py-2">
                <p class="text-gray m-0">
                    &copy; All Rights are reserved by ordermate
                </p>
            </div>
        </footer>
        
    </div>

    @stack('scripts')

</body>

</html>
