@extends('layouts.app')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <a href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}">
      <h5 class="d-inline text-brown">
        <i class="fa-solid fa-angle-left text-orange"></i> Menu List
      </h5>
    </a>
  </div>

  {{-- カート一覧 --}}
  <div class="row mt-3">
    <h3 class="fw-bold mb-4">Cart</h3>
    <div class="col">

      @if ($orderItems->count() > 0)
        @foreach ($orderItems as $item)
          <div class="card border-0 hover-card p-0 clickable-card"
          data-href="{{ route('guest.cart.edit', [
              'storeName' => $store->store_name,
              'tableUuid' => $table->uuid,
              'orderItem' => $item->id
          ]) }}">
            <div class="card-body d-flex align-items-center">
              {{-- 左：商品画像 --}}
              <div style="width: 80px; height: 80px; flex-shrink: 0;">
                <img src="{{ asset('storage/' . $item->menu->image) }}" alt="product"
                      class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%;">
              </div>
          
              {{-- 中央：商品名・価格 --}}
              <div class="flex-grow-1 px-3 d-flex flex-column justify-content-center">
                <span class="fw-bold">{{ $item->menu->name }}</span>
                <span class="text-muted">{{ number_format($item->menu->price, 2) }} php</span>
                {{-- カスタムオプション --}}
                @if ($item->customOptions->count() > 0)
                  <ul class="mb-0 small text-muted">
                    @foreach ($item->customOptions as $custom)
                      <li>
                        {{ $custom->customOption->name }}
                        （{{ $custom->quantity }}）
                        @if ($custom->extra_price != 0)
                          <span>（{{ $custom->extra_price > 0 ? '+' : '' }}{{ number_format($custom->extra_price, 2) }} php）</span>
                        @else
                          <span>（±0 php）</span>
                        @endif
                      </li>
                    @endforeach
                  </ul>
                @endif
              </div>
          
              {{-- 右：数量・削除 --}}
              <div class="d-flex flex-column align-items-end justify-content-between" style="height: 80px;">
                <span class="fw-bold">x{{ $item->quantity }}</span>
                <button type="button" class="btn btn-sm btn-outline-danger p-1"
                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $item->id }}">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </div>
            {{-- 削除確認モーダル --}}
            <div class="modal fade" id="deleteModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <p>「{{ $item->menu->name }}」をカートから削除しますか？</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('guest.cart.destroy', [
                                'storeName' => $store->store_name,
                                'tableUuid' => $table->uuid,
                                'orderItem' => $item->id
                            ]) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
          </div>
        </div>
        @endforeach

        {{-- 合計表示 --}}
        <div class="d-flex justify-content-between align-items-center mt-4">
          <div>
            <small class="text-muted">Total</small><br>
            <span class="fs-4 fw-bold">{{ number_format($totalPrice, 2) }} php</span>
          </div>
          <form action="{{ route('guest.cart.complete', [
                      'storeName' => $store->store_name,
                      'tableUuid' => $table->uuid
                  ]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Complete Order</button>
          </form>
        </div>
      @else
        {{-- 空のとき --}}
        <p class="text-muted">Your cart is empty.</p>
        <a href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}"
           class="btn btn-outline-primary">Back to Menu</a>
      @endif
  </div>
</div>

{{-- hover時に浮き上がるエフェクト --}}
<style>
  .hover-card {
    transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
  }
  .hover-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    transform: translateY(-2px);
  }
</style>

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // カードをクリックしたらリンク先に飛ぶ
    document.querySelectorAll(".clickable-card").forEach(card => {
      card.addEventListener("click", () => {
        window.location.href = card.dataset.href;
      });
    });

    // 削除ボタンはカードクリックを止める
    document.querySelectorAll(".btn[data-bs-toggle='modal']").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation();
      });
    });
  });
</script>
@endpush

@endsection
