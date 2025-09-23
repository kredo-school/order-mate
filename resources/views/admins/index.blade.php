@extends('layouts.base')
@section('title', 'Admin Index')
@section('content')
    <h1 class="mb-4 d-inline">Stores</h1>
    <a href="{{ route('logout') }}"
    class="nav-link d-flex justify-content-end mb-3"
    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <span class="me-2 d-flex justify-content-center" style="width: 24px;">
            <i class="fa-solid fa-right-from-bracket"></i>
        </span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    <div class="container mb-4">
        <form action="{{ route('admin.chat.broadcast') }}" method="POST">
            @csrf
    
            {{-- タイトル + ドロップダウンを横並び --}}
            <div class="d-flex align-items-center mb-3">
                <h4 class="me-3 mb-0">Broadcast to Managers</h4>
                
                <div class="dropdown flex-grow-1">
                    <button class="btn btn-outline-secondary dropdown-toggle text-start" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
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
                        @foreach($managers->sortBy('store.store_name') as $manager)
                            @if($manager->store)
                                <li>
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="manager_ids[]" 
                                               value="{{ $manager->id }}" 
                                               id="manager-{{ $manager->id }}" 
                                               class="form-check-input manager-checkbox">
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
                <div id="store-{{$store->id}}" class="col-md-4 mb-4">
                    <a href="{{ route('admin.show', $store->id) }}" class="text-decoration-none text-dark">
                        <div class="store-box p-3 h-100">
                            <div class="row g-0">
                                {{-- 左側 1/3 店舗写真 --}}
                                <div class="col-4">
                                    @if($store->store_photo ?? false)
                                        <img src="{{ Storage::url($store->store_photo) }}" 
                                             alt="{{ $store->store_name }}" 
                                             class="img-fluid h-100 w-100 object-fit-cover rounded">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100 rounded">
                                            No Image
                                        </div>
                                    @endif
                                </div>
                                {{-- 右側 2/3 情報 --}}
                                <div class="col-8">
                                    <div class="ps-3">
                                        <h5 class="fw-bold">
                                            {{ $store->store_name }}
                                            @if($store->unread_messages_count > 0)
                                                <span class="badge bg-danger">{{ $store->unread_messages_count }}</span>
                                            @endif
                                        </h5>
                                        <p class="mb-1"><i class="fa-solid fa-phone text-muted"></i> {{ $store->phone ?? '-' }}</p>
                                        <p class="mb-1"><i class="fa-solid fa-envelope text-muted"></i> {{ $store->user->email ?? Auth::user()->email }}</p>
                                        <p class="mb-1"><i class="fa-solid fa-location-dot text-muted"></i> {{ $store->address ?? '-' }}</p>
                                        <p class="mb-1"><i class="fa-solid fa-user-tie text-muted"></i> {{ $store->manager_name ?? '-' }}</p>
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // --- 未読バッジ更新 ---
        function refreshBadges() {
            fetch('/chat/unread-per-store')
                .then(res => res.json())
                .then(data => {
                    data.forEach(store => {
                        const badge = document.querySelector(`#store-${store.id} .badge`);
                        if (badge) {
                            badge.textContent = store.count > 0 ? store.count : '';
                        }
                    });
                });
        }
    
        refreshBadges();
        window.addEventListener("pageshow", e => {
            if (e.persisted) refreshBadges();
        });
    
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


