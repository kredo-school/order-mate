@extends('layouts.app')

@section('title', 'Add Products')

@section('content')
    <main class="bg-light-mode">
        <div class="d-flex justify-content-between mt-4 mx-3">
            <a href="{{ route('manager.index') }}" class="">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
            </a>
        </div>
        <div class="container page-center w-50">
            <!-- Card component from the provided CSS -->
            <div class="card p-4">
                <!-- Form -->
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Image upload section -->
                    <div class="form-section mb-4 row align-items-center">
                        <div class="col-4">
                            <label for="image" class="form-label text-brown">Image</label>
                        </div>
                        <div class="d-flex align-items-center col-8">
                            <input type="file" id="image" name="image" class="d-none">
                            <button type="button" onclick="document.getElementById('image').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload menu image
                            </button>
                            <!-- プレビューエリア -->
                            <div id="menu-image-preview" class="ms-3"></div>
                        </div>
                    </div>

                    <!-- Name field -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="name" class="form-label">Name</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder=""
                                class="form-control">
                        </div>
                    </div>

                    <!-- Price field -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="price" class="form-label">Price</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="price" name="price" value="{{ old('price') }}" placeholder=""
                                class="form-control">
                        </div>
                    </div>

                    <!-- Description field -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="description" class="form-label">Description</label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="description" name="description" value="{{ old('description') }}"
                                placeholder="" class="form-control">
                        </div>
                    </div>

                    <!-- Category dropdown -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label for="menu_category_id" class="form-label">Category</label>
                        </div>
                        <div class="col-8">
                            <select id="menu_category_id" class="form-select me-2" name="menu_category_id">
                                @if ($all_categories->isEmpty())
                                    <option value="" class="text-brown" selected>Please add a category first</option>
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


                    <!-- Tag field (画像アップロードに変更) -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label for="tag" class="form-label">Tag</label>
                        </div>
                        <div class="d-flex align-items-center col-8">
                            <input type="file" id="tag" name="tag" accept="image/*" class="d-none"
                                onchange="previewTagImage(event)">
                            <button type="button" onclick="document.getElementById('tag').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload Tag
                            </button>
                            <!-- プレビュー -->
                            <div id="tag-preview" class="ms-3">
                                @if (old('tag'))
                                    <img src="{{ old('tag') }}" alt="Tag Preview" class="img-thumbnail"
                                        style="max-width: 100px;">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Allergies section -->
                    <div class="form-section mb-4 row">
                        <div class="col-4">
                            <label class="form-label text-brown">Allergies</label>
                        </div>
                        <div class="d-flex flex-wrap gap-2 col-8">
                            <!-- Example allergy items using your CSS classes -->
                            <div class="allergy-item">
                                <i class="fas fa-egg allergy-icon"></i>
                            </div>
                            <div class="allergy-item">
                                <i class="fas fa-fish allergy-icon"></i>
                            </div>
                            <div class="allergy-item">
                                <i class="fas fa-wheat-alt allergy-icon"></i>
                            </div>
                            <div class="allergy-item">
                                <i class="fas fa-seedling allergy-icon"></i>
                            </div>
                            <div class="allergy-item">
                                <i class="fas fa-cheese allergy-icon"></i>
                            </div>
                            <div class="allergy-item">
                                <i class="fas fa-shrimp allergy-icon"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Custom options section -->
                    <div class="form-section mb-4 row form-underline">
                        <div class="col-4">
                            <label for="custom_groups" class="form-label">Custom</label>
                        </div>

                        <div class="d-grid gap-2 col-8">
                            <div id="custom-groups-wrapper">
                                <!-- ここに JS で select を追加していく -->
                                <div class="d-flex mb-2 align-items-center custom-group-row">
                                    <select name="custom_groups[0][id]" class="form-select me-2 custom-group-select"
                                        data-index="0">
                                        <option value=""> Select Custom Group </option>
                                        @foreach ($customGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->title }}</option>
                                        @endforeach
                                    </select>

                                    <div class="form-check me-2">
                                        <input type="checkbox" name="custom_groups[0][is_required]" value="1"
                                            class="form-check-input">
                                        <label class="form-check-label">Required</label>
                                    </div>

                                    {{-- <input type="number" name="custom_groups[0][max_selectable]" value="1"
                                        class="form-control" style="width:100px"> --}}

                                    <button type="button" class="btn btn-danger btn-sm remove-custom-group">x</button>
                                </div>
                            </div>

                            <!-- ボタン類 -->
                            <div class="d-flex justify-content-between text-brown">
                                <button type="button" id="add-custom-group"
                                    class="btn btn-link p-0 text-brown custom-link" style="text-decoration: none;">+
                                    Add</button>
                                <a href="{{ route('manager.custom.index') }}" class="text-brown custom-link">see all
                                    custom</a>
                            </div>
                        </div>
                    </div>


                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary btn-block btn-md">Add</button>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const customGroups = @json($customGroups);

        /**
         * ====== LocalStorage 保存・復元 ======
         */

        // フォーム状態を保存
        function saveFormState() {
            const state = [];
            document.querySelectorAll('.custom-group-row').forEach((row, index) => {
                const groupId = row.querySelector('.custom-group-select').value;
                const isRequired = row.querySelector(`[name="custom_groups[${index}][is_required]"]`).checked;
                const options = Array.from(document.querySelectorAll(
                        `input[name="custom_groups[${index}][options][]"]:checked`))
                    .map(opt => opt.value);
                state.push({
                    groupId,
                    isRequired,
                    options
                });
            });
            sessionStorage.setItem('customFormState', JSON.stringify(state));
        }

        // フォーム状態を復元
        function restoreFormState() {
            const saved = sessionStorage.getItem('customFormState');
            if (!saved) return;
            const state = JSON.parse(saved);

            state.forEach((item, idx) => {
                if (idx > 0) {
                    addCustomGroupRow();
                }
                const row = document.querySelectorAll('.custom-group-row')[idx];
                const select = row.querySelector('.custom-group-select');
                select.value = item.groupId;

                if (item.isRequired) {
                    row.querySelector(`[name="custom_groups[${idx}][is_required]"]`).checked = true;
                }

                // options を fetch してからチェックを反映
                if (item.groupId) {
                    fetch(`/manager/custom/${item.groupId}/options`)
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
                                input.name = `custom_groups[${idx}][options][]`;
                                input.value = opt.id;

                                if (item.options.includes(opt.id.toString())) {
                                    input.checked = true;
                                }

                                const label = document.createElement('label');
                                label.classList.add('form-check-label');
                                label.textContent = `${opt.name} (+${opt.extra_price})`;

                                checkWrapper.appendChild(input);
                                checkWrapper.appendChild(label);

                                optionsDiv.appendChild(checkWrapper);
                            });

                            row.after(optionsDiv);
                            updateAvailableGroups(); // 選択肢を最新化
                        });
                }
            });
        }

        /**
         * ====== Custom Group 行の追加 ======
         */
        function addCustomGroupRow() {
            const wrapper = document.getElementById('custom-groups-wrapper');
            const index = wrapper.querySelectorAll('.custom-group-row').length;

            const row = document.createElement('div');
            row.classList.add('d-flex', 'mb-2', 'align-items-center', 'custom-group-row');

            @verbatim
            row.innerHTML = `
            <select name="custom_groups[${index}][id]" 
                    class="form-select me-2 custom-group-select" 
                    data-index="${index}">
                <option value=""> Select Custom Group </option>
                ${customGroups.map(group => `<option value="${group.id}">${group.title}</option>`).join('')}
            </select>

            <div class="form-check me-2">
                <input type="checkbox" name="custom_groups[${index}][is_required]" value="1"
                       class="form-check-input">
                <label class="form-check-label">Required</label>
            </div>

            <button type="button" class="btn btn-danger btn-sm remove-custom-group">x</button>
        `;
        @endverbatim

        wrapper.appendChild(row);
        updateAvailableGroups();
        }

        /**
         * ====== 重複防止のため選択肢更新 ======
         */
        function updateAvailableGroups() {
            const selectedValues = Array.from(document.querySelectorAll('.custom-group-select'))
                .map(sel => sel.value)
                .filter(val => val);

            document.querySelectorAll('.custom-group-select').forEach(select => {
                const currentValue = select.value;

                Array.from(select.options).forEach(option => {
                    if (!option.value) return; // 空選択肢は除外
                    option.disabled = selectedValues.includes(option.value) && option.value !==
                        currentValue;
                });
            });
        }

        /**
         * ====== イベント ======
         */

        // Select が変わったら options を表示
        document.addEventListener('change', function(e) {
            if (e.target.matches('.custom-group-select')) {
                const groupId = e.target.value;
                const wrapper = e.target.closest('.custom-group-row');
                const index = e.target.dataset.index;

                // 古い options を削除
                const oldOptions = wrapper.nextElementSibling;
                if (oldOptions && oldOptions.classList.contains('custom-options')) {
                    oldOptions.remove();
                }

                if (!groupId) {
                    saveFormState();
                    updateAvailableGroups();
                    return;
                }

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

                            const label = document.createElement('label');
                            label.classList.add('form-check-label');
                            label.textContent = `${opt.name} (+${opt.extra_price})`;

                            checkWrapper.appendChild(input);
                            checkWrapper.appendChild(label);

                            optionsDiv.appendChild(checkWrapper);
                        });

                        wrapper.after(optionsDiv);
                        saveFormState();
                        updateAvailableGroups();
                    });
            }
        });

        // Add ボタン
        document.getElementById('add-custom-group').addEventListener('click', function() {
            addCustomGroupRow();
            saveFormState();
        });

        // 行削除
        document.addEventListener('click', function(e) {
            if (e.target.matches('.remove-custom-group')) {
                const row = e.target.closest('.custom-group-row');
                const optionsDiv = row.nextElementSibling;
                if (optionsDiv && optionsDiv.classList.contains('custom-options')) {
                    optionsDiv.remove();
                }
                row.remove();
                saveFormState();
                updateAvailableGroups();
            }
        });

        // 変更イベントで保存
        document.addEventListener('change', saveFormState);
        document.addEventListener('click', saveFormState);

        // 初期化
        document.addEventListener('DOMContentLoaded', function() {
            restoreFormState();
            updateAvailableGroups();
        });

        // Tag img 用
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
    </script>
@endpush
