@extends('layouts.app')
@section('content')
<div class="container text-center" style="margin-top: 150px; margin-bottom: 150px;">
    <h2 class="mb-4">Menu Ordered Successfully!</h2>
    <a href="{{ route('guest.index', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-primary me-3">Go to Menu List</a>

    <a href="{{ route('guest.orderHistory', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}" class="btn btn-success">Go to Order History</a>
</div>
@endsection