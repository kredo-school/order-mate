@extends('layouts.app')
@section('title', 'Call Staff')
@section('content')
<div class="container mt-5 text-center page-center">
  <i class="fa-solid fa-bell fa-5x text-warning"></i>
  <h2 class="text-brown mt-5">{{__('guest.call_server')}}</h2>
  <div class="mt-5">
    <a href="{{url()->previous()}}" class="btn btn-outline btn-lg">{{__('guest.back')}}</a>
    <form method="POST" action="{{ route('guest.call.store', [$storeName, $table->uuid]) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-primary btn-lg">{{__('guest.call')}}</button>
    </form>
  </div>
</div>
@endsection