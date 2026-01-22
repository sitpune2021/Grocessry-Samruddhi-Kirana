@extends('website.layout')

@section('title', 'About Us')

@section('content')

<style>
    .about-content p {
        font-size: 16px;
        line-height: 1.8;

    }
    .new {
        margin-top: 150px;
    }
</style>

<div class="container py-5">
    <div class="row align-items-center new">

        <!-- LEFT : ABOUT CONTENT -->
        <div class="col-lg-7 col-md-12 mb-4 mb-lg-0">
            <h1 class="mb-4">About Us</h1>

            @if($about)
            <div class="about-content">
                {!! $about->content !!}
            </div>
            @else
            <p>No about content found.</p>
            @endif
        </div>

        <!-- RIGHT : STATIC IMAGE -->
        <div class="col-lg-5 col-md-12 text-center">
            <img
                src="{{ asset('https://brandcoremedia.com/wp-content/uploads/Grovery-mobile-app-development-Ahmedabad.jpg') }}"
                alt="About Image"
                class="img-fluid rounded shadow">
        </div>

    </div>
</div>

@endsection