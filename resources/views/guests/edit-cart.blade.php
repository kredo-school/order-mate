@extends('layouts.app')
@section('title', 'Edit Cart')

@section('content')
    <div class="container mt-4">
        {{-- 戻るボタン --}}
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('guest.cart.show', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}">
                <h5 class="d-inline text-brown">
                    <i class="fa-solid fa-angle-left text-orange"></i> {{__('guest.edit_cart')}}
                </h5>
            </a>
        </div>
        <div class="row gx-0">
            {{-- 左側（商品画像） --}}
            <div class="col-md-5 d-flex justify-content-center align-items-start position-relative mt-2">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                        class="img-fluid rounded shadow" style="max-width: 250px; height: auto;">

                    {{-- タグ画像 --}}
                    @if ($product->tag)
                        <img src="{{ asset('storage/' . $product->tag) }}" alt="tag" class="position-absolute"
                            style="top: -5%; left: 15%; max-width: 60px; transform: translate(0, 0); border-radius:5px;">
                    @endif
                @else
                    <div class="text-muted" style="width:250px; height:auto;">{{__('guest.no_image')}}</div>
                @endif
            </div>

            @php
                $currencyCode = $store->currency ?? 'php'; // DB にあるコード、なければ php
                $currencyLabel = config('currencies')[$currencyCode] ?? '₱ - PHP';
            @endphp
            
            {{-- 右側 --}}
            <div class="col-md-7 mt-3">
                <h2 class="fw-bold text-center text-brown fs-1 mb-1">{{ $product->name }}</h2>
                <p class="fs-5 text-center text-brown">{{ explode(' - ', $currencyLabel)[0] }} {{ number_format($product->price) }}</p>


                {{-- Edit Form --}}
                <form
                    action="{{ route('guest.cart.update', [
                        'storeName' => $store->store_name,
                        'tableUuid' => $table->uuid,
                        'orderItem' => $orderItem->id,
                    ]) }}"
                    method="POST" id="edit-cart-form">
                    @csrf
                    @method('PATCH')

                    {{-- 商品数量 --}}
                    <div class="mt-3 text-center">
                        <h5 class="fw-semibold text-brown">{{__('guest.quantity')}}</h5>
                        <div class="d-flex justify-content-center align-items-center">
                            <button type="button"
                                class="btn btn-outline-secondary btn-m product-decrement text-brown">-</button>
                            <span id="product-quantity" class="mx-2 text-brown fs-3">{{ $orderItem->quantity }}</span>
                            <button type="button"
                                class="btn btn-outline-secondary btn-m product-increment text-brown">+</button>
                        </div>
                    </div>
                    <input type="hidden" name="quantity" value="{{ $orderItem->quantity }}" id="product-quantity-input">

                    {{-- カスタムオプション --}}
                    @if ($product->customGroups && $product->customGroups->count())
                        <div class="mt-4">
                            @foreach ($product->customGroups as $group)
                                <div class="mb-3">
                                    <h5 class="mb-1 fw-semibold fs-3 ms-5 text-brown">{{ $group->title }}</h5>
                                    <ul class="list-unstyled ms-3 custom-group text-brown">
                                        @foreach ($group->customOptions as $option)
                                            @php
                                                $currentQty = $selectedOptions[$option->id] ?? 0;
                                            @endphp
                                            <li class="d-flex align-items-center justify-content-between mb-2 option-row ms-5">
                                                <div>
                                                    {{ $option->name }}
                                                    <span class="text-brown fw-light fs-5">
                                                        （{{ $option->extra_price > 0 ? '+' : ($option->extra_price < 0 ? '-' : '±') }}{{ number_format(abs($option->extra_price)) }}）
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center me-5">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-sm decrement">-</button>
                                                    <span class="mx-2 quantity" data-option-id="{{ $option->id }}">{{ $currentQty }}</span>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-sm increment">+</button>
                                                </div>
                                            </li>
                                            <input type="hidden" name="options[{{ $option->id }}]"
                                                value="{{ $currentQty }}" id="option-input-{{ $option->id }}">
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- ボタン --}}
                    <div class="mt-5 text-center">
                        <a href="{{ route('guest.cart.show', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}"
                            class="btn btn-outline btn-lg me-1 px-5">{{__('guest.back')}}</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">{{__('guest.update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const minusBtn = document.querySelector('.product-decrement');
                const plusBtn = document.querySelector('.product-increment');
                const quantityDisplay = document.getElementById('product-quantity');
                const quantityInput = document.getElementById('product-quantity-input');
                const form = document.getElementById('edit-cart-form');

                // 商品数量の増減
                plusBtn.addEventListener('click', () => {
                    let qty = parseInt(quantityDisplay.textContent);
                    qty++;
                    quantityDisplay.textContent = qty;
                    quantityInput.value = qty;
                    updateCustomOptionQuantities(qty);
                });

                minusBtn.addEventListener('click', () => {
                    let qty = parseInt(quantityDisplay.textContent);
                    if (qty > 1) {
                        qty--;
                        quantityDisplay.textContent = qty;
                        quantityInput.value = qty;
                        updateCustomOptionQuantities(qty);
                    }
                });

                // オプションの増減ボタン制御
                document.querySelectorAll('.custom-group').forEach(group => {
                    const incrementBtns = group.querySelectorAll('.increment');
                    const decrementBtns = group.querySelectorAll('.decrement');

                    incrementBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const span = btn.parentElement.querySelector('.quantity');
                            const input = document.getElementById('option-input-' + span.dataset.optionId);
                            const productQty = parseInt(quantityInput.value);

                            const total = getGroupTotal(group);
                            let current = parseInt(span.textContent);

                            if (total < productQty) {
                                current++;
                                span.textContent = current;
                                input.value = current;
                            } else {
                                alert('{{__('guest.custom_alert')}}');
                            }
                        });
                    });

                    decrementBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const span = btn.parentElement.querySelector('.quantity');
                            const input = document.getElementById('option-input-' + span.dataset.optionId);
                            let current = parseInt(span.textContent);

                            if (current > 0) {
                                current--;
                                span.textContent = current;
                                input.value = current;
                            }
                        });
                    });
                });

                // オプショングループ内の合計を取得
                function getGroupTotal(group) {
                    return Array.from(group.querySelectorAll('.quantity')).reduce(
                        (sum, el) => sum + parseInt(el.textContent),
                        0
                    );
                }

                // 商品数量を減らしたとき、オプションを自動調整
                function updateCustomOptionQuantities(productQty) {
                    document.querySelectorAll('.custom-group').forEach(group => {
                        let total = getGroupTotal(group);
                        if (total > productQty) {
                            let excess = total - productQty;
                            group.querySelectorAll('.quantity').forEach(span => {
                                const input = document.getElementById('option-input-' + span.dataset.optionId);
                                let value = parseInt(span.textContent);
                                if (excess > 0 && value > 0) {
                                    const reduce = Math.min(value, excess);
                                    span.textContent = value - reduce;
                                    input.value = value - reduce;
                                    excess -= reduce;
                                }
                            });
                        }
                    });
                }

                // バリデーション（送信前チェック）
                form.addEventListener('submit', e => {
                    const productQty = parseInt(quantityInput.value);
                    if (productQty <= 0) {
                        alert('{{__('guest.add_to_cart_alert')}}');
                        e.preventDefault();
                        return;
                    }

                    let valid = true;
                    document.querySelectorAll('.custom-group').forEach(group => {
                        const total = getGroupTotal(group);
                        if (total > productQty) {
                            alert('{{__('guest.custom_alert')}}');
                            valid = false;
                        }
                    });

                    if (!valid) e.preventDefault();
                });
            });
        </script>
    @endpush
@endsection
