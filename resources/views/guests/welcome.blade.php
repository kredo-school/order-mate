@extends('layouts.noheader')

@section('content')
<div class="container text-center py-5">
    <h2 class="fw-bold">Welcome to {{ $store->store_name }}!</h2>
    <p class="mt-3">Thank you for visiting us. Please enter the number of guests.</p>

    <form action="{{ route('guest.startOrder', [$store->store_name, $table->uuid]) }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <input type="number" name="guest_count" min="1" max="20" class="form-control w-50 mx-auto" placeholder="Number of guests" required>
        </div>
        <button type="submit" class="btn btn-primary">Start Order</button>
    </form>
</div>
@endsection
