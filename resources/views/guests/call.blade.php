@extends('layouts.app')
@section('title', 'Call Staff')
@section('content')
<div class="container mt-5 text-center">
  <i class="fa-solid fa-bell fa-5x text-warning"></i>
  <h2 class="text-brown mt-5">Would you like to call a server?</h2>
  <div class="mt-5">
    <a href="{{url()->previous()}}" class="btn btn-outline btn-lg">No</a>
    <a href="" class="btn btn-primary btn-lg">Yes</a>
  </div>
</div>
@endsection