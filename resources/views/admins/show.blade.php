@extends('layouts.base')
@section('title', 'Admin Store')
@section('content')

  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <a href="{{ url()->previous() }}" class="">
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
        <p class="card-text"><i class="fa-solid fa-phone text-muted"></i> {{ $store->phone ?? '-' }}</p>
        <p class="card-text"><i class="fa-solid fa-envelope text-muted"></i> {{ $store->user->email ?? Auth::user()->email }}</p>
        <p class="card-text"><i class="fa-solid fa-location-dot text-muted"></i> {{ $store->address ?? '-' }}</p>
        <p class="card-text"><i class="fa-solid fa-user-tie text-muted"></i> {{ $store->manager_name ?? '-' }}</p>
      </div>
      
      {{-- Chat Column --}}
      @include('chats.chat', ['chat' => $chat, 'messages' => $messages])
    </div>
  </div>

@endsection