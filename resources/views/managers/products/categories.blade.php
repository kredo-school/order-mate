@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container">
  <div class="">
    <a href="{{url()->previous()}}" class="">
      <h3 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Category</h3>
    </a>
  </div>
</div>
@endsection