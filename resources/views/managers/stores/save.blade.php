@extends('layouts.app')

@section('title', 'Store Info')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ url()->previous() }}" class="">
                <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Store Info</h5>
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
                    <div class="form-check form-switch mt-3">
                        <label class="form-check-label" for="payment_enabled">Enable Payment</label>
                        <input class="form-check-input" type="checkbox" id="payment_enabled"
                            name="payment_enabled" value="1"
                            {{ old('payment_enabled', $store->payment_enabled ?? true) ? 'checked' : '' }}>
                    </div>
                </div>


                {{-- update store info --}}
                <div class="col">

                    {{-- Store Name --}}
                    <div class="mb-3 row">
                        <label for="store_name" class="col-sm-3 col-form-label">Store Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="store_name" name="store_name"
                                value="{{ old('store_name', $store->store_name ?? '') }}">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3 row">
                        <label for="address" class="col-sm-3 col-form-label">Address</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $store->address ?? '') }}">
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3 row">
                        <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $store->phone ?? '') }}">
                        </div>
                    </div>

                    {{-- Manager Name --}}
                    <div class="mb-3 row">
                        <label for="manager_name" class="col-sm-3 col-form-label">Manager Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="manager_name" name="manager_name"
                                value="{{ old('manager_name', $store->manager_name ?? '') }}">
                        </div>
                    </div>

                    {{-- Open Hours --}}
                    <div class="mb-3 row">
                        <label for="open_hours" class="col-sm-3 col-form-label">Open Hours</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="open_hours" name="open_hours"
                                value="{{ old('open_hours', $store->open_hours ?? '') }}">
                        </div>
                    </div>

                    {{-- User Email --}}
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $store->user->email ?? Auth::user()->email) }}">
                        </div>
                    </div>

                    {{-- Currency --}}
                    <div class="mb-3 row">
                        <label for="currency" class="col-sm-3 col-form-label">Currency</label>
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
                        <label for="current_password" class="col-sm-3 col-form-label">Current Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                    </div>

                    {{-- New Password --}}
                    <div class="mb-3 row">
                        <label for="password" class="col-sm-3 col-form-label">New Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-3 row">
                        <label for="password_confirmation" class="col-sm-3 col-form-label">Confirm Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                <a href="{{ route('manager.stores.index') }}" class="btn btn-outline me-2">Back</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>

    </div>
@endsection
