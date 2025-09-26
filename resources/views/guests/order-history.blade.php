@extends('layouts.app')
@section('title', 'Order History')

@section('content')

<!-- テスト用の強制上書きCSS（本番は app.css に移してください） -->
<style>
/* テーブル全体のラッパー（外枠用の色） */
.table-wrapper {
  background-color: #F9EAD3 !important;
  padding: 12px;
  border-radius: 8px;
}

/* table本体は透明に（ラッパーの色を見せる） */
.table-wrapper table {
  background-color: transparent !important;
  margin-bottom: 0 !important;
  border-collapse: separate;
  border-spacing: 0 4px; /* 行間に余白を作ると見やすい */
}

/* ヘッダー */
.table-wrapper thead th {
  background-color: transparent !important;
  border: none !important;
  color: #5C3D2E !important;
  font-weight: 600;
}

/* 偶数・奇数行の色分け */
.table-wrapper tbody tr:nth-child(odd) td {
  background-color: #FFF3E1 !important;
  color: #5C3D2E !important;
  border: none !important;
  vertical-align: middle;
  padding: 0.9rem;
}

.table-wrapper tbody tr:nth-child(even) td {
  background-color: #F9EAD3 !important;
  color: #5C3D2E !important;
  border: none !important;
  vertical-align: middle;
  padding: 0.9rem;
}

/* ホバーで濃い色に */
.table-wrapper tbody tr:hover td {
  background-color: #F5D9B8 !important;
}

/* text-brown 上書き（念のため） */
.text-brown {
  color: #5C3D2E !important;
}
</style>

<div class="container py-4">
   {{-- 戻るボタン --}}
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}">
                <h5 class="d-inline text-brown">
                    <i class="fa-solid fa-angle-left text-orange"></i> Menu List
                </h5>
            </a>
        </div>
  <h3 class="fw-bold mb-4 text-brown">Order History</h3>

  @if ($history->count() > 0)
    <div class="p-2 rounded table-wrapper">
      <table class="table border-0 mb-0">
        <thead>
          <tr>
            <th class="text-brown border-0">Menu</th>
            <th class="text-brown border-0">Options</th>
            <th class="text-brown border-0">Price</th>
            <th class="text-brown border-0">Qty</th>
            <th class="text-brown border-0">Status</th>
        </tr>
      </thead>
                  @php
                $currencyCode = $store->currency ?? 'php'; // DB にあるコード、なければ php
                $currencyLabel = config('currencies')[$currencyCode] ?? '₱ - PHP';
            @endphp

      <tbody>
        @foreach ($history as $row)
          <tr class="border-0">
            <td class="border-0">{{ $row['menu_name'] }}</td>
            <td class="border-0">{{ $row['options'] }}</td>
            <td class="border-0">{{ explode(' - ', $currencyLabel)[0] }}
                                {{ number_format($row['price'], 2) }}</td>
            <td class="border-0">x{{ $row['quantity'] }}</td>
            <td class="border-0">{{ ucfirst($row['status']) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <p class="text-muted text-brown">No order history.</p>
  @endif
</div>
@endsection
