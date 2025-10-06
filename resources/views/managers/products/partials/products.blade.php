@php
    $list = $products ?? ($menus ?? collect());
@endphp

@if (request()->has('search') && $list->isEmpty())
    <p class="text-gray">{{__('manager.no_results')}}</p>
@elseif ($list->isEmpty())
    <p class="text-gray">{{__('manager.no_products')}}</p>
@else
<div class="row">
    @foreach ($list as $product)
        <div class="col-6 col-md-3 col-lg-2">
            <a href="{{ $isGuestPage
                ? route('guest.show', [
                    'storeName' => $store->store_name,
                    'tableUuid' => $table->uuid,
                    'id' => $product->id,
                ])
                : route('manager.products.show', $product->id) }}"
                class="text-decoration-none text-brown d-block">

                <div class="card border-0 shadow-none position-relative h-100 p-1">
                    @if ($product->tag)
                        <img src="{{ asset('storage/' . $product->tag) }}" alt="tag" class="position-absolute"
                            style="top:5px; left:5px; width:50px; height:50px; object-fit:cover; z-index:10;">
                    @endif

                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                            alt="{{ $product->name }}" style="width: 100%; height: auto; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                            style="padding-top: 56.25%;"> {{-- 16:9 比例のスペース --}}
                            <span class="text-muted">{{__('manager.no_image')}}</span>
                        </div>
                    @endif

                    @php
                        $currencyCode = $store->currency ?? 'php';
                        $currencyLabel = config('currencies')[$currencyCode] ?? '₱ - PHP';
                    @endphp

                    <div class="card-body text-center p-2">
                        <h5 class="card-title mb-1 text-brown fw-bold mt-1">{{ $product->name }}</h5>
                        @if (isset($product->price))
                            <p class="mb-0 text-brown">{{ explode(' - ', $currencyLabel)[0] }} {{ number_format($product->price, 2) }}</p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

@endif
