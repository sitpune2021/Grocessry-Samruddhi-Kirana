@extends('website.layout')

@section('title', 'Home')


@section('content')

    <body>

        <!-- Single Page Header start -->
            <div class="container-fluid page-header py-5">
                <h1 class="text-center text-white display-6">Register</h1>
            </div><br><br><br><br>
        <!-- Single Page Header End -->


        <form method="POST" action="{{ route('register') }}">
            @csrf

            <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}">
            @error('name')
                <div style="color:red;">{{ $message }}</div>
            @enderror

            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
            @error('email')
                <div style="color:red;">{{ $message }}</div>
            @enderror

            <input type="text" name="mobile" placeholder="Mobile Number" value="{{ old('mobile') }}">
            @error('mobile')
                <div style="color:red;">{{ $message }}</div>
            @enderror

            <input type="password" name="password" placeholder="Password">
            @error('password')
                <div style="color:red;">{{ $message }}</div>
            @enderror

            <button type="submit">Register</button>
        
        </form>


    </body>