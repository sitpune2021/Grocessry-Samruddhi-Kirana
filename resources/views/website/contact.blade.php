@extends('website.layout')

@section('title', 'Home')


@section('content')

<body>
    <style>
        .contact-section {
            background: #f8f9fa;
            margin-top:130px;
        }

        .contact-title {
            font-size: 36px;
            font-weight: 700;
            color: #222;
        }

        .contact-subtitle {
            color: #666;
            max-width: 600px;
            margin: auto;
        }

        .contact-form-box,
        .contact-info-box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
        }

        .custom-input {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            transition: 0.3s;
        }

        .custom-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.25);
        }

        .contact-btn {
            background: linear-gradient(45deg, #717071, #474747);
            /* color: #d81414; */
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s;
        }

        .contact-btn:hover {
            opacity: 0.9;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .contact-info-item i {
            font-size: 22px;
            color: #28a745;
            margin-right: 15px;
            margin-top: 5px;
        }

        .contact-info-item h6 {
            font-weight: 600;
            margin-bottom: 5px;
        }
    </style>
    <!-- Contact Start -->
    <section class="contact-section py-5" >
        <div class="container">

            <!-- SECTION TITLE -->
            <div class="text-center mb-5">
                <h2 class="contact-title">Contact Us</h2>
                <p class="contact-subtitle">
                    We’d love to hear from you. Please fill out the form below
                    and our team will get back to you within 24 hours.
                </p>
            </div>

            <div class="row g-5">

                <!-- LEFT SIDE - FORM -->
                <div class="col-lg-7">
                    <div class="contact-form-box shadow-sm">

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name"
                                        class="form-control custom-input"
                                        placeholder="Enter your name" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email"
                                        class="form-control custom-input"
                                        placeholder="Enter your email" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Your Message</label>
                                <textarea name="message"
                                    class="form-control custom-input"
                                    rows="5"
                                    placeholder="Write your message here..."
                                    required></textarea>
                            </div>

                            <button type="submit"
                                class="btn contact-btn text-white">
                                Send Message
                            </button>
                        </form>

                        @if(session('success'))
                        <div class="alert alert-success mt-4">
                            {{ session('success') }}
                        </div>
                        @endif

                    </div>
                </div>

                <!-- RIGHT SIDE - CONTACT INFO -->
                <div class="col-lg-5">
                    <div class="contact-info-box shadow-sm">

                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h6>Our Location</h6>
                                <p>Hadapser, Pune, Maharashtra</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h6>Email Us</h6>
                                <p>shekharmudmesitsolution@gmail.com</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <h6>Call Us</h6>
                                <p>(+91) 8421309533</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <iframe
                                class="rounded w-100"
                                style="height: 200px; border:0;"
                                loading="lazy"
                                allowfullscreen
                                src="https://www.google.com/maps?q=Hadapser,Pune&z=14&output=embed">
                            </iframe>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

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