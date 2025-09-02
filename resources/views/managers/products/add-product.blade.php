@extends('layouts.app')

@section('title', 'Add Products')

@section('content')
    <main class="bg-light-mode">
        <div class="d-flex justify-content-between mt-4 mx-3">
            <a href="{{ route('manager.products.index') }}">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
            </a>
        </div>

        <div class="container page-center w-50">
            <div class="card p-4">
                <form action="{{ route('manager.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Image upload -->
                    <div class="form-section mb-4 row align-items-center">
                        <div class="col-4">
                            <label for="image" class="form-label text-brown">Image</label>
                        </div>
                        <div class="d-flex align-items-center col-8">
                            <input type="file" id="image" name="image" class="d-none" onchange="previewMenuImage(event)">
                            <button type="button" onclick="document.getElementById('image').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload menu image
                            </button>
                            <div id="menu-image-preview" class="ms-3"></div>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="name" class="form-label">Name</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control">
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="price" class="form-label">Price</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="price" name="price" value="{{ old('price') }}" class="form-control">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="description" class="form-label">Description</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="description" name="description" value="{{ old('description') }}" class="form-control">
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label for="menu_category_id" class="form-label text-brown">Category</label>
                        </div>
                        <div class="col-8">
                            <select id="menu_category_id" class="form-select me-2" name="menu_category_id">
                                @if ($all_categories->isEmpty())
                                    <option value="" selected>Please add a category first</option>
                                @else
                                    <option value="">Select Category</option>
                                    @foreach ($all_categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('menu_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Tag upload -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label for="tag" class="form-label text-brown">Tag</label>
                        </div>
                        <div class="d-flex align-items-center col-8">
                            <input type="file" id="tag" name="tag" accept="image/*" class="d-none" onchange="previewTagImage(event)">
                            <button type="button" onclick="document.getElementById('tag').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload Tag
                            </button>
                            <div id="tag-preview" class="ms-3"></div>
                        </div>
                    </div>

                    <!-- Allergies -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label class="form-label text-brown">Allergens</label>
                        </div>
                        <div class="col-8 d-flex flex-wrap gap-3">
                            @php
                                $allergens = [
                                    'milk' => 'Milk',
                                    'egg' => 'Eggs',
                                    'fish' => 'Fish',
                                    'shrimp' => 'Shrimp',
                                    'soy' => 'Soy',
                                    'wheat' => 'Wheat',
                                    'sesame' => 'Sesame',
                                    'cashew' => 'Cashew',
                                    'walnut' => 'Walnut',
                                ];
                            @endphp
                            @foreach ($allergens as $key => $label)
                                <div class="form-check text-center">
                                    <input type="checkbox" name="allergens[]" value="{{ $key }}"
                                        id="allergen-{{ $key }}" class="form-check-input d-none"
                                        {{ in_array($key, old('allergens', [])) ? 'checked' : '' }}>
                                    <label for="allergen-{{ $key }}" class="allergen-label {{ in_array($key, old('allergens', [])) ? 'selected' : '' }}">
                                        @include("icons.allergens.$key")
                                        <span class="tooltip-text">{{ $label }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-block btn-md">Add</button>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    // プレビュー: menu image
    function previewMenuImage(event) {
        const preview = document.getElementById('menu-image-preview');
        preview.innerHTML = '';
        const file = event.target.files[0];
        if (!file) return;
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.classList.add('img-thumbnail');
        img.style.maxWidth = '150px';
        img.style.maxHeight = '150px';
        preview.appendChild(img);
    }

    // プレビュー: tag image
    function previewTagImage(event) {
        const preview = document.getElementById('tag-preview');
        preview.innerHTML = '';
        const file = event.target.files[0];
        if (!file) return;
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.classList.add('img-thumbnail');
        img.style.maxWidth = '100px';
        preview.appendChild(img);
    }

    // アレルゲンの色切り替え
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="allergens[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const label = this.nextElementSibling;
                if (this.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            });
        });
    });
</script>
@endpush
