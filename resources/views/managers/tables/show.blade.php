@extends('layouts.app')

@section('title', 'Table Order History')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-4">
    <a href="{{ route('manager.tables') }}" class="">
        <h5 class="d-inline text-brown">
            <i class="fa-solid fa-angle-left text-orange"></i> Back to Tables
        </h5>
    </a>
    <h3 class="fw-bold">Table {{ $table->number }} - Order History</h3>
  </div>

  @if ($history->count() > 0)
    <table class="table table-hover border-0">
      <thead class="border-0">
        <tr class="border-0">
          <th class="border-0">Menu</th>
          <th class="border-0">Options</th>
          <th class="border-0">Price</th>
          <th class="border-0">Qty</th>
          <th class="border-0">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($history as $row)
          <tr class="border-0">
            <td class="border-0">{{ $row['menu_name'] }}</td>
            <td class="border-0">{{ $row['options'] }}</td>
            <td class="border-0">{{ number_format($row['price'], 2) }} php</td>
            <td class="border-0">x{{ $row['quantity'] }}</td>
            <td class="border-0">{{ ucfirst($row['status']) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center py-2">
        <div>
            <span class="fw-bold">Total: </span>
            <span class="h3 fw-bold">{{ number_format($totalPrice, 2) }}</span>
            @if($isPaid)
                <span class="text-success ms-2 fw-bolder">(paid via {{ $paymentMethod ?? 'unknown' }})</span>
            @else
                <span class="text-danger ms-2 fw-bolder">(unpaid)</span>
            @endif
        </div>
    
        <div class="d-flex gap-3">
          @if (! $isPaid)
              <!-- Payment ボタン -->
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                  Payment
              </button>
          @endif
                <form action="{{ route('manager.tables.checkout', $table->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </form>
        </div>
        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form action="{{ route('manager.tables.pay', $table->id) }}" method="POST">
              @csrf
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="paymentModalLabel">Select Payment Method</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                    <label class="form-check-label" for="cash">Cash</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="credit" value="credit card">
                    <label class="form-check-label" for="credit">Credit Card</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="qr" value="qr code">
                    <label class="form-check-label" for="qr">QR Code</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="other" value="other">
                    <label class="form-check-label" for="other">Other</label>
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-success">Confirm</button>
                </div>
              </div>
            </form>
          </div>
        </div>

    </div>
  @else
    <p class="text-muted">No order history.</p>
  @endif
</div>
@endsection
