@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="container mt-5 text-center">
  <h2 class="text-brown mt-5">{{__('guest.checkout_message')}}</h2>
  <div class="mt-5">
    <a href="{{url()->previous()}}" class="btn btn-outline btn-lg">{{__('guest.back')}}</a>
    <form method="POST" action="{{ route('guest.checkout.complete', [$storeName, $table->uuid]) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-primary btn-lg">{{__('guest.checkout')}}</button>
    </form>
  </div>
</div>
@endsection