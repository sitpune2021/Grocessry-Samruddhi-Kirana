@extends('website.layout')

@section('title', 'Login')

@section('content')

<div class="container py-5 mt-5">
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6 col-md-6 ">
            <div class="card shadow border-0 rounded-4 mt-5">
                <div class="card-body">

                    <h4 class="text-center mb-4">Welcome Back</h4>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email + Password -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email address</label>
                                <input type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Enter email">
                                @error('email')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter password">
                                @error('password')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>

                        <!-- Register -->
                        <p class=" mt-3 mb-0">
                            New user?
                            <a href="{{ route('register') }}" class="text-primary fw-semibold">
                                Register
                            </a>
                        </p>

                    </form>

                </div>
            </div>


        </div>
    </div>
</div>

@endsection