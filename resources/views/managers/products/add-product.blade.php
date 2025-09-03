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
                            <input type="file" id="image" name="image" class="d-none"
                                onchange="previewMenuImage(event)">
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
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-control">
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="price" class="form-label">Price</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="price" name="price" value="{{ old('price') }}"
                                class="form-control">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="description" class="form-label">Description</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="description" name="description" value="{{ old('description') }}"
                                class="form-control">
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
                            <input type="file" id="tag" name="tag" accept="image/*" class="d-none"
                                onchange="previewTagImage(event)">
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
                                    <label for="allergen-{{ $key }}"
                                        class="allergen-label {{ in_array($key, old('allergens', [])) ? 'selected' : '' }}">
                                        @include("icons.allergens.$key")
                                        <span class="tooltip-text">{{ $label }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom options section -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4"> <label for="custom_groups" class="form-label">Custom</label> </div>
                        <div class="d-grid gap-2 col-8">
                            <div id="custom-groups-wrapper"> <!-- ここに JS で select を追加していく -->
                                <div class="d-flex mb-2 align-items-center custom-group-row"> <select
                                        name="custom_groups[0][id]" class="form-select me-2 custom-group-select"
                                        data-index="0">
                                        <option value=""> Select Custom Group </option>
                                        @foreach ($customGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-check me-2"> <input type="checkbox"
                                            name="custom_groups[0][is_required]" value="1" class="form-check-input">
                                        <label class="form-check-label">Required</label>
                                    </div> {{-- <input type="number" name="custom_groups[0][max_selectable]" value="1" class="form-control" style="width:100px"> --}}
                                    <button type="button" class="btn btn-danger btn-sm remove-custom-group">x</button>
                                </div>
                            </div> <!-- ボタン類 -->
                            <div class="d-flex justify-content-between text-brown"> <button type="button"
                                    id="add-custom-group" class="btn btn-link p-0 text-brown custom-link"
                                    style="text-decoration: none;">+ Add</button> <a
                                    href="{{ route('manager.custom.index') }}" class="text-brown custom-link">see all
                                    custom</a> </div>
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
        const customGroups = @json($customGroups);

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

        // custom
        let selectedCustoms = [];
        let customGroupIndex = 1;

        // 利用可能 options を取得
        function getAvailableOptions() {
            return customGroups
                .filter(group => !selectedCustoms.includes(group.id))
                .map(group => `<option value="${group.id}">${group.title}</option>`)
                .join('');
        }

        // 新しい custom row を作る
        function createCustomRow(index, selectedValue = '') {
            if (selectedValue) selectedCustoms.push(parseInt(selectedValue));

            const newRow = document.createElement('div');
            newRow.classList.add('d-flex', 'mb-2', 'align-items-center', 'custom-group-row');

            newRow.innerHTML = `
        <select name="custom_groups[${index}][id]" class="form-select me-2 custom-group-select" data-index="${index}">
            <option value="">Select Custom Group</option>
            ${getAvailableOptions()}
        </select>
        <div class="form-check me-2">
            <input type="checkbox" name="custom_groups[${index}][is_required]" value="1" class="form-check-input">
            <label class="form-check-label">Required</label>
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-custom-group">x</button>
    `;

            return newRow;
        }

        // 追加ボタン
        document.getElementById('add-custom-group').addEventListener('click', function() {
            const wrapper = document.getElementById('custom-groups-wrapper');
            const newRow = createCustomRow(customGroupIndex);
            wrapper.appendChild(newRow);
            customGroupIndex++;
        });

        // 削除ボタン
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-custom-group')) {
                const row = e.target.closest('.custom-group-row');
                const select = row.querySelector('select');
                const val = parseInt(select.value);
                if (val) {
                    selectedCustoms = selectedCustoms.filter(id => id !== val);
                }

                const optionsDiv = row.nextElementSibling;
                if (optionsDiv && optionsDiv.classList.contains('custom-options')) {
                    optionsDiv.remove();
                }

                row.remove();
                updateAllSelectOptions();
            }
        });

        // 選択変更時
        document.addEventListener('change', function(e) {
            if (e.target.matches('.custom-group-select')) {
                const wrapper = e.target.closest('.custom-group-row');
                const index = e.target.dataset.index;
                const oldVal = selectedCustoms[index] || null;
                const newVal = parseInt(e.target.value);

                if (oldVal) {
                    selectedCustoms = selectedCustoms.filter(id => id !== oldVal);
                }
                if (newVal) selectedCustoms.push(newVal);
                updateAllSelectOptions();

                // 古い options を削除
                const oldOptions = wrapper.nextElementSibling;
                if (oldOptions && oldOptions.classList.contains('custom-options')) {
                    oldOptions.remove();
                }

                if (!newVal) return;

                fetch(`/manager/custom/${newVal}/options`)
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

                            const label = document.createElement('label');
                            label.classList.add('form-check-label');
                            label.textContent = `${opt.name} (+${opt.extra_price})`;

                            checkWrapper.appendChild(input);
                            checkWrapper.appendChild(label);

                            optionsDiv.appendChild(checkWrapper);
                        });

                        wrapper.after(optionsDiv);
                    });
            }
        });

        // 全 select を更新（重複防止用）
        function updateAllSelectOptions() {
            document.querySelectorAll('.custom-group-select').forEach(select => {
                const currentVal = parseInt(select.value) || '';
                select.innerHTML = `<option value="">Select Custom Group</option>` +
                    customGroups
                    .filter(group => !selectedCustoms.includes(group.id) || group.id === currentVal)
                    .map(group => `<option value="${group.id}">${group.title}</option>`)
                    .join('');
                select.value = currentVal;
            });
        }
    </script>
@endpush
