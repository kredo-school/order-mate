@extends('layouts.base')
@section('title', 'Admin Index')
@section('content')
    <div class="d-flex align-items-center justify-content-between mx-5 mt-4 mb-3">
        <h1 class="mb-0 text-brown">Stores</h1>

        <div class="d-flex align-items-center gap-3">
            {{-- 通知アイコン --}}
            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-regular fa-bell fa-2x text-orange"></i>
                <span id="admin-nav-badge" class="badge bg-danger ms-2"></span>
            </a>

            {{-- ログアウト --}}
            <a href="{{ route('logout') }}" class="nav-link d-flex align-items-center text-orange"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket fa-lg"></i>
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <div class="container mb-4 mt-4">
        <form action="{{ route('admin.chat.broadcast') }}" method="POST">
            @csrf

            {{-- タイトル + ドロップダウンを横並び --}}
            <div class="d-flex align-items-center mb-3">
                <h4 class="me-3 mb-0 text-brown">Broadcast to Managers</h4>

                <div class="dropdown flex-grow-1">
                    <button class="btn btn-outline-secondary text-brown dropdown-toggle text-start" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Select Managers
                    </button>
                    <ul class="dropdown-menu w-100 p-2" style="max-height: 250px; overflow-y: auto;">
                        <li>
                            <div class="form-check">
                                <input type="checkbox" id="select-all" class="form-check-input">
                                <label for="select-all" class="form-check-label">Select All</label>
                            </div>
                        </li>
                        <hr>
                        @foreach ($managers->sortBy('store.store_name') as $manager)
                            @if ($manager->store)
                                <li>
                                    <div class="form-check">
                                        <input type="checkbox" name="manager_ids[]" value="{{ $manager->id }}"
                                            id="manager-{{ $manager->id }}" class="form-check-input manager-checkbox">
                                        <label for="manager-{{ $manager->id }}" class="form-check-label">
                                            {{ $manager->store->store_name }} ({{ $manager->name }})
                                        </label>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Analyticsボタンを追加 --}}
                <a href="{{ route('admin.analytics') }}" class="btn btn-primary ms-2">
                    Analytics
                </a>
            </div>

            {{-- メッセージ入力欄 --}}
            <div class="mb-3">
                <textarea name="content" class="form-control" placeholder="Enter your message..." required></textarea>
            </div>

            {{-- 送信ボタン --}}
            <button type="submit" class="btn btn-primary">Broadcast</button>
        </form>
    </div>

    <div class="container">
        <div class="row">
            @foreach ($all_stores as $store)
                <div id="store-{{ $store->id }}" class="col-md-4 mb-4">
                    <a href="{{ route('admin.show', $store->id) }}" class="text-decoration-none text-dark">
                        <div class="store-box p-3 h-100">
                            <div class="row g-0">
                                {{-- 左側 1/3 店舗写真 --}}
                                <div class="col-4">
                                    @if ($store->store_photo ?? false)
                                        <img src="{{ Storage::url($store->store_photo) }}" alt="{{ $store->store_name }}"
                                            class="img-fluid h-100 w-100 object-fit-cover rounded text-brown">
                                    @else
                                        <div class="no-image d-flex flex-column align-items-center justify-content-center h-100 rounded"
                                            style="background-color: #f5f0e6;">
                                            <i class="fa-solid fa-shop fa-lg text-brown mb-2 mt-2"></i>
                                            <p class="text-brown mb-0 fs-6">No Image</p>
                                        </div>
                                    @endif
                                </div>
                                {{-- 右側 2/3 情報 --}}
                                <div class="col-8">
                                    <div class="ps-3">
                                        <h5 class="fw-bold text-brown">
                                            {{ $store->store_name }}
                                            <span class="badge bg-danger store-unread-badge">
                                                {{ $store->unread_messages_count ?? '' }}
                                            </span>
                                        </h5>
                                        <p class="mb-1 text-brown"><i class="fa-solid fa-phone text-muted"></i>
                                            {{ $store->phone ?? '-' }}</p>
                                        <p class="mb-1 text-brown"><i class="fa-solid fa-envelope text-muted"></i>
                                            {{ $store->user->email ?? Auth::user()->email }}</p>
                                        <p class="mb-1 text-brown"><i class="fa-solid fa-location-dot text-muted"></i>
                                            {{ $store->address ?? '-' }}</p>
                                        <p class="mb-1 text-brown "><i class="fa-solid fa-user-tie text-muted"></i>
                                            {{ $store->manager_name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    <style>
        .store-box {
            border: 1px solid transparent;
            border-radius: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .store-box:hover {
            border-color: #ccc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            fetch('/chat/unread-total')
                .then(res => res.json())
                .then(data => {
                    const navBadge = document.querySelector("#admin-nav-badge");
                    if (navBadge) {
                        navBadge.textContent = data.count > 0 ? data.count : '';
                    }
                });



            // --- 未読バッジ更新 ---
            function refreshBadges() {
                fetch('/chat/unread-per-store')
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(store => {
                            const badge = document.querySelector(
                                `#store-${store.id} .store-unread-badge`);
                            if (badge) {
                                if (store.count > 0) {
                                    badge.style.display = 'inline-flex';
                                    badge.textContent = store.count;
                                } else {
                                    badge.style.display = 'inline-flex';
                                    badge.textContent = store.count ?? 0;
                                }
                            }
                        });
                    });
            }

            function refreshNavBadge() {
                fetch('/chat/unread-total')
                    .then(res => res.json())
                    .then(data => {
                        const navBadge = document.querySelector("#admin-nav-badge");
                        if (navBadge) {
                            navBadge.textContent = data.total > 0 ? data.total : '';
                        }
                    });
            }

            refreshBadges();
            window.addEventListener("pageshow", e => {
                if (e.persisted) refreshBadges();
            });

            refreshNavBadge();
            setInterval(refreshNavBadge, 1000);

            // --- チェックボックス制御 ---
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.manager-checkbox');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        selectAll.checked = Array.from(checkboxes).every(c => c.checked);
                    });
                });
            }

            // --- ドロップダウン内で閉じないようにする ---
            document.querySelectorAll('.dropdown-menu input, .dropdown-menu label').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            // --- フォーム送信前バリデーション ---
            const broadcastForm = document.querySelector('form[action="{{ route('admin.chat.broadcast') }}"]');
            if (broadcastForm) {
                broadcastForm.addEventListener('submit', function(e) {
                    const checkedCount = document.querySelectorAll('.manager-checkbox:checked').length;
                    if (checkedCount === 0) {
                        e.preventDefault();
                        alert("Choose at least one manager to send the message.");
                    }
                });
            }
        });
    </script>

@endsection
