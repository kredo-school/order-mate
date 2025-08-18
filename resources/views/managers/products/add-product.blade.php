@extends('layouts.app')

@section('title', 'Add Products')

@section('content')
    <main class="bg-light-mode">
        <div class="d-flex justify-content-between mt-4 mx-3">
            <a href="{{ route('manager.index') }}" class="">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Menu List</h5>
            </a>
        </div>
        <div class="container page-center">
            <!-- Card component from the provided CSS -->
            <div class="card p-4">
                <!-- Form -->
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Image upload section -->
                    <div class="form-section mb-4">
                        <label for="image" class="form-label text-brown">Image</label>
                        <div class="d-flex align-items-center">
                            <input type="file" id="image" name="image" class="d-none">
                            <button type="button" onclick="document.getElementById('image').click()"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2 text-brown">
                                <i class="fas fa-upload me-2"></i>Upload
                            </button>
                        </div>
                    </div>

                    <!-- Name field -->
                    <div class="form-section mb-4 form-underline">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder=""
                            class="form-control">
                    </div>

                    <!-- Price field -->
                    <div class="form-section mb-4 form-underline">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" id="price" name="price" value="{{ old('price') }}" placeholder="300"
                            class="form-control">
                    </div>

                    <!-- Description field -->
                    <div class="form-section mb-4 form-underline">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" id="description" name="description" value="{{ old('description') }}"
                            placeholder="Ramen ha ramen desu" class="form-control">
                    </div>

                    <!-- Category dropdown -->
                    {{-- <div class="form-section mb-4 form-underline">
                        <label for="menu_category_id" class="form-label">Category</label>
                        <select id="menu_category_id" name="menu_category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('menu_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    <!-- Tag field -->
                    <div class="form-section mb-4 form-underline">
                        <label for="tag" class="form-label">Tag</label>
                        <input type="text" id="tag" name="tag" value="{{ old('tag') }}"
                            placeholder="spicy, seafood" class="form-control">
                    </div>

                    <!-- Allergies section -->
                    <div class="form-section mb-4">
                        <label class="form-label text-brown">Allergies</label>
                        <div class="d-flex flex-wrap gap-2">
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
                    {{-- <div class="form-section mb-4 form-underline">
                        <label for="custom_groups" class="form-label">Custom</label>
                        <div class="d-grid gap-2">
                            @foreach ($customGroups as $group)
                                <select id="custom_group_{{ $group->id }}" name="custom_groups[{{ $group->id }}]"
                                    class="form-control">
                                    <option value="">{{ $group->title }}</option>
                                    @foreach ($group->customOptions as $option)
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            @endforeach
                            <div class="d-flex justify-content-between text-orange">
                                <a href="#">+ Add</a>
                                <a href="#">see all custom</a>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-3">Add</button>
                </form>
            </div>
        </div>
    </main>
@endsection
