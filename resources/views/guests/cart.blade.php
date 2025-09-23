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
            <h3 class="fw-bold mb-4 text-brown">Cart</h3>

<<<<<<< HEAD
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
                <span class="text-muted">{{ number_format($item->price, 2) }} php</span>
                {{-- カスタムオプション --}}
                @if ($item->customOptions->count() > 0)
                  <ul class="mb-0 small text-muted">
                    @foreach ($item->customOptions as $custom)
                      <li>
                        {{ $custom->customOption->name }}
                        @if ($custom->extra_price != 0)
                          <span>（{{ $custom->extra_price > 0 ? '+' : '' }}{{ number_format($custom->extra_price, 2) }} php）</span>
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
=======
            @if ($orderItems->count() > 0)
                @foreach ($orderItems as $item)
                    <div class="d-flex align-items-center mb-3">
                        {{-- カード（クリックで編集ページへ） --}}
                        <div class="card border-0 hover-card flex-grow-1 p-0">
                            <a href="{{ route('guest.cart.edit', [
>>>>>>> 6bd537375fe2ef7c90b134b4582bda9f0684a4cf
                                'storeName' => $store->store_name,
                                'tableUuid' => $table->uuid,
                                'orderItem' => $item->id,
                            ]) }}"
                                class="stretched-link"></a>

                            <div class="card-body d-flex align-items-center">
                                {{-- 左：商品画像 --}}
                                <div style="width: 80px; height: 80px; flex-shrink: 0;">
                                    <img src="{{ asset('storage/' . $item->menu->image) }}" alt="product"
                                        class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 100%;">
                                </div>

                                {{-- 中央：商品名・価格 --}}
                                <div class="flex-grow-1 px-3 d-flex flex-column justify-content-center">
                                    <span class="fw-bold text-brown">{{ $item->menu->name }}</span>
                                    <span class="fw-light text-brown">{{ number_format($item->menu->price) }} php</span>
                                    {{-- カスタムオプション --}}
                                    @if ($item->customOptions->count() > 0)
                                        <ul class="mb-0 small text-muted text-brown">
                                            @foreach ($item->customOptions as $custom)
                                                <li>
                                                    {{ $custom->customOption->name }}
                                                    {{ $custom->quantity }}
                                                    @if ($custom->extra_price != 0)
                                                        <span
                                                            class="text-brown">{{ $custom->extra_price > 0 ? '+' : '' }}{{ number_format($custom->extra_price) }}
                                                            php</span>
                                                    @else
                                                        <span class="text-brown">±0 php</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                {{-- 数量 --}}
                                <span class="fw-bold fs-4 text-brown me-3">x {{ $item->quantity }}</span>
                            </div>
                        </div>

                        {{-- 削除ボタン（カードの外） --}}
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" data-bs-toggle="modal"
                            data-bs-target="#deleteModal-{{ $item->id }}">
                            <i class="fa-solid fa-trash fa-2x"></i>
                        </button>
                    </div>

                    {{-- 削除確認モーダル --}}
                    <div class="modal fade" id="deleteModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="background-color: #fdf6ec;">
                                <div class="modal-header">
                                    <h5 class="modal-title text-brown d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                        Confirm Delete
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}"
                                        class="img-fluid rounded mb-3" style="max-height: 150px;">
                                    <p class="text-brown">
                                        Are you sure you want to delete
                                        <strong>“{{ $item->menu->name }}”</strong> from your cart?
                                    </p>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn btn-secondary px-5"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <form
                                        action="{{ route('guest.cart.destroy', [
                                            'storeName' => $store->store_name,
                                            'tableUuid' => $table->uuid,
                                            'orderItem' => $item->id,
                                        ]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-5">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach



                {{-- 合計表示 --}}
                {{-- <div class="d-flex justify-content-between align-items-center mt-4">
          <div>
            <small class="text-muted">Total</small><br>
            <span class="fs-4 fw-bold">{{ number_format($totalPrice, 2) }} php</span>
          </div> --}}
                <div class="d-flex justify-content-end mb-5">

                    <form
                        action="{{ route('guest.cart.complete', [
                            'storeName' => $store->store_name,
                            'tableUuid' => $table->uuid,
                        ]) }}"
                        method="POST">
                        @csrf
                        <button type="submit" id="completeOrderBtn" class="btn btn-primary btn-m px-5 me-2">Complete
                            Order</button>
                    </form>
                </div>
            @else
                {{-- 空のとき --}}
                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 300px;">
                    <h2 class="text-brown mb-4">Your cart is empty.</h2>
                    <a href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}"
                        class="btn btn-outline btn-m">Back to Menu</a>
                </div>
            @endif
        </div>
<<<<<<< HEAD
      @else
        <p class="text-muted">Your cart is empty.</p>
        <a href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}"
           class="btn btn-outline-primary">Back to Menu</a>
      @endif
  </div>
</div>

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
    document.querySelectorAll(".clickable-card").forEach(card => {
      card.addEventListener("click", () => {
        window.location.href = card.dataset.href;
      });
    });

    document.querySelectorAll(".btn[data-bs-toggle='modal']").forEach(btn => {
      btn.addEventListener("click", e => e.stopPropagation());
    });
  });
</script>
@endpush

@endsection
=======

        {{-- hover時に浮き上がるエフェクト --}}
        <style>
            .hover-card {
                transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
            }

            .hover-card:hover {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
                transform: translateY(-2px);
            }
        </style>

        @push('scripts')
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    // --- Complete Order ポップオーバー ---
                    const completeBtn = document.getElementById('completeOrderBtn');
                    if (completeBtn && typeof bootstrap !== 'undefined') {
                        const completePopover = new bootstrap.Popover(completeBtn, {
                            trigger: 'manual',
                            placement: 'bottom',
                            title: 'Info',
                            content: 'Click this button to confirm your order'
                        });
                        completePopover.show();
                        completeBtn.addEventListener('click', () => {
                            try {
                                completePopover.dispose();
                            } catch (e) {}
                        });
                    }
                });
            </script>
        @endpush
    @endsection
>>>>>>> 6bd537375fe2ef7c90b134b4582bda9f0684a4cf
