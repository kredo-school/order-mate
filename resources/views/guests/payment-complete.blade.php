@extends('layouts.app')
@section('title', 'Payment Complete')
@section('content')
<div class="container mt-5 text-center">
  <h2 class="text-brown mt-5">Your payment is completed!</h2>
  <div class="mt-5">
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary me-3">Back to Menu</a>
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary me-3">Checkout</a>
  </div>
</div>
@endsection