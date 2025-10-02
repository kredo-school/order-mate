@extends('layouts.app')

@section('title', 'Show Product')

@section('content')
    <main class="bg-light-mode">
        <div class="container page-center w-75">
            <div class="row align-items-center">

                <!-- 商品画像 + tag -->
                <div class="col-md-4 position-relative text-center">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded mb-3"
                            style="max-height:400px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:400px;">
                            {{__('manager.no_image')}}
                        </div>
                    @endif

                    @if ($product->tag)
                        <img src="{{ asset('storage/' . $product->tag) }}" class="position-absolute"
                            style="top:10px; left:10px; max-width:80px; border-radius:5px;">
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

                    <!-- Allergies -->
                    <div class="mb-3 d-flex justify-content-center gap-2 flex-wrap">
                        @if ($product->allergies && $product->allergies->count() > 0)
                            @foreach ($product->allergies as $allergy)
                                <i class="fa-solid fa-circle-exclamation text-danger fs-5" title="{{ $allergy->name }}"></i>
                            @endforeach
                        @else
                            <span class="text-brown">{{__('manager.no_allergens')}}</span>
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
    </main>
@endsection
