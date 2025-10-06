@extends('layouts.app')

@section('content')
<div class="container page-center d-flex justify-content-center align-items-center">
    <div class="row justify-content-center g-3 w-100" style="max-width: 600px;">
        {{-- テーブル --}}
        <div class="col-12 col-md-6">
            <a href="{{ route('manager.tables') }}" class="menu-card">
                {{ __('manager.tables') }}
            </a>
        </div>

        {{-- オーダーリスト --}}
        <div class="col-12 col-md-6">
            <a href="{{ route('manager.order-list') }}" class="menu-card">
                {{ __('manager.order_list') }}
            </a>
        </div>

        {{-- 商品管理 --}}
        <div class="col-12 col-md-6">
            <a href="{{ route('manager.products.index') }}" class="menu-card">
                {{ __('manager.admin_menu') }}
            </a>
        </div>

        {{-- 店舗情報 --}}
        <div class="col-12 col-md-6 position-relative">
            <a href="{{ route('manager.stores.index') }}" class="menu-card d-flex justify-content-center align-items-center">
                {{ __('manager.store_info') }}
                @if($store && $store->unread_messages_count > 0)
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                        {{ $store->unread_messages_count }}
                    </span>
                @endif
            </a>
        </div>
    </div>
</div>

{{-- スタイル --}}
<style>
    .menu-card {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100px;
        background-color: #ff7a00;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
        .menu-card {
            width: 100%;
        }
    }
</style>
@endsection
