@extends('layouts.app')
@section('title', 'Call Complete')
@section('content')
<div class="container my-5 text-center">
  <h2 class="text-brown mt-5">A server will come soon.</h2>
  <div class="mt-5">
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary btn-lg">Back to Top</a>
  </div>
</div>
@endsection