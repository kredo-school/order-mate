@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>edit menu</h1>

        <form action="{{ route('manager.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- 名前 -->
            <div class="form-group">
                <label for="name">商品名</label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ old('name', $product->name) }}" required>
            </div>

            <!-- 価格 -->
            <div class="form-group">
                <label for="price">価格</label>
                <input type="number" name="price" id="price" class="form-control"
                    value="{{ old('price', $product->price) }}" required>
            </div>

            <!-- カテゴリ -->
            <div class="form-group">
                <label for="menu_category_id">カテゴリ</label>
                <select name="menu_category_id" id="menu_category_id" class="form-control" required>
                    @foreach ($all_categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == old('menu_category_id', $product->menu_category_id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 画像 -->
            <div class="form-group">
                <label for="image">画像</label>
                @if ($product->image)
                    <div>
                        <img src="{{ asset('storage/' . $product->image) }}" alt="image" style="max-width:150px;">
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <!-- タグ画像 -->
            <div class="form-group">
                <label for="tag">タグ画像</label>
                @if ($product->tag)
                    <div>
                        <img src="{{ asset('storage/' . $product->tag) }}" alt="tag" style="max-width:150px;">
                    </div>
                @endif
                <input type="file" name="tag" id="tag" class="form-control">
            </div>

            <!-- カスタムグループ -->
            <div class="form-group">
                <label>カスタムグループ</label>
                @foreach ($customGroups as $group)
                    <div class="form-check">
                        <input type="checkbox" name="custom_groups[{{ $loop->index }}][id]" value="{{ $group->id }}"
                            class="form-check-input" {{ $product->customGroups->contains($group->id) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $group->name }}</label>

                        <input type="hidden" name="custom_groups[{{ $loop->index }}][max_selectable]" value="1">
                        <!-- 必要なら is_required を追加 -->
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('manager.index') }}" class="btn btn-secondary">キャンセル</a>
        </form>
    </div>
@endsection
