@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
    {{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">
@endpush

@section('main')
    <div class="card card-primary">


        <div class="card-header">
            <h4>Login</h4>
        </div>
        {{-- <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="text-right">Login</h4>
            <!-- Section for the logos, they will be side by side -->
            <div class="d-flex align-items-center">
                <!-- Logo BPKPD -->
                <img src="{{ asset('img/logo_arch_web.png') }}" style="height: 50px;">
            </div>
        </div> --}}

        <div class="card-body">
            <form method="POST"
                action="{{route('login')}}"
                class="needs-validation"
                novalidate="">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email"
                        type="email"
                        class="form-control @error('email')
                        is-invalid
                        @enderror"
                        name="email"
                        tabindex="1"
                        autofocus>
                        @error('email')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror

                </div>

                <div class="form-group">
                    <div class="d-block">
                        <label for="password"
                            class="control-label ">Password</label>

                    </div>
                    <input id="password"
                        type="password"
                        class="form-control @error('password')
                        is-invalid
                        @enderror"
                        name="password"
                        tabindex="2">
                        @error('password')
                        <div class="invalid-feedback">
                            {{$message}}
                        @enderror

                </div>



                <div class="form-group">
                    <button type="submit"
                        class="btn btn-primary btn-lg btn-block"
                        tabindex="4">
                        Login
                    </button>
                </div>
            </form>


        </div>
    </div>
    {{-- <div class="text-muted mt-5 text-center">
        Don't have an account? <a href="{{route('register')}}">Create One</a>
    </div> --}}
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
