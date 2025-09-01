@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $product->name }}</h1>
        <p>価格: {{ $product->price }}円</p>
        <p>カテゴリ: {{ $product->category ? $product->category->name : '未分類' }}</p>

        @if ($product->image)
            <div>
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width:200px;">
            </div>
        @endif

        @if ($product->tag)
            <div>
                <img src="{{ asset('storage/' . $product->tag) }}" alt="tag" style="max-width:100px;">
            </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('manager.products.edit', $product->id) }}" class="btn btn-warning">編集する</a>
            <a href="{{ route('manager.index') }}" class="btn btn-secondary">一覧へ戻る</a>
        </div>
    </div>
@endsection
