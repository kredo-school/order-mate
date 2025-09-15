@extends('layouts.app')
@section('title', 'Order History')
@section('content')
<div class="container">
  <h3 class="fw-bold mb-4">Order History</h3>

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
  @else
    <p class="text-muted">No order history.</p>
  @endif
</div>
@endsection
