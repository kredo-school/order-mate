@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <!-- flexboxで縦横中央配置 -->
        <div class="col-md-4 d-flex flex-column align-items-center justify-content-center">
            <a href="#" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex poppin" style="height: 100%">
                    Order List
                </div>
            </a>
            <a href="{{route('manager.index')}}" class="card mb-4 bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex" style="height: 100%">
                    Admin Menu
                </div>
            </a>
            <a href="#" class="card bg-orange border-0 shadow-sm text-white text-decoration-none w-100" style="height: 100px">
                <div class="card-body align-items-center justify-content-center d-flex" style="height: 100%">
                    Store Info
                </div>
            </a>
        </div>
    </div>
</div>
@endsection