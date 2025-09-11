@extends('layouts.app')
@section('title', 'Checkout Complete')
@section('content')
<div class="container mt-5 text-center">
  @if ($isPaid)
    <h2 class="text-brown mt-5">Thank you for coming!</h2>
  @else
    
  @endif
</div>
@endsection