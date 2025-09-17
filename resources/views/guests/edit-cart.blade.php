@extends('layouts.app')
@section('title', 'Edit Cart')

@section('content')
    <div class="container mt-4">
        <div class="row">
            {{-- 左側（商品画像） --}}
            <div class="col-md-5 d-flex justify-content-end align-items-center">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                        class="img-fluid rounded shadow"
                        style="width: 90%; max-width: 500px; height: 500px; object-fit: cover;">
                @else
                    <div class="text-muted">No Image</div>
                @endif
            </div>


            {{-- 右側 --}}
            <div class="col-md-7 mt-4">
                <h2 class="fw-bold text-center text-brown">{{ $product->name }}</h2>
                <p class="fs-5 text-center text-brown">{{ number_format($product->price) }}php</p>

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
                        <h5 class="fw-semibold text-brown mt-5 mb-3">Quantity</h5>
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
                        <div class="mt-4 text-brown" style="margin: 0 90px; padding: 0 80px;">
                            @foreach ($product->customGroups as $group)
                                <div class="mb-3">
                                    <h5 class="mb-1 fw-semibold fs-4 mb-2">{{ $group->title }}</h5>
                                    <ul class="list-unstyled ms-3 custom-group fs-5">
                                        @foreach ($group->customOptions as $option)
                                            @php
                                                $currentQty = $selectedOptions[$option->id] ?? 0;
                                            @endphp
                                            <li class="d-flex align-items-center justify-content-between mb-2">
                                                <div>
                                                    {{ $option->name }}
                                                    <span class="text-brown">
                                                        （{{ $option->extra_price > 0 ? '+' : ($option->extra_price < 0 ? '-' : '±') }}{{ number_format(abs($option->extra_price)) }}）
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-sm decrement">-</button>
                                                    <span class="mx-2 quantity" data-option-id="{{ $option->id }}"
                                                        style="min-width: 15px;">{{ $currentQty }}</span>
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
                            class="btn btn-outline btn-lg me-1 px-5">Back</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // 商品数量
                const productQtyEl = document.getElementById("product-quantity");
                const productQtyInput = document.getElementById("product-quantity-input");

                document.querySelector(".product-increment").addEventListener("click", () => {
                    let value = parseInt(productQtyEl.textContent);
                    value++;
                    productQtyEl.textContent = value;
                    productQtyInput.value = value;
                });

                document.querySelector(".product-decrement").addEventListener("click", () => {
                    let value = parseInt(productQtyEl.textContent);
                    if (value > 0) value--;
                    productQtyEl.textContent = value;
                    productQtyInput.value = value;
                });

                // カスタムオプション数量
                document.querySelectorAll(".increment").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const quantityEl = btn.parentElement.querySelector(".quantity");
                        const optionId = quantityEl.dataset.optionId;
                        let value = parseInt(quantityEl.textContent);
                        value++;
                        quantityEl.textContent = value;
                        document.getElementById("option-input-" + optionId).value = value;
                    });
                });

                document.querySelectorAll(".decrement").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const quantityEl = btn.parentElement.querySelector(".quantity");
                        const optionId = quantityEl.dataset.optionId;
                        let value = parseInt(quantityEl.textContent);
                        if (value > 0) {
                            value--;
                            quantityEl.textContent = value;
                            document.getElementById("option-input-" + optionId).value = value;
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
