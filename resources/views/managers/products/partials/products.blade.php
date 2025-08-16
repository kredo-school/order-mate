@php
    // $products が無ければ空コレクションにして isEmpty() を安全に呼べるようにする
    $list = $products ?? $menus ?? collect();
@endphp

@if ($list->isEmpty())
    <p class="text-gray">No products in this category.</p>
@else
    <div class="row">
        @foreach ($list as $product)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ $product->name }}</h5>
                        <p>{{ isset($product->price) ? number_format($product->price) . '円' : '' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
