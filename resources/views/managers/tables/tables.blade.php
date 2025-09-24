@extends('layouts.app')

@section('title', 'Tables Management')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between">
        <a href="{{ route('home')}}" class="">
            <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> Table Management</h5>
        </a>
    </div>
    @if($tables->isEmpty())
        <p>No tables found.</p>
    @else
        <div class="row">
            @foreach($tables as $table)
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ route('manager.tables.show', $table) }}" class="btn btn-outline w-100">
                        {{ $table->number }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
