@extends('website.layout')

@section('title', 'Home')


@section('content')

    <body>

        <!-- Single Page Header start -->
            <div class="container-fluid page-header py-5">
                <h1 class="text-center text-white display-6">Login</h1>
            </div><br><br><br><br>
        <!-- Single Page Header End -->

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>

            <p>New user? <a href="{{ route('register') }}">Register</a></p>
        </form>

    </body>