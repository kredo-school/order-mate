@extends('layouts.app')
@section('content')
    <div class="container text-center page-center">
        <h2 class="mb-5 text-brown">{{__('guest.order_success')}}</h2>
        <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}"
            class="btn btn-outline btn-m me-3">{{__('guest.back_to_menu')}}</a>

        <a href="{{ route('guest.orderHistory', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}"
            class="btn btn-primary btn-m">{{__('guest.view_orders')}}</a>
    </div>
@endsection
