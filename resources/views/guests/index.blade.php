@extends('layouts.app')
@section('title', 'Guest Home')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between">
            <h5 class="d-inline text-brown">{{__('guest.menu_list')}}</h5>
            <form action="{{ route('guest.index', ['storeName' => $store->store_name, 'tableUuid' => $table->uuid]) }}"
                method="GET" class="product-search d-flex align-items-center mx-3" role="search">
                <input type="search" name="search" class="input-underline" placeholder="{{__('guest.search_products')}}"
                    value="{{ request('search') }}" aria-label="Search products">
                <button type="submit" class="btn-icon text-orange ms-2" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>


        {{-- category --}}
        <div class="position-relative mt-5">
            <div class="d-flex align-items-center">
                <div class="category-scroll d-flex">
                    @foreach ($all_categories as $category)
                        <a href="#" class="category-link" data-id="{{ $category->id }}">
                            <div class="p-0">
                                <span class="category-tab {{ !request()->has('search') && $loop->first ? 'active' : '' }}">
                                    {{ $category->name }}
                                </span>
                            </div>
                        </a>
                    @endforeach

                    {{-- 検索結果タブ（検索時のみ表示） --}}
                    @if (request()->filled('search'))
                        <a href="#" class="category-link search-results-tab" data-id="search">
                            <div class="p-0">
                                <span class="category-tab active">{{__('guest.search_results')}} ({{$products->count()}})</span>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- products --}}
        <div id="products-container" class="mt-4">
            @include('managers.products.partials.products', ['products' => $products ?? collect()])
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.category-link');
            const container = document.getElementById('products-container');

            // Bladeから渡された変数
            const isGuestPage = @json($isGuestPage);
            const storeName = @json($store->store_name ?? '');
            const tableUuid = @json($table->uuid ?? '');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    const categoryId = this.dataset.id;
                    if (!categoryId) return;

                    e.preventDefault();

                    // 検索フォームをリセット
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.value = '';
                    }

                    // Search Resultsタブを非表示にする
                    const searchTab = document.querySelector('.search-results-tab');
                    if (searchTab && categoryId !== 'search') {
                        searchTab.remove();
                    }

                    // activeクラス切り替え
                    document.querySelectorAll('.category-tab').forEach(el => el.classList.remove(
                        'active'));
                    this.querySelector('.category-tab').classList.add('active');

                    // "Search Results" はAjaxリクエストしない
                    if (categoryId !== 'search') {
                        const url = isGuestPage ?
                            `/guest/${storeName}/${tableUuid}/products/${categoryId}` :
                            `/manager/products/by-category/${categoryId}`;

                        fetch(url)
                            .then(res => res.text())
                            .then(html => {
                                container.innerHTML = html;
                            })
                            .catch(err => console.error(err));
                    }
                });
            });
        });
    </script>
@endsection
