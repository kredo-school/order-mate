@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between">
    <a href="{{url()->previous()}}" class="">
      <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
    </a>
    <input type="search" name="search_product" id="search_product">
    <a href="{{route('manager.create')}}" class="text-orange">
      <i class="fa-solid fa-plus"></i> Add
    </a>
  </div>

  {{-- category --}}

  {{-- products --}}
</div>
@endsection