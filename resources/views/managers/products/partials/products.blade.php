@php
    $list = $products ?? ($menus ?? collect());
@endphp

@if (request()->has('search') && $list->isEmpty())
    <p class="text-gray">No results found</p>
@elseif ($list->isEmpty())
    <p class="text-gray">No products in this category.</p>
@else
    <div class="row">
        @foreach ($list as $product)
            <div class="col-md-3 mb-4">
                <a href="{{ $isGuestPage 
                    ? route('guest.show', [
                        'storeName' => $store->store_name,
                        'tableUuid' => $table->uuid,
                        'id' => $product->id
                    ]) 
                    : route('manager.products.show', $product->id) }}" 
                    class="text-decoration-none text-brown">
                    
                    <div class="card h-100 border-0 shadow-none position-relative">
                        @if ($product->tag)
                            <img src="{{ asset('storage/' . $product->tag) }}" alt="tag" class="position-absolute"
                                style="top:5px; left:5px; width:50px; height:50px; object-fit:cover; z-index:10;">
                        @endif

                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                style="height: 180px;">
                                <span class="text-muted">No Image</span>
                            </div>
                        @endif

                        <div class="card-body text-center p-2">
                            <h5 class="card-title mb-1 text-brown fw-bold mt-1">{{ $product->name }}</h5>
                            @if (isset($product->price))
                                <p class="mb-0 text-brown">{{ number_format($product->price) }}php</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif
