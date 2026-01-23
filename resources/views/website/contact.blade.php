@extends('website.layout')

@section('title', 'Home')


@section('content')

<body>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Contact Details</h1>
        <!-- <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active text-white">Contact</li>
            </ol> -->
    </div>
    <!-- Single Page Header End -->


    <!-- Contact Start -->
    <div class="container-fluid contact">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto" style="max-width: 700px;">
                            <h1 class="text-primary">Get in touch</h1>
                            <p class="mb-4">
                                Needs essential info like phone, email, hours, and address
                                (with map), plus a simple form, clear CTAs (Call to Action),
                                links to social media, and an expected response time to build
                                trust and guide visitors, focusing on ease of use and clear
                                solutions for their needs.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="h-100 rounded">
                            <iframe
                                class="rounded w-100"
                                style="height: 400px; border:0;"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q=Maharashtra,India&z=6&output=embed">
                            </iframe>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf

                            <input type="text" name="name"
                                class="w-100 form-control border-0 py-3 mb-4"
                                placeholder="Your Name" required>

                            <input type="email" name="email"
                                class="w-100 form-control border-0 py-3 mb-4"
                                placeholder="Enter Your Email" required>

                            <textarea name="message"
                                class="w-100 form-control border-0 mb-4"
                                rows="5" placeholder="Your Message" required></textarea>

                            <button class="w-100 btn form-control border-secondary py-3 bg-white text-primary"
                                type="submit">Submit</button>
                        </form>

                        @if(session('success'))
                        <p class="text-success mt-3">{{ session('success') }}</p>
                        @endif
                    </div>

                    <div class="col-lg-5">
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Address</h4>
                                <p class="mb-2">Hadapser, Pune</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded mb-4 bg-white">
                            <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Mail Us</h4>
                                <p class="mb-2">shekharmudmesitsolution@gmail.com</p>
                            </div>
                        </div>
                        <div class="d-flex p-4 rounded bg-white">
                            <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                            <div>
                                <h4>Telephone</h4>
                                <p class="mb-2">(+91) 8421309533</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <div class="container featurs py-4">
        <div class="container">
            <div class="row g-3">

                <!-- Feature 1 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>Free Shipping</h5>
                            <p>Free on orders over $300</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>Secure Payment</h5>
                            <p>100% secure payment</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>30 Day Return</h5>
                            <p>30-day money guarantee</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>24/7 Support</h5>
                            <p>Fast support anytime</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>