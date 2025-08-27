@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between">
    <a href="{{url()->previous()}}" class="">
      <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
    </a>
    <input type="search" name="search_product" id="search_product">
    <a href="{{route('manager.create')}}" class="text-orange">
      <i class="fa-solid fa-plus"></i> Add
    </a>
  </div>

  {{-- category --}}
  <div class="position-relative mt-5">
    <div class="d-flex align-items-center">
        <!-- 横スクロール可能なカテゴリー部分 -->
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
    {{-- products エリア（初期表示）--}}
    @include('managers.products.partials.products', ['products' => $products ?? collect()])
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      const tabs = document.querySelectorAll('.category-link');
      const container = document.getElementById('products-container');

      tabs.forEach(tab => {
          tab.addEventListener('click', function (e) {
              const categoryId = this.dataset.id;

              if (!categoryId) {
                  // "New"ボタンの場合はデフォルトのリンク動作を許可
                  return;
              }

              e.preventDefault();

              // activeクラス切り替え
              document.querySelectorAll('.category-tab').forEach(el => el.classList.remove('active'));
              this.querySelector('.category-tab').classList.add('active');

              // Ajaxで商品一覧を取得
              fetch(`/manager/products/by-category/${categoryId}`)
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