@extends('layouts.app')

@section('title', 'Show Product')

@section('content')
    <div class="bg-light-mode">
        <div class="d-flex justify-content-between mt-2 mb-4 mx-3">
            <a href="{{ route('manager.products.index') }}">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.product_detail')}}</h5>
            </a>
        </div>
        <div class="container w-75">
            <div class="row align-items-center">

                <!-- 商品画像 + tag -->
                <div class="col-md-4 position-relative text-center">
                    @if ($product->tag)
                        <img src="{{ asset('storage/' . $product->tag) }}" class="position-absolute"
                            style="top:-15px; left:-5px; max-width:80px; border-radius:5px; object-fit:cover; z-index:10;">
                    @endif

                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded mb-3" style="width: 100%; height: auto; object-fit: cover; aspect-ratio: 4 / 3;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: auto; object-fit: cover; aspect-ratio: 4 / 3;">
                            {{__('manager.no_image')}}
                        </div>
                    @endif

                </div>

                <!-- 商品情報 -->
                <div class="col-md-8 d-flex flex-column justify-content-center text-center">

                    <!-- Name -->
                    <div class="text-brown fs-3 fw-bold mb-2">
                        {{ trim($product->name) !== '' ? $product->name : 'No Name' }}
                    </div>

                    <!-- Price -->
                    <div class="text-brown fs-5 mb-1">
                        {{ isset($product->price) ? number_format($product->price, 2) . 'php' : 'No Price' }}
                    </div>

                    <!-- Category -->
                    <div class="text-brown fs-5 mb-2">
                        {{ $product->category?->name ?: 'No Category' }}
                    </div>

                    <!-- Description -->
                    <div class="text-brown fs-5 mb-3">
                        {{ trim($product->description) !== '' ? $product->description : 'No Description' }}
                    </div>

                    {{-- 変更点: Allergies 表示ブロックを menu->allergy (配列) に合わせて描画 --}}
                    <div class="mb-3 d-flex justify-content-center gap-2 flex-wrap">
                        @php
                            // 互換のため: 単一カラム名が 'allergy' または 'allergies' の可能性がある場合に対応
                            $allergenKeys = [];
                            if (!empty($product->allergy) && is_array($product->allergy)) {
                                $allergenKeys = $product->allergy;
                            } elseif (!empty($product->allergies) && is_array($product->allergies)) {
                                // 万一リレーションでCollectionが返る場合（将来の移行対応）
                                $allergenKeys = $product->allergies->pluck('key')->filter()->all();
                            } elseif (!empty($product->allergy) && is_string($product->allergy)) {
                                // JSON文字列やカンマ区切りが残っている場合に備えて
                                $decoded = json_decode($product->allergy, true);
                                if (is_array($decoded)) {
                                    $allergenKeys = $decoded;
                                } else {
                                    $allergenKeys = array_filter(array_map('trim', explode(',', $product->allergy)));
                                }
                            }
                        @endphp

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
                            <span class="text-brown">{{ __('manager.no_allergens') }}</span>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('manager.products.edit', $product->id) }}"
                            class="btn btn-light border text-brown d-flex align-items-center gap-1">
                            <i class="fa-solid fa-pen"></i> {{__('manager.edit')}}
                        </a>
                        <form action="{{ route('manager.products.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-light border text-brown d-flex align-items-center gap-1"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fa-solid fa-trash"></i> {{__('manager.delete')}}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>



        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered bg-light-mode">
                <div class="modal-content p-3 text-center">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded mb-3"
                            style="max-height:250px;">
                    @endif
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-danger fa-3x"></i>
                    </div>
                    <h5 class="mb-3">{!!__('manager.delete_product', ['product'=>$product->name])!!}</h5>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('manager.cancel')}}</button>
                        <form action="{{ route('manager.products.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{__('manager.delete')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
