@extends('layouts.app')

@section('content')

<div class="page-center">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="p-4 bg-light-mode rounded"> 
                
                <div class="logo-area mb-4">
                    <h2 class="text-brown">{{ __('Verify Your Email Address') }}</h2>
                </div>

                <div class="text-brown"> @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p class="mb-4">
                        {{ __('Before proceeding, please check your email for a verification link.') }}
                    </p>
                    
                    <p>
                        {{ __('If you did not receive the email') }},
                    </p>
                    
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" 
                            class="btn btn-primary btn-m mt-3 align-baseline"
                        >
                            {{ __('click here to request another') }}
                        </button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection