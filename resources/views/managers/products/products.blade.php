@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between">
            <a href="{{ url()->previous() }}" class="">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
            </a>
            {{-- 検索フォーム（下線＋アイコンのみ） --}}
            <form action="{{ route('manager.products.index') }}" method="GET"
                class="product-search d-flex align-items-center mx-3" role="search">
                <input type="search" name="search" class="input-underline" placeholder="Search products..."
                    value="{{ request('search') }}" aria-label="Search products">
                <button type="submit" class="btn-icon text-orange ms-2" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <a href="{{ route('manager.products.create') }}" class="text-orange">
                <i class="fa-solid fa-plus"></i> Add menu
            </a>
        </div>

        {{-- category --}}
        <div class="position-relative mt-5">
            <div class="d-flex align-items-center">
                <!-- 横スクロール可能なカテゴリー部分 -->
                <div class="category-scroll d-flex">
                    {{-- カテゴリ --}}
                    @foreach ($all_categories as $category)
                        <a href="#" class="category-link" data-id="{{ $category->id }}">
                            <div class="p-0">
                                <span
                                    class="category-tab 
                    {{ !request()->has('search') && $loop->first ? 'active' : '' }}">
                                    {{ $category->name }}
                                </span>
                            </div>
                        </a>
                    @endforeach

                    {{-- 検索結果タブ（検索時のみ表示） --}}
                    @if (request()->filled('search'))
                        <a href="#" class="category-link search-results-tab" data-id="search">
                            <div class="p-0">
                                <span class="category-tab active">
                                    Search Results
                                </span>
                            </div>
                        </a>
                    @endif

                </div>
                <!-- New ボタン（右端固定） -->
                <a href="{{ route('manager.categories.index') }}" class="new-btn category-link">
                    <div class="p-0">
                        <span class="category-tab">
                            <i class="fa-solid fa-plus"></i> New
                        </span>
                    </div>
                </a>
            </div>
        </div>



        {{-- products --}}
        <div id="products-container" class="mt-4">
            {{-- products エリア（初期表示） --}}
            @include('managers.products.partials.products', ['products' => $products ?? collect()])
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.category-link');
            const container = document.getElementById('products-container');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    const categoryId = this.dataset.id;

                    // "New"ボタンだけスルー
                    if (!categoryId || categoryId === 'new') {
                        return;
                    }

                    e.preventDefault();

                    // 検索フォームをリセット
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.value = '';
                    }

                    // Search Resultsタブを非表示にする
                    const searchTab = document.querySelector('.search-results-tab');
                    if (searchTab) {
                        searchTab.remove();
                    }

                    // active切り替え
                    document.querySelectorAll('.category-tab').forEach(el => el.classList.remove(
                        'active'));
                    this.querySelector('.category-tab').classList.add('active');

                    // "Search Results" はAjaxリクエストしない
                    if (categoryId !== 'search') {
                        fetch(`/manager/products/by-category/${categoryId}`)
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
