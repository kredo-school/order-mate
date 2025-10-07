@extends('layouts.app')

@section('title', 'Store Info')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('manager.home') }}" class="">
                <h5 class="d-inline text-brown">
                    <i class="fa-solid fa-angle-left text-orange"></i> {{ __('manager.store_info') }}
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

                    <div class="d-flex justify-content-center">
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
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa-solid fa-envelope fa-2x me-2"></i>
                                <span>{{ $store->user->email ?? Auth::user()->email }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-coins fa-2x me-2"></i>
                                <span>{{ config('currencies')[$store->currency] ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chat Column --}}
                @include('chats.chat', ['chat' => $chat, 'messages' => $messages])

                <div class="mt-4 d-flex justify-content-center">
                    <a href="{{ route('manager.stores.edit') }}" class="btn btn-outline me-2">
                        <i class="fa-solid fa-pen-to-square"></i> {{ __('manager.edit') }}
                    </a>
                    <a href="{{ route('manager.stores.qrCode') }}" class="btn btn-primary">
                        <i class="fa-solid fa-qrcode"></i> {{ __('manager.create_qr_code') }}
                    </a>
                </div>
            @else
                <div class="alert alert-warning">
                    {{__('manager.not_store_info_yet')}}<br>
                    <a href="{{ route('manager.stores.edit') }}" class="btn btn-sm btn-primary mt-2">{{__('manager.create_store_info')}}</a>
                </div>
        @endif
    </div>
@endsection
