@extends('layouts.app')
@section('content')
<div class="container text-center" style="margin-top: 150px; margin-bottom: 150px;">
    <h2 class="mb-4 text-brown mb-5">Menu Added Successfully!</h2>
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-outline btn-m me-3">Go to Menu List</a>

    <a href="{{ route('guest.cart.show', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary btn-m">Go to Cart</a>
</div>
@endsection