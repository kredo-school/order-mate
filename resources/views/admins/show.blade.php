@extends('layouts.base')
@section('title', 'Admin Store')
@section('content')

  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <a href="{{ route('admin.index') }}" class="">
        <h5 class="d-inline text-brown">
          <i class="fa-solid fa-angle-left text-orange"></i> Store Info
        </h5>
      </a>
    </div>

    <div class="row">
      <div class="col-md-4 text-center mb-3">
        <div class="store-photo-wrapper mx-auto mb-3">
          @if($store->store_photo ?? false)
            <img src="{{ Storage::url($store->store_photo) }}" 
                 class="store-photo rounded img-fluid" 
                 alt="{{ $store->store_name }}">
          @else
            <div class="store-photo bg-secondary d-flex align-items-center justify-content-center rounded">
              <i class="fa-solid fa-shop fa-2x text-white"></i>
            </div>
          @endif
        </div>
      
        <h4 class="card-title">{{ $store->store_name }}</h4>
        <div class="d-flex justify-content-center mt-3">
            <div class="text-brown">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid fa-location-dot fa-2x me-2"></i>
                    <span>{{ $store->address ?? '-' }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid fa-phone fa-2x me-2"></i>
                    <span>{{ $store->phone ?? '-' }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid fa-user-tie fa-2x me-2"></i>
                    <span>{{ $store->manager_name ?? '-' }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid fa-clock fa-2x me-2"></i>
                    <span>{{ $store->open_hours ?? '-' }}</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-envelope fa-2x me-2"></i>
                    <span>{{ $store->user->email ?? Auth::user()->email }}</span>
                </div>
            </div>
        </div>
      </div>
      
      {{-- Chat Column --}}
      @include('chats.chat', ['chat' => $chat, 'messages' => $messages])
    </div>
  </div>

@endsection