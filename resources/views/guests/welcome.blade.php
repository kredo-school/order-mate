@extends('layouts.noheader')

@section('content')
<div class="container text-center page-center">
    <h2 class="fw-bold">{{__('guest.welcome', ['store'=>$store->store_name])}}</h2>
    <p class="mt-3">{{__('guest.thank_you_visiting')}}</p>

    {{-- 言語選択 --}}
    <div class="mb-3">
        <form id="locale-form" action="{{ route('guest.set_locale', [$store->store_name, $table->uuid]) }}" method="POST" class="">
            @csrf
            <div class="mb-3">
                <select name="locale" id="locale-select" class="form-select form-control w-50 mx-auto">
                    <option value="ja" {{ app()->getLocale() === 'ja' ? 'selected' : '' }}>日本語</option>
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">{{__('guest.apply')}}</button>
        </form>
    </div>
    <form action="{{ route('guest.startOrder', [$store->store_name, $table->uuid]) }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <input type="number" name="guest_count" min="1" max="20" class="form-control w-50 mx-auto" placeholder="{{__('guest.number_of_guests')}}" required>
        </div>
        {{-- 言語を hidden で送信 --}}
        <input type="hidden" name="language" value="{{ session('locale', app()->getLocale()) }}">
        <button type="submit" class="btn btn-primary">{{ __('guest.start_order') }}</button>
    </form>
</div>
@push('scripts')
<script>
document.getElementById('locale-form').addEventListener('submit', function(e){
    e.preventDefault();
    const form = e.currentTarget;
    const fd = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
        body: fd
    }).then(r => {
        if (r.ok) {
            window.location.reload();
        } else {
            alert('{{__('guest.failed_change_language')}}');
        }
    }).catch(() => alert('{{__('guest.network_error')}}'));
});
</script>
@endpush
@endsection
