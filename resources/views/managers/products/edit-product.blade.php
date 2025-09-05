@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <main class="bg-light-mode">
        <div class="d-flex justify-content-between mt-4 mx-3">
            <a href="{{ route('manager.products.index') }}">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
            </a>
        </div>

        <div class="container page-center w-50">
            <div class="card p-4">
                <form action="{{ route('manager.products.update', $product->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Image upload -->
                    <div class="form-section mb-4 row align-items-center">
                        <div class="col-4">
                            <label for="image" class="form-label text-brown">Image</label>
                        </div>
                        <div class="d-flex align-items-center col-8">
                            <input type="file" id="image" name="image" class="d-none"
                                onchange="previewMenuImage(event)">
                            <button type="button" onclick="document.getElementById('image').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload menu image
                            </button>
                            <div id="menu-image-preview" class="ms-3">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail"
                                        style="max-width:150px; max-height:150px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="name" class="form-label">Name</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                                class="form-control">
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="price" class="form-label">Price</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="price" name="price" value="{{ old('price', $product->price) }}"
                                class="form-control">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="description" class="form-label">Description</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="description" name="description"
                                value="{{ old('description', $product->description) }}" class="form-control">
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label for="menu_category_id" class="form-label text-brown">Category</label>
                        </div>
                        <div class="col-8">
                            <select id="menu_category_id" class="form-select me-2" name="menu_category_id">
                                @foreach ($all_categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('menu_category_id', $product->menu_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tag -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label for="tag" class="form-label text-brown">Tag</label>
                        </div>
                        <div class="d-flex align-items-center col-8">
                            <input type="file" id="tag" name="tag" class="d-none"
                                onchange="previewTagImage(event)">
                            <button type="button" onclick="document.getElementById('tag').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload Tag
                            </button>
                            <div id="tag-preview" class="ms-3">
                                @if ($product->tag)
                                    <img src="{{ asset('storage/' . $product->tag) }}" class="img-thumbnail"
                                        style="max-width:100px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Custom Groups -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="custom_groups" class="form-label">Custom</label>
                        </div>
                        <div class="d-grid gap-2 col-8">
                            <div id="custom-groups-wrapper">
                                @foreach ($product->customGroups as $index => $group)
                                    <div class="d-flex mb-2 align-items-center custom-group-row">
                                        <select name="custom_groups[{{ $index }}][id]"
                                            class="form-select me-2 custom-group-select" data-index="{{ $index }}">
                                            <option value="">Select Custom Group</option>
                                            @foreach ($customGroups as $g)
                                                <option value="{{ $g->id }}"
                                                    {{ $group->id == $g->id ? 'selected' : '' }}>{{ $g->title }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <div class="form-check me-2">
                                            <input type="checkbox" name="custom_groups[{{ $index }}][is_required]"
                                                value="1" class="form-check-input"
                                                {{ $group->pivot->is_required ? 'checked' : '' }}>
                                            <label class="form-check-label">Required</label>
                                        </div>

                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-custom-group">x</button>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between text-brown">
                                <button type="button" id="add-custom-group"
                                    class="btn btn-link p-0 text-brown custom-link" style="text-decoration: none;">+
                                    Add</button>
                                <a href="{{ route('manager.custom.index') }}" class="text-brown custom-link">see all
                                    custom</a>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary btn-block btn-md">Update</button>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const customGroups = @json($customGroups);

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

        // Custom Group JS (Add / Remove / Options)
        document.getElementById('add-custom-group').addEventListener('click', function() {
            const wrapper = document.getElementById('custom-groups-wrapper');
            const index = wrapper.querySelectorAll('.custom-group-row').length;
            const row = document.createElement('div');
            row.classList.add('d-flex', 'mb-2', 'align-items-center', 'custom-group-row');
            row.innerHTML = `
            <select name="custom_groups[${index}][id]" class="form-select me-2 custom-group-select" data-index="${index}">
                <option value="">Select Custom Group</option>
                ${customGroups.map(g=>`<option value="${g.id}">${g.title}</option>`).join('')}
            </select>
            <div class="form-check me-2">
                <input type="checkbox" name="custom_groups[${index}][is_required]" value="1" class="form-check-input">
                <label class="form-check-label">Required</label>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-custom-group">x</button>
        `;
            wrapper.appendChild(row);
        });

        document.addEventListener('click', function(e) {
            if (e.target.matches('.remove-custom-group')) {
                e.target.closest('.custom-group-row').remove();
            }
        });
    </script>
@endpush
