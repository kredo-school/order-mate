@extends('layouts.app')

@section('title', 'Store Info')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <a href="{{url()->previous()}}" class="">
      <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Store Info</h5>
    </a>
  </div>

  <form action="{{ route('manager.stores.save') }}" method="POST" enctype="multipart/form-data">
    @csrf
  
    <div class="row">
      {{-- store_photo --}}
      <div class="col text-center mb-3">
        @if ($store && $store->store_photo)
          <img src="{{ Storage::url($store->store_photo) }}" alt="store_photo"
               class="img-fluid rounded" style="max-width: 200px;">
        @else
          <i class="fa-solid fa-shop fa-5x text-muted"></i>
        @endif
        <div class="mt-3">
          <input type="file" class="form-control" id="store_photo" name="store_photo" accept="image/*">
        </div>
      </div>
  
      {{-- update store info --}}
      <div class="col">
  
        {{-- Store Name --}}
        <div class="mb-3">
          <label for="store_name" class="form-label">Store Name</label>
          <input type="text" class="form-control" id="store_name" name="store_name"
                value="{{ old('store_name', $store->store_name ?? '') }}">
        </div>
  
        {{-- Address --}}
        <div class="mb-3">
          <label for="address" class="form-label">Address</label>
          <input type="text" class="form-control" id="address" name="address"
                value="{{ old('address', $store->address ?? '') }}">
        </div>
  
        {{-- Phone --}}
        <div class="mb-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="text" class="form-control" id="phone" name="phone"
                value="{{ old('phone', $store->phone ?? '') }}">
        </div>
  
        {{-- Manager Name --}}
        <div class="mb-3">
          <label for="manager_name" class="form-label">Manager Name</label>
          <input type="text" class="form-control" id="manager_name" name="manager_name"
                value="{{ old('manager_name', $store->manager_name ?? '') }}">
        </div>
  
        {{-- Open Hours --}}
        <div class="mb-3">
          <label for="open_hours" class="form-label">Open Hours</label>
          <input type="text" class="form-control" id="open_hours" name="open_hours"
                value="{{ old('open_hours', $store->open_hours ?? '') }}">
        </div>

        {{-- User Email --}}
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email"
                value="{{ old('email', $store->user->email ?? Auth::user()->email) }}">
        </div>
  
        {{-- Current Password --}}
        <div class="mb-3">
          <label for="current_password" class="form-label">Current Password</label>
          <input type="password" class="form-control" id="current_password" name="current_password">
        </div>
  
        {{-- New Password --}}
        <div class="mb-3">
          <label for="password" class="form-label">New Password</label>
          <input type="password" class="form-control" id="password" name="password">
        </div>
  
        {{-- Confirm Password --}}
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
      </div>
    </div>
    
    <div class="mt-3">
      <a href="{{ route('manager.stores.index') }}" class="btn btn-outline">Back</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
  
</div>
@endsection