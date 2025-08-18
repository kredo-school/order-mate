@extends('layouts.base')
@section('title', 'Admin Index')
@section('content')
    <h1 class="mb-4">店舗一覧</h1>

    <div class="container">
        <div class="row">
            @foreach ($all_stores as $store)
                <div class="col-md-6 mb-4">
                    <a href="{{ route('admin.show', $store->id) }}" class="text-decoration-none text-dark">
                        <div class="card shadow-sm h-100">
                            <div class="row g-0">
                                {{-- 左側 1/3 店舗写真 --}}
                                <div class="col-4">
                                    @if($store->store_photo ?? false)
                                        <img src="{{ Storage::url($store->store_photo) }}" alt="{{ $store->store_name }}" class="img-fluid h-100 w-100 object-fit-cover">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100">
                                            No Image
                                        </div>
                                    @endif
                                </div>
                                {{-- 右側 2/3 情報 --}}
                                <div class="col-8">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $store->store_name }}</h5>
                                        <p class="card-text mb-1"><i class="fa-solid fa-phone text-muted"></i> {{ $store->phone ?? '-' }}</p>
                                        <p class="card-text mb-1"><i class="fa-solid fa-envelope text-muted"></i> {{ $store->user->email ?? Auth::user()->email }}</p>
                                        <p class="card-text mb-1"><i class="fa-solid fa-location-dot text-muted"></i> {{ $store->address ?? '-' }}</p>
                                        <p class="card-text mb-1"><i class="fa-solid fa-user-tie text-muted"></i> {{ $store->manager_name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection