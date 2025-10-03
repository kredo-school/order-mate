@extends('layouts.app')

@section('title', 'Table Order History')

@section('content')
@php
$currencyCode = $store->currency ?? 'php'; // DBに保存されたコード、なければ php
$currencyLabel = config('currencies')[$currencyCode] ?? '₱ - PHP';
@endphp
<div class="container">
  <div class="d-flex justify-content-between mb-4">
    <a href="{{ route('manager.tables') }}" class="">
        <h5 class="d-inline text-brown">
            <i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.back_to_tables')}}
        </h5>
    </a>
    <h3 class="fw-bold">{{__('manager.table')}} {{ $table->number ?? '不明' }}</h3>
  </div>

  @if ($history->count() > 0)
    <table class="table table-hover border-0">
      <thead class="border-0">
        <tr class="border-0">
          <th class="border-0">{{__('manager.menu')}}</th>
          <th class="border-0">{{__('manager.options')}}</th>
          <th class="border-0">{{__('manager.price')}}</th>
          <th class="border-0">{{__('manager.qty')}}</th>
          <th class="border-0">{{__('manager.status')}}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($history as $row)
          <tr class="border-0">
            <td class="border-0">{{ $row['menu_name'] }}</td>
            <td class="border-0">{{ $row['options'] }}</td>
            <td class="border-0">{{ number_format($row['price'], 2) }} {{ explode(' - ', $currencyLabel)[0] }}</td>
            <td class="border-0">x{{ $row['quantity'] }}</td>
            <td class="border-0">{{ ucfirst($row['status']) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center py-2">
        <div>
            <span class="fw-bold">{{__('manager.total')}}: </span>
            <span class="h3 fw-bold">{{ number_format($totalPrice, 2) }}</span>
            <span class="fw-bold">{{ explode(' - ', $currencyLabel)[0] }}</span>
            @if($isPaid)
                <span class="text-success ms-2 fw-bolder">({{__('manager.paid_via')}}: {{ $paymentMethod ?? 'unknown' }})</span>
            @else
                <span class="text-danger ms-2 fw-bolder">({{__('manager.unpaid')}})</span>
            @endif
        </div>
    
        <div class="d-flex gap-3">
          @if (! $isPaid)
              <!-- Payment ボタン -->
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                  {{__('manager.payment')}}
              </button>
          @endif
                <form action="{{ route('manager.tables.checkout', $table->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">{{__('manager.checkout')}}</button>
                </form>
        </div>
        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form action="{{ route('manager.tables.pay', $table->id) }}" method="POST">
              @csrf
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="paymentModalLabel">{{__('manager.select_payment')}}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                    <label class="form-check-label" for="cash">{{__('manager.cash')}}</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="credit" value="credit card">
                    <label class="form-check-label" for="credit">{{__('manager.credit_card')}}</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="qr" value="qr code">
                    <label class="form-check-label" for="qr">{{__('manager.qr_code')}}</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="other" value="other">
                    <label class="form-check-label" for="other">{{__('manager.other')}}</label>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('manager.cancel')}}</button>
                  <button type="submit" class="btn btn-success">{{__('manager.confirm')}}</button>
                </div>
              </div>
            </form>
          </div>
        </div>

    </div>
  @else
    <p class="text-muted">{{__('manager.no_orders')}}</p>
  @endif
</div>
@endsection
