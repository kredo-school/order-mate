@extends('layouts.app')

@section('title', 'Store Info')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ url()->previous() }}" class="">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.store_info')}}</h5>
            </a>
        </div>

        <form action="{{ route('manager.stores.save') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- store_photo --}}
                <div class="col text-center mb-3">
                    @if ($store && $store->store_photo)
                        <img src="{{ Storage::url($store->store_photo) }}" alt="store_photo" class="img-fluid rounded"
                            style="max-width: 200px;">
                    @else
                        <i class="fa-solid fa-shop fa-5x text-muted"></i>
                    @endif
                    <div class="mt-3">
                        <input type="file" class="form-control" id="store_photo" name="store_photo" accept="image/*">
                    </div>

                    {{-- Payment On/Off --}}
                    <div class="form-check form-switch mt-3 text-start">
                        <label class="form-check-label" for="payment_enabled"> {{__('manager.enable_payment')}}</label>
                        <input class="form-check-input" type="checkbox" id="payment_enabled"
                            name="payment_enabled" value="1"
                            {{ old('payment_enabled', $store->payment_enabled ?? true) ? 'checked' : '' }}>
                    </div>

                    {{-- Language --}}
                    <div class="mt-3 d-flex">
                        <label for="language" class="form-label">{{__('manager.language')}}</label>
                        <select class="form-select ms-2" id="language" name="language">
                            <option value="en" {{ old('language', $store->language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="ja" {{ old('language', $store->language ?? 'en') === 'ja' ? 'selected' : '' }}>日本語</option>
                        </select>
                    </div>
                </div>


                {{-- update store info --}}
                <div class="col">

                    {{-- Store Name --}}
                    <div class="mb-3 row">
                        <label for="store_name" class="col-sm-3 col-form-label">{{__('manager.store_name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="store_name" name="store_name"
                                value="{{ old('store_name', $store->store_name ?? '') }}">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3 row">
                        <label for="address" class="col-sm-3 col-form-label">{{__('manager.address')}}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $store->address ?? '') }}">
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3 row">
                        <label for="phone" class="col-sm-3 col-form-label">{{__('manager.phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $store->phone ?? '') }}">
                        </div>
                    </div>

                    {{-- Manager Name --}}
                    <div class="mb-3 row">
                        <label for="manager_name" class="col-sm-3 col-form-label">{{__('manager.manager_name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="manager_name" name="manager_name"
                                value="{{ old('manager_name', $store->manager_name ?? '') }}">
                        </div>
                    </div>

                    {{-- Open Hours --}}
                    <div class="mb-3 row">
                        <label for="open_hours" class="col-sm-3 col-form-label">{{__('manager.open_hours')}}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="open_hours" name="open_hours"
                                value="{{ old('open_hours', $store->open_hours ?? '') }}">
                        </div>
                    </div>

                    {{-- User Email --}}
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-3 col-form-label">{{__('manager.email')}}</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $store->user->email ?? Auth::user()->email) }}">
                        </div>
                    </div>

                    {{-- Currency --}}
                    <div class="mb-3 row">
                        <label for="currency" class="col-sm-3 col-form-label">{{__('manager.currency')}}</label>
                        <div class="col-sm-9">
                            <select name="currency" id="currency" class="form-select">
                                @foreach (config('currencies') as $code => $label)
                                    <option value="{{ $code }}" {{ old('currency', $store->currency ?? 'php') === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Current Password --}}
                    <div class="mb-3 row">
                        <label for="current_password" class="col-sm-3 col-form-label">{{__('manager.current_password')}}</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div class="mb-3 row">
                        <label for="password" class="col-sm-3 col-form-label">{{__('manager.new_password')}}</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-3 row">
                        <label for="password_confirmation" class="col-sm-3 col-form-label">{{__('manager.confirm_password')}}</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                <a href="{{ route('manager.stores.index') }}" class="btn btn-outline me-2">{{__('manager.back')}}</a>
                <button type="submit" class="btn btn-primary">{{__('manager.save')}}</button>
            </div>
        </form>

    </div>
@endsection
