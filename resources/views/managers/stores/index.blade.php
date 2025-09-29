@extends('layouts.app')

@section('title', 'Store Info')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('home') }}" class="">
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
                        <img src="{{ Storage::url($store->store_photo) }}" alt="store_photo" class="img-fluid rounded mb-3"
                            style="max-width: 200px;">
                    @else
                        <i class="fa-solid fa-shop fa-5x text-muted mb-5 mt-5"></i>
                    @endif

                    <h2 class="fw-bold mb-5 text-center text-brown">{{ $store->store_name ?? 'No Name' }}</h2>

                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fa-solid fa-2x fa-location-dot me-2 text-brown"></i>
                        <p class="mb-0 text-brown" style="font-size: 1.05rem;">{{ $store->address ?? '-' }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fa-solid fa-phone fa-2x me-2 text-brown"></i>
                        <p class="mb-0 text-brown" style="font-size: 1.05rem;">{{ $store->phone ?? '-' }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fa-solid fa-user-tie fa-2x me-2 text-brown"></i>
                        <p class="mb-0 text-brown" style="font-size: 1.05rem;">{{ $store->manager_name ?? '-' }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fa-solid fa-clock fa-2x me-2 text-brown"></i>
                        <p class="mb-0 text-brown" style="font-size: 1.05rem;">{{ $store->open_hours ?? '-' }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fa-solid fa-envelope fa-2x me-2 text-brown"></i>
                        <p class="mb-0 text-brown" style="font-size: 1.05rem;">
                            {{ $store->user->email ?? Auth::user()->email }}</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fa-solid fa-coins fa-2x me-2 text-brown"></i>
                        <p class="mb-0 text-brown" style="font-size: 1.05rem;">
                            {{-- 選択された通貨コードをラベルに変換して表示 --}}
                            {{ config('currencies')[$store->currency] ?? '' }}
                        </p>
                    </div>

                </div>

                {{-- Chat Column --}}
                @include('chats.chat', ['chat' => $chat, 'messages' => $messages])

                <div class="mt-4 d-flex justify-content-center">
                    <a href="{{ route('manager.stores.edit') }}" class="btn btn-outline me-2">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <a href="{{ route('manager.stores.qrCode') }}" class="btn btn-primary">
                        <i class="fa-solid fa-qrcode"></i> Create QR Code
                    </a>
                </div>
            @else
                <div class="alert alert-warning">
                    Store information has not been registered yet.<br>
                    <a href="{{ route('manager.stores.edit') }}" class="btn btn-sm btn-primary mt-2">Create account</a>
                </div>
        @endif
    </div>
@endsection
