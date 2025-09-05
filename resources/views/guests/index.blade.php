@extends('layouts.app')
@section('title', 'Guest Home')
@section('content')
<div class="container">
  <div class="d-flex justify-content-between">
    <h5 class="d-inline text-brown">Menu List</h5>
    <input type="search" name="search_product" id="search_product">
  </div>

  {{-- category --}}
  <div class="position-relative mt-5">
    <div class="d-flex align-items-center">
        <div class="category-scroll d-flex">
            @foreach ($all_categories as $category)
                <a href="#" 
                    class="category-link" 
                    data-id="{{ $category->id }}">
                    <div class="p-0">
                        <span class="category-tab {{ $loop->first ? 'active' : '' }}">
                            {{ $category->name }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
  </div>

  {{-- products --}}
  <div id="products-container" class="mt-4">
    @include('managers.products.partials.products', ['products' => $products ?? collect()])
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      const tabs = document.querySelectorAll('.category-link');
      const container = document.getElementById('products-container');

      // Blade から渡された変数
      const isGuestPage = @json($isGuestPage);
      const storeName = @json($store->store_name ?? '');
      const tableUuid = @json($table->uuid ?? '');

      tabs.forEach(tab => {
          tab.addEventListener('click', function (e) {
              const categoryId = this.dataset.id;
              if (!categoryId) return;

              e.preventDefault();

              // active クラス切り替え
              document.querySelectorAll('.category-tab').forEach(el => el.classList.remove('active'));
              this.querySelector('.category-tab').classList.add('active');

              // Ajax URL
              const url = isGuestPage 
                  ? `/guest/${storeName}/${tableUuid}/products/${categoryId}` 
                  : `/manager/products/by-category/${categoryId}`;

              fetch(url)
                  .then(res => res.text())
                  .then(html => {
                      container.innerHTML = html;
                  })
                  .catch(err => console.error(err));
          });
      });
  });
</script>
@endsection
