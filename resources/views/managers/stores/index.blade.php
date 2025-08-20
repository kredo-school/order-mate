@extends('layouts.app')

@section('title', 'Store Info')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <a href="{{ url()->previous() }}" class="">
      <h5 class="d-inline text-brown">
        <i class="fa-solid fa-angle-left text-orange"></i> Store Info
      </h5>
    </a>
  </div>

  @if ($store)
    <div class="row">
      {{-- store Info --}}
      <div class="col text-center mb-3">
        {{-- Store Photo --}}
        @if ($store->store_photo)
          <img src="{{ Storage::url($store->store_photo) }}" alt="store_photo" class="img-fluid rounded mb-3" style="max-width: 200px;">
        @else
          <i class="fa-solid fa-shop fa-5x text-muted mb-3"></i>
        @endif

        <h4 class="fw-bold">{{ $store->store_name ?? 'No Name' }}</h4>
        <p class="mb-1"><i class="fa-solid fa-location-dot text-muted"></i> {{ $store->address ?? '-' }}</p>
        <p class="mb-1"><i class="fa-solid fa-phone text-muted"></i> {{ $store->phone ?? '-' }}</p>
        <p class="mb-1"><i class="fa-solid fa-user-tie text-muted"></i> {{ $store->manager_name ?? '-' }}</p>
        <p class="mb-1"><i class="fa-solid fa-clock text-muted"></i> {{ $store->open_hours ?? '-' }}</p>
        <p class="mb-1"><i class="fa-solid fa-envelope text-muted"></i> {{ $store->user->email ?? Auth::user()->email }}</p>
      </div>

      {{-- Chat (まだ仮置き) --}}
      <div class="col">
        <div class="border rounded p-3 bg-light">
          <h5 class="fw-bold mb-3">Chat</h5>
          <p class="text-muted">ここにチャット機能を追加予定</p>
        </div>
      </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
      <a href="{{ route('manager.stores.edit') }}" class="btn btn-primary me-2">Edit</a>
      <a href="{{route('manager.stores.qrCode')}}" class="btn btn-primary">Create QR Code</a>
    </div>
  @else
    <div class="alert alert-warning">
      店舗情報がまだ登録されていません。<br>
      <a href="{{ route('manager.stores.edit') }}" class="btn btn-sm btn-primary mt-2">新規登録</a>
    </div>
  @endif
</div>
@endsection
