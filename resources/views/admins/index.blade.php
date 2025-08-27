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
        });
    </script>
@endsection


