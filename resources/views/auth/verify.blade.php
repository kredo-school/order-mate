@extends('layouts.app')

@section('content')

<div class="page-center">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="p-4 bg-light-mode rounded"> 
                
                <div class="logo-area mb-4">
                    <h2 class="text-brown">{{ __('manager.verify_email') }}</h2>
                </div>

                <div class="text-brown text-center"> @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('manager.fresh_link') }}
                        </div>
                    @endif

                    <p class="mb-4">
                        {{ __('manager.check_email') }}
                    </p>
                    
                    <p>
                        {{ __('manager.not_receive') }},
                    </p>
                    
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" 
                            class="btn btn-primary btn-m mt-3 align-baseline"
                        >
                            {{ __('manager.request_another') }}
                        </button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection