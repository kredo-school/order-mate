@extends('layouts.app')
@section('title', 'Payment Complete')
@section('content')
<div class="container mt-5 text-center">
  <h2 class="text-brown mt-5">{{__('guest.payment_success')}}</h2>
  <div class="mt-5">
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary me-3">{{__('guest.back_to_menu')}}</a>
    <form method="POST" action="{{ route('guest.checkout.complete', [$storeName, $table->uuid]) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-primary">{{__('guest.checkout')}}</button>
    </form>
  </div>
</div>
@endsection