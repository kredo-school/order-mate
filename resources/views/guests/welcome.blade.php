@extends('layouts.noheader')

@section('content')
<div class="container text-center py-5">
    <h2 class="fw-bold">Welcome to {{ $store->store_name }}!</h2>
    <p class="mt-3">Thank you for visiting us. Please enter the number of guests.</p>

    <form action="{{ route('guest.startOrder', [$store->store_name, $table->uuid]) }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <input type="number" name="guest_count" min="1" max="20" class="form-control w-50 mx-auto" placeholder="Number of guests" required>
        </div>
        {{-- 言語選択 --}}
        <div class="mb-3">
            <label for="language" class="form-label">Language</label>
            <select name="language" id="language" class="form-select w-50 mx-auto">
                <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>English</option>
                <option value="ja" {{ old('language') === 'ja' ? 'selected' : '' }}>日本語</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('guest.start_order') }}</button>
    </form>
</div>
@endsection
