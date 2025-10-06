@extends('layouts.app')
@section('content')
<div class="container text-center page-center">
    <h2 class="mb-4 text-brown mb-5">{{ __('guest.add_success') }}</h2>
    <div>
        <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" 
           class="btn btn-outline btn-m me-3">{{ __('guest.back_to_menu') }}</a>

        <a href="{{ route('guest.cart.show', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" 
           class="btn btn-primary btn-m">{{ __('guest.view_cart') }}</a>
    </div>
</div>
@endsection