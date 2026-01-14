@extends('website.layout')

@section('title', 'Login')

@section('content')


<style>
    .card {
        background: #fff;
    }

    .form-control {
        border-radius: 10px;
    }

    .btn {
        border-radius: 10px;
    }
</style>
<!-- Page Header -->
<!-- <div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Login</h1>
</div> -->

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">

                    <h4 class="text-center mb-4">Welcome Back</h4>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter email"
                                required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter password"
                                required>
                        </div>

                        <!-- Remember -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>

                        <!-- Register -->
                        <p class="text-center mt-3 mb-0">
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