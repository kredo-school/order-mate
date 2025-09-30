@extends('layouts.app')
@section('content')
<div class="container d-flex align-items-center justify-content-center" >
    <div class="row w-100 justify-content-center">
        <div class="col-md-4 d-flex flex-column align-items-center">
            <a href="{{ route('manager.tables') }}" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex poppin" style="height: 100%">
                    {{ __('manager.tables') }}
                </div>
            </a>
            <a href="{{ route('manager.order-list') }}" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex poppin" style="height: 100%">
                    {{ __('manager.order_list') }}
                </div>
            </a>
            <a href="{{route('manager.products.index')}}" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex" style="height: 100%">
                    {{ __('manager.admin_menu') }}
                </div>
            </a>
            <a href="{{route('manager.stores.index')}}" class="card bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex" style="height: 100%">
                    {{ __('manager.store_info') }}
                    @if($store && $store->unread_messages_count > 0)
                        <span class="badge bg-danger">{{ $store->unread_messages_count }}</span>
                    @endif
                </div>
            </a>
        </div>
    </div>
</div>
@endsection