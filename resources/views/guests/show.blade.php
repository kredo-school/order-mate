@extends('layouts.app')
@section('title', 'Menu Info')

@section('content')
    <div class="container">
        {{-- 戻るボタン --}}
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}">
                <h5 class="d-inline text-brown">
                    <i class="fa-solid fa-angle-left text-orange"></i> {{__('guest.menu_list')}}
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

            {{-- 右側（商品情報） --}}
            <div class="col-md-7">
                <h2 class="fw-bold text-center text-brown fs-1 mt-3 mb-1">{{ $product->name }}</h2>
                <p class="fs-5 text-center text-brown">{{ explode(' - ', $currencyLabel)[0] }}
                    {{ number_format($product->price) }}</p>

                {{-- 説明 --}}
                @if (!empty($product->description))
                    <p class="mt-3 text-center text-brown"> {{ $product->description }}</p>
                @endif

                {{-- アレルギー情報 --}}
                @php
                    // DB に保存されている可能性のあるフォーマットを正規化して配列にする
                    $allergenKeys = [];
                
                    if (!empty($product->allergy)) {
                        if (is_array($product->allergy)) {
                            $allergenKeys = $product->allergy;
                        } elseif (is_string($product->allergy)) {
                            $decoded = json_decode($product->allergy, true);
                            if (is_array($decoded)) {
                                $allergenKeys = $decoded;
                            } else {
                                // カンマ区切りの旧フォーマットへフォールバック
                                $allergenKeys = array_filter(array_map('trim', explode(',', $product->allergy)));
                            }
                        }
                    } elseif (!empty($product->allergies) && method_exists($product->allergies, 'pluck')) {
                        // 万一リレーションで保存している場合（将来の移行に備え）
                        $allergenKeys = $product->allergies->pluck('key')->filter()->map(fn($v) => (string)$v)->all();
                    }
                
                    // 型の正規化（文字列配列）
                    $allergenKeys = array_map('strval', (array)$allergenKeys);
                @endphp
                
                <div class="mb-3 d-flex justify-content-center gap-2 flex-wrap">
                    @if (!empty($allergenKeys))
                        @foreach ($allergenKeys as $key)
                            <div class="d-flex align-items-center">
                                {{-- アイコン partial があれば表示（ファイルが無ければ無視） --}}
                                @includeIf("icons.allergens.{$key}")
                                {{-- 翻訳ラベルがあれば使う。resources/lang/{lang}/guest.php に allergen_labels を用意しても良い --}}
                                <span class="ms-2 text-brown">{{ __('guest.allergen_labels.' . $key) !== 'guest.allergen_labels.' . $key ? __('guest.allergen_labels.' . $key) : ucfirst($key) }}</span>
                            </div>
                        @endforeach
                    @else
                        <span class="text-brown">{{ __('guest.no_allergens') ?? __('manager.no_allergens') }}</span>
                    @endif
                </div>

                {{-- Add to Cart Form --}}
                <form
                    action="{{ route('guest.cart.add', [
                        'storeName' => $store->store_name,
                        'tableUuid' => $table->uuid,
                        'menu' => $product->id,
                    ]) }}"
                    method="POST" id="add-to-cart-form">
                    @csrf

                    {{-- 商品数量 --}}
                    <div class="mt-3 text-center">
                        <h5 class="fw-semibold text-brown">{{__('guest.quantity')}}</h5>
                        <div class="d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-outline-secondary btn-m product-decrement text-brown">-</button>
                            <span id="product-quantity" class="mx-2 text-brown fs-3">0</span>
                            <button type="button" class="btn btn-outline-secondary btn-m product-increment text-brown">+</button>
                        </div>
                    </div>
                    <input type="hidden" name="quantity" value="0" id="product-quantity-input">

                    {{-- カスタムオプション --}}
                    @if ($product->customGroups && $product->customGroups->count())
                        <div class="mt-4">
                            @foreach ($product->customGroups as $group)
                                <div class="mb-3">
                                    <h5 class="mb-1 fw-semibold fs-3 text-brown ms-5">{{ $group->title }}</h5>
                                    <ul class="list-unstyled ms-3 custom-group text-brown">
                                        @foreach ($group->customOptions as $option)
                                            <li class="d-flex align-items-center justify-content-between mb-2 option-row ms-5"
                                                style="font-size: 1.4rem;">
                                                <div>
                                                    {{ $option->name }}
                                                    @if ($option->extra_price)
                                                        <span class="fw-light text-brown fs-5">
                                                            {{ $option->extra_price > 0 ? '+' : ($option->extra_price < 0 ? '-' : '±') }}{{ number_format(abs($option->extra_price)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center me-5">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-s decrement">-</button>
                                                    <span class="mx-2 quantity"
                                                        data-option-id="{{ $option->id }}">0</span>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-s increment">+</button>
                                                </div>
                                            </li>
                                            <input type="hidden" name="options[{{ $option->id }}]" value="0"
                                                id="option-input-{{ $option->id }}">
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Add ボタン --}}
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary btn-lg" id="add-to-cart-btn">{{__('guest.add_to_cart')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('guest-scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // 必要なDOM要素を取得
                const productQtyEl = document.getElementById("product-quantity");
                const productQtyInput = document.getElementById("product-quantity-input");
                const customGroupElements = document.querySelectorAll(".custom-group");
                const addToCartBtn = document.getElementById("add-to-cart-btn");
                const addToCartForm = document.getElementById("add-to-cart-form");

                // --- 商品数量ボタンのロジック ---
                document.querySelector(".product-increment").addEventListener("click", () => {
                    let value = parseInt(productQtyEl.textContent);
                    value++;
                    productQtyEl.textContent = value;
                    productQtyInput.value = value;
                });

                document.querySelector(".product-decrement").addEventListener("click", () => {
                    let value = parseInt(productQtyEl.textContent);
                    if (value > 0) {
                        value--;
                        productQtyEl.textContent = value;
                        productQtyInput.value = value;
                        updateCustomOptionQuantities(value);
                    }
                });

                // --- カスタムオプションの増減ボタンのイベントリスナー ---
                document.querySelectorAll(".increment").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const quantityEl = btn.parentElement.querySelector(".quantity");
                        let value = parseInt(quantityEl.textContent);

                        const maxQty = parseInt(productQtyInput.value);
                        const groupEl = btn.closest("ul.custom-group");
                        const groupTotal = Array.from(groupEl.querySelectorAll(".quantity")).reduce((
                            sum, q) => sum + parseInt(q.textContent), 0);

                        if (maxQty === 0) {
                            alert("{{__('guest.add_to_cart_alert')}}");
                            return;
                        }

                        if (groupTotal < maxQty) {
                            value++;
                            quantityEl.textContent = value;
                            document.getElementById("option-input-" + quantityEl.dataset.optionId)
                                .value = value;
                        } else {
                            alert("{{__('guest.custom_alert')}}");
                        }
                    });
                });

                document.querySelectorAll(".decrement").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const quantityEl = btn.parentElement.querySelector(".quantity");
                        let value = parseInt(quantityEl.textContent);
                        if (value > 0) {
                            value--;
                            quantityEl.textContent = value;
                            document.getElementById("option-input-" + quantityEl.dataset.optionId)
                                .value = value;
                        }
                    });
                });

                // 商品数量が減ったときにカスタムオプションを調整する関数
                function updateCustomOptionQuantities(newProductQty) {
                    customGroupElements.forEach(groupEl => {
                        Array.from(groupEl.querySelectorAll(".quantity")).forEach(quantityEl => {
                            let optionValue = parseInt(quantityEl.textContent);
                            if (optionValue > newProductQty) {
                                quantityEl.textContent = newProductQty;
                                document.getElementById("option-input-" + quantityEl.dataset.optionId)
                                    .value = newProductQty;
                            }
                        });
                    });
                }

                // --- 「Add」ボタンのイベントリスナー ---
                addToCartBtn.addEventListener("click", (e) => {
                    e.preventDefault();

                    const qty = parseInt(productQtyInput.value);
                    if (qty < 1) {
                        alert("{{__('guest.add_to_cart_alert')}}");
                        return;
                    }

                    const formData = new FormData(addToCartForm);
                    const formAction = addToCartForm.action;
                    const options = {};
                    formData.forEach((value, key) => {
                        if (key.startsWith('options[')) {
                            options[key] = parseInt(value);
                        }
                    });

                    const dataToSend = {
                        quantity: qty,
                        options: options,
                        _token: document.querySelector('meta[name="csrf-token"]').content
                    };

                    fetch(formAction, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: new FormData(addToCartForm)
                        })
                        .then(response => response.text())
                        .then(() => {
                            // Just redirect after success
                            window.location.href =
                                "{{ route('guest.cart.addComplete', ['storeName' => $storeName, 'tableUuid' => $tableUuid]) }}";
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("{{__('guest.failed_add_alert')}}");
                        });
                });
            });
        </script>
    @endpush
@endsection
