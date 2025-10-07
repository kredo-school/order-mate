@extends('layouts.app')

@section('title', 'Tables Management')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('manager.home')}}" class="">
            <h5 class="d-inline text-brown"><i class="fa-solid fa-angle-left text-orange"></i> {{__('manager.table_list')}}</h5>
        </a>
    </div>
    @if($tables->isEmpty())
        <p>{{__('manager.no_tables')}}</p>
    @else
        <div class="row">
            @foreach($tables as $table)
                <div class="col-6 col-md-3 mb-3">
                    <a href="{{ route('manager.tables.show', $table) }}" 
                        class="btn {{ $table->open_count > 0 ? 'btn-primary' : 'btn-outline' }} w-100">
                        @if ($table->number == 0)
                            @if ($table->open_count > 0)
                                {{__('manager.takeout')}}/{{__('manager.in_use')}}
                            @else
                                {{__('manager.takeout')}}/{{__('manager.available')}}
                            @endif
                        @else
                            @if ($table->open_count > 0)
                                {{$table->number}}/{{__('manager.in_use')}}
                            @else
                                {{$table->number}}/{{__('manager.available')}}
                            @endif
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
