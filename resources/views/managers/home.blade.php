@extends('layouts.app')
@section('content')
<div class="container min-vh-100 d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-4 d-flex flex-column align-items-center">
            <a href="#" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex poppin" style="height: 100%">
                    Order List
                </div>
            </a>
            <a href="{{route('manager.products.index')}}" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex" style="height: 100%">
                    Admin Menu
                </div>
            </a>
            <a href="{{route('manager.stores.index')}}" class="card bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex" style="height: 100%">
                    Store Info
                    @if($store && $store->unread_messages_count > 0)
                        <span class="badge bg-danger">{{ $store->unread_messages_count }}</span>
                    @endif
                </div>
            </a>
        </div>
    </div>
</div>
@endsection