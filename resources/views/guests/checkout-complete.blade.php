@extends('layouts.noheader')

@section('content')
    <div class="container text-center py-5 page-center">
        @php
            $currencyCode = $store->currency ?? 'php'; // DB にあるコード、なければ php
            $currencyLabel = config('currencies')[$currencyCode] ?? '₱ - PHP';
        @endphp
        <h2 class="fw-bold">{{ $message }}</h2>
        @if ($showTotal)
            <p class="mt-3">{{__('guest.total')}}: <span class="fw-bold">{{ explode(' - ', $currencyLabel)[0] }} {{ number_format($order->total_price, 2) }}</span></p>
        @endif
        <p class="text-muted mt-4">{{__('guest.session_ended')}}</p>
    </div>
@endsection
