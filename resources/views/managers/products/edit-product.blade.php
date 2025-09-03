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
                    <!-- Custom Groups -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="custom_groups" class="form-label">Custom</label>
                        </div>
                        <div class="d-grid gap-2 col-8">
                            <div id="custom-groups-wrapper">
                                {{-- 初期表示は JS が担当 --}}
                            </div>

                            <div class="d-flex justify-content-between text-brown">
                                <button type="button" id="add-custom-group" class="btn btn-link p-0 text-brown custom-link"
                                    style="text-decoration: none;">+ Add</button>
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

        // custom
        document.addEventListener('DOMContentLoaded', function() {
            const customGroups = @json($customGroups);
            const existingGroups = @json($product->customGroups);

            function createCustomGroupRow(group = null, index = null, selectedIds = []) {
                const wrapper = document.getElementById('custom-groups-wrapper');
                const rowIndex = index ?? wrapper.querySelectorAll('.custom-group-row').length;

                const row = document.createElement('div');
                row.classList.add('d-flex', 'mb-2', 'align-items-center', 'custom-group-row');

                let selectOptions = '<option value="">Select Custom Group</option>';
                customGroups.forEach(g => {
                    const disabled = selectedIds.includes(String(g.id)) ? 'disabled' : '';
                    const selected = group && g.id == group.id ? 'selected' : '';
                    selectOptions += `<option value="${g.id}" ${selected} ${disabled}>${g.title}</option>`;
                });

                row.innerHTML = `
            <select name="custom_groups[${rowIndex}][id]" class="form-select me-2 custom-group-select" data-index="${rowIndex}">
                ${selectOptions}
            </select>
            <div class="form-check me-2">
                <input type="checkbox" name="custom_groups[${rowIndex}][is_required]" value="1" class="form-check-input" ${group && group.pivot.is_required ? 'checked' : ''}>
                <label class="form-check-label">Required</label>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-custom-group">x</button>
        `;

                wrapper.appendChild(row);

                if (group) fetchCustomOptions(group.id, rowIndex, row, group.pivot.options ?? []);

                updateAllSelectOptions();
            }

            function fetchCustomOptions(groupId, index, row, preSelected = []) {
                fetch(`/manager/custom/${groupId}/options`)
                    .then(res => res.json())
                    .then(data => {
                        const optionsDiv = document.createElement('div');
                        optionsDiv.classList.add('custom-options', 'mt-2', 'w-100');

                        data.options.forEach(opt => {
                            const checkWrapper = document.createElement('div');
                            checkWrapper.classList.add('form-check');

                            const input = document.createElement('input');
                            input.type = 'checkbox';
                            input.classList.add('form-check-input');
                            input.name = `custom_groups[${index}][options][]`;
                            input.value = opt.id;
                            if (preSelected.includes(opt.id)) input.checked = true;

                            const label = document.createElement('label');
                            label.classList.add('form-check-label');
                            label.textContent = `${opt.name} (+${opt.extra_price})`;

                            checkWrapper.appendChild(input);
                            checkWrapper.appendChild(label);

                            optionsDiv.appendChild(checkWrapper);
                        });

                        row.after(optionsDiv);
                    });
            }

            function updateAllSelectOptions() {
                const selectedIds = Array.from(document.querySelectorAll('.custom-group-select'))
                    .map(select => select.value)
                    .filter(val => val);

                document.querySelectorAll('.custom-group-select').forEach(select => {
                    Array.from(select.options).forEach(option => {
                        if (option.value === "") return;
                        option.hidden = selectedIds.includes(option.value) && option.value !==
                            select.value;
                    });
                });
            }

            // ✅ 初期表示
            existingGroups.forEach((group, index) => {
                createCustomGroupRow(group, index);
            });

            // add ボタン
            document.getElementById('add-custom-group').addEventListener('click', () => {
                const selectedIds = Array.from(document.querySelectorAll('.custom-group-select'))
                    .map(select => select.value)
                    .filter(val => val);
                createCustomGroupRow(null, null, selectedIds);
            });

            // select 変更
            document.addEventListener('change', e => {
                if (e.target.matches('.custom-group-select')) {
                    const row = e.target.closest('.custom-group-row');
                    const oldOptions = row.nextElementSibling;
                    if (oldOptions && oldOptions.classList.contains('custom-options')) oldOptions.remove();
                    if (e.target.value) fetchCustomOptions(e.target.value, e.target.dataset.index, row);

                    updateAllSelectOptions();
                }
            });

            // row 削除
            document.addEventListener('click', e => {
                if (e.target.matches('.remove-custom-group')) {
                    const row = e.target.closest('.custom-group-row');
                    const oldOptions = row.nextElementSibling;
                    if (oldOptions && oldOptions.classList.contains('custom-options')) oldOptions.remove();
                    row.remove();

                    updateAllSelectOptions();
                }
            });
        });
    </script>
@endpush
