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
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
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

        {{-- ================= ヘッダー ================= --}}
        @if(trim($__env->yieldContent('header')) != '')
            @yield('header')
        @else
            <nav class="navbar navbar-expand navbar-light shadow-sm mb-4">
                <div class="container m-0">
                    <a class="navbar-brand"
                        @if(Route::is('guest.*') && isset($store, $table))
                            href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}"
                        @else
                            href="{{ url('/') }}"
                        @endif>
                            <img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="Ordermate Logo" class="logo">
                    </a>

                    <p class="d-flex align-items-center justify-content-center m-0 w-100" style="height: 100%">
                        {{ $userStore->store_name ?? '' }}
                    </p>

                    <!-- 右側メニュー -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a id="navbarDropdownGuest" class="nav-link" href="#" role="button"
                                    data-bs-toggle="offcanvas" data-bs-target="#langMenu" aria-controls="langMenu">
                                    <i class="fa-solid fa-language fa-2x text-orange"></i>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a id="navbarDropdown" class="nav-link" href="#" role="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#sideMenu" aria-controls="sideMenu">
                                    <i class="fa-solid fa-bars fa-2x text-orange"></i>
                                </a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </nav>
        @endif
        {{-- =============== /ヘッダー =============== --}}

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
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center"><i class="fa-solid fa-flag me-2"></i> Japanese</a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center"><i class="fa-solid fa-flag me-2"></i> English</a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center"><i class="fa-solid fa-flag me-2"></i> Chinese</a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center"><i class="fa-solid fa-flag me-2"></i> Korean</a>
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
                        <a href="{{ route('manager.stores.index') }}"
                            class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <span class="me-2 d-flex justify-content-center" style="width: 24px;">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            Store Information
                        </a>
                        <a href="{{ route('manager.products.index') }}" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-utensils me-2"></i> Menu
                        </a>
                        <a href="{{ route('manager.order-list') }}" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-list-ul me-2"></i> Order List
                        </a>
                        <a href="#" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-boxes-packing me-2"></i> Takeout
                        </a>
                        <a href="{{ route('manager.custom.index') }}" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-table-cells me-2"></i> Custom
                        </a>
                        <a href="{{ route('manager.categories.index') }}" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-layer-group me-2"></i> Category
                        </a>
                        <a href="{{ route('manager.tables') }}" class="nav-link text-white px-3 py-2 d-flex align-items-center">
                            <i class="fa-solid fa-table me-2"></i> Table
                        </a>
                        <a href="{{ route('logout') }}"
                            class="nav-link text-white px-3 py-2 d-flex align-items-center mt-auto mb-5"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>
            </div>
        @endauth

        <main>
            @yield('content')
        </main>


        {{-- ================= フッター ================= --}}
        @if(trim($__env->yieldContent('footer')) != '')
            @yield('footer')
        @else
            <footer class="shadow-sm bg-light-mode">
                {{-- QRゲストの注文ページだけ表示 --}}
                @if (request()->routeIs('guest.*'))
                    <div class="container-fluid d-flex justify-content-between align-items-center py-2">
                        {{-- 左側（Total Price）--}}
                        <div>
                            <span class="fw-bold text-brown fs-4 ms-4">Total: </span>
                            <span class="h3 fw-bold text-brown" id="total-price">{{ number_format($totalPrice ?? 0, 2) }}</span>
                            @if($isPaid)
                                <span class="text-muted ms-2 fw-bolder">(paid)</span>
                            @endif
                        </div>
            
                        {{-- 右側（リンク4つ）--}}
                        @php
                            $storeName = request()->route('storeName');
                            $tableUuid = request()->route('tableUuid');
                        @endphp
                        <div class="d-flex gap-3">
                            <a href="{{ route('guest.orderHistory', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="nav-link p-0 fs-5 text-brown">Order History</a>
                            <a href="{{ route('guest.call', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="nav-link p-0 fs-5 text-brown">Call Staff</a>
                            {{-- 未決済なら Payment を表示 --}}
                            @if (empty($isPaid) || ! $isPaid)
                                <form action="{{ route('guest.payment', [$store->store_name, $table->uuid]) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-link nav-link p-0 fs-5 text-brown">Payment</button>
                                </form>
                            @endif

                            {{-- Checkout（確定）は常に表示）--}}
                            <form action="{{ route('guest.checkout', [$store->store_name, $table->uuid]) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link p-0  fs-5 text-brown">Checkout</button>
                            </form>
                            <a href="{{ route('guest.cart.show', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="nav-link p-0"><i class="fa-solid fa-cart-shopping fa-3x me-4 ms-2"></i>
                            <span id="cart-count"
                                class="position-absolute badge rounded-pill bg-orange d-flex justify-content-center align-items-center"
                                style="top: -9px; right: 10px; font-size: 1rem; width: 22px; height: 22px; {{ ($cartCount ?? 0) == 0 ? 'display:none;' : '' }}">
                                {{ $cartCount ?? 0 }}
                            </span>
                            </a>
                        </div>
                    </div>
                @endif
            
                {{-- 下段（共通 Copyright） --}}
                <div class="text-center py-2">
                    <p class="text-gray m-0">&copy; All Rights are reserved by ordermate</p>
                </div>
            @endif

        </footer>

    </div>

   @if (request()->routeIs('guest.*'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cartCountEl = document.getElementById("cart-count");
            const initialCount = parseInt("{{ $cartCount }}");
            if (cartCountEl && initialCount > 0) {
                cartCountEl.style.display = 'flex';
            }
        });
    </script>
    @stack('guest-scripts')
@endif

</body>
</html>
