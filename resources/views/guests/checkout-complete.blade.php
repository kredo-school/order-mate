@extends('layouts.noheader')

@section('content')
<div class="container text-center py-5">
    <h2 class="fw-bold">{{ $message }}</h2>
    @if ($showTotal)
        <p class="mt-3">Total: <span class="fw-bold">{{ number_format($order->total_price, 2) }} php</span></p>
    @endif
    <p class="text-muted mt-4">This session has ended. Please ask our staff if you need assistance.</p>
</div>
@endsection
