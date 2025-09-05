@extends('layouts.app')
@section('title', 'Menu Info')

@section('content')
<div class="container mt-4">
    <div class="row">
        {{-- 左側（商品画像） --}}
        <div class="col-md-4 d-flex justify-content-center align-items-start">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow" style="max-width: 100%; height: auto;">
            @else
                <div class="text-muted">No Image</div>
            @endif
        </div>

        <div class="col-md-2"></div>

        {{-- 右側（商品情報） --}}
        <div class="col-md-6">
            <h2 class="fw-bold text-center">{{ $product->name }}</h2>
            <p class="fs-5 text-center">{{ number_format($product->price) }}円</p>

            {{-- 説明 --}}
            @if (!empty($product->description))
                <p class="mt-3 text-center"> {{ $product->description }}</p>
            @endif

            {{-- アレルギー情報 --}}
            @if (!empty($product->allergy))
                <p> {{ $product->allergy }}</p>
            @endif

            {{-- タグ --}}
            @if ($product->tag)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $product->tag) }}" alt="tag" style="max-width:80px;">
                </div>
            @endif

            {{-- Add to Cart Form --}}
            <form action="{{ route('guest.cart.add', [
                    'storeName' => $store->store_name,
                    'tableUuid' => $table->uuid,
                    'menu'      => $product->id
                ]) }}" method="POST" id="add-to-cart-form">
                @csrf

                {{-- 商品数量 --}}
                <div class="mt-3 text-center">
                    <h5 class="fw-semibold">数量</h5>
                    <div class="d-flex justify-content-center align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm product-decrement">-</button>
                        <span id="product-quantity" class="mx-2">0</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm product-increment">+</button>
                    </div>
                </div>
                <input type="hidden" name="quantity" value="0" id="product-quantity-input">

                {{-- カスタムオプション --}}
                @if ($product->customGroups && $product->customGroups->count())
                    <div class="mt-4">
                        @foreach ($product->customGroups as $group)
                            <div class="mb-3">
                                <h5 class="mb-1 fw-semibold">{{ $group->title }}</h5>
                                <ul class="list-unstyled ms-3 custom-group">
                                    @foreach ($group->customOptions as $option)
                                        <li class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                {{ $option->name }}
                                                @if ($option->extra_price)
                                                    <span class="text-muted">
                                                        （{{ $option->extra_price > 0 ? '+' : ($option->extra_price < 0 ? '-' : '±') }}{{ number_format(abs($option->extra_price)) }}）
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-outline-secondary btn-sm decrement">-</button>
                                                <span class="mx-2 quantity" data-option-id="{{ $option->id }}">0</span>
                                                <button type="button" class="btn btn-outline-secondary btn-sm increment">+</button>
                                            </div>
                                        </li>
                                        <input type="hidden" name="options[{{ $option->id }}]" value="0" id="option-input-{{ $option->id }}">
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add ボタン --}}
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Add</button>
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

            const maxQty = parseInt(productQtyInput.value);
            const groupEl = btn.closest("ul.custom-group");
            const groupTotal = Array.from(groupEl.querySelectorAll(".quantity"))
                .reduce((sum, q) => sum + parseInt(q.textContent), 0);

            if (groupTotal < maxQty) {
                value++;
                quantityEl.textContent = value;
                document.getElementById("option-input-" + optionId).value = value;
            } else {
                alert("選択可能な最大数は " + maxQty + " です");
            }
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
