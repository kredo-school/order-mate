@extends('layouts.base')
@section('title', 'Admin Store')
@section('content')
    <h1>{{ $store->store_name }}</h1>

    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-4">
                @if($store->store_photo ?? false)
                    <img src="{{ Storage::url($store->store_photo) }}" class="img-fluid rounded-start" alt="{{ $store->store_name }}">
                @else
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100">
                        No Image
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">{{ $store->store_name }}</h5>
                    <p class="card-text"><i class="fa-solid fa-phone text-muted"></i> {{ $store->phone ?? '-' }}</p>
                    <p class="card-text"><i class="fa-solid fa-envelope text-muted"></i> {{ $store->user->email ?? Auth::user()->email }}</p>
                    <p class="card-text"><i class="fa-solid fa-location-dot text-muted"></i> {{ $store->address ?? '-' }}</p>
                    <p class="card-text"><i class="fa-solid fa-user-tie text-muted"></i> {{ $store->manager_name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <a href="" class="btn btn-secondary">戻る</a>
@endsection