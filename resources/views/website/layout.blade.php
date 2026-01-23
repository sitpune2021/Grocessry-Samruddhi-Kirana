<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">
    <title>@yield('title', 'Samrudh Website')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Website CSS ONLY --}}
    <link href="{{ asset('website/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website/css/style.css') }}" rel="stylesheet">

    @stack('styles')

    <!-- Featurs Section End -->



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* navbar */

        /* //////////////////////index slider card  */
        .product-card {
            width: 230px;
            height: 320px;
            /* FIXED HEIGHT */
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
        }

        /* OFFER BADGE */
        .offer-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #2563eb;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 6px;
            border-radius: 4px;
        }

        /* IMAGE */
        .product-img {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-img img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }

        /* INFO */
        .product-info {
            flex-grow: 1;
        }

        .delivery-time {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .product-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
            height: 36px;
            /* 2 lines fixed */
            overflow: hidden;
        }

        .product-unit {
            font-size: 13px;
            color: #777;
            /* margin-top: 4px; */
        }

        /* PRICE ROW */
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* margin-top: 8px; */
        }

        .price-box {

            flex-direction: column;
        }

        .price-new {
            font-size: 16px;
            font-weight: 700;
        }

        .price-old {
            font-size: 12px;
            color: #999;
            text-decoration: line-through;
        }

        /* ADD BUTTON */
        .btn-add {
            border: 1px solid #28a745;
            background: #fff;
            color: #28a745;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-add:hover {
            background: #28a745;
            color: #fff;
        }

        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25D366;
            color: #fff;
            border-radius: 50px;
            padding: 12px 16px;
            font-size: 22px;
            z-index: 9999;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;

            animation: whatsappBlink 1.2s infinite;
            box-shadow: 0 0 0 rgba(37, 211, 102, 0.6);
        }

        @keyframes whatsappBlink {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }

            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }


        /*   ///////////////////new css index file ////////////*/

        /* Product Slider Base */
        .product-slider {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 10px 5px;
        }

        .product-slide-item {
            flex: 0 0 180px;
        }

        /* Image */
        .product-sm-img img {
            width: 100%;
            height: 140px;
            object-fit: contain;
        }

        /* Title */
        .product-sm-title {
            font-size: 14px;
            font-weight: 600;
            margin: 6px 0;
            height: 40px;
            overflow: hidden;
        }

        /* Footer */
        .product-sm-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Arrows */
        .slider-arrow {
            position: absolute;
            top: 40%;
            background: #fff;
            border: none;
            font-size: 20px;
            padding: 5px 10px;
            z-index: 10;
        }

        /* 🔥 MOBILE FIX */
        @media (max-width: 576px) {
            .product-slide-item {
                flex: 0 0 140px;
                /* Smaller cards */
            }

            .product-sm-img img {
                height: 110px;
            }

            .product-sm-title {
                font-size: 13px;
                height: 36px;
            }

            .btn-add-sm {
                font-size: 12px;
                padding: 5px 8px;
            }
        }

        .pagination {
            justify-content: center !important;
            flex-wrap: wrap;
        }

        .pagination .page-item {
            display: inline-flex !important;
        }

        .pagination .page-link {
            padding: 4px 8px;
            font-size: 12px;
            line-height: 1.2;
            min-width: 30px;
            height: 30px;
            border-radius: 4px;
        }

        /* Small product card () */
        .product-sm-card {
            border: 1px solid #e9e7e7;
            border-radius: 12px;
            padding: 10px;
            background: #ffffff;
            height: 100%;
            transition: box-shadow 0.2s ease;
        }

        .product-sm-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .product-sm-img {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-sm-img img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .product-sm-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
            margin: 8px 0 4px;
        }

        .product-sm-weight {
            font-size: 12px;
            color: #777;
        }

        .product-sm-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .product-sm-price {
            font-size: 14px;
            font-weight: 700;
        }

        .btn-add-sm {
            border: 1px solid #28a745;
            color: #28a745;
            background: #fff;
            padding: 3px 14px;
            font-size: 13px;
            border-radius: 8px;
        }

        .btn-add-sm:hover {
            background: #28a745;
            color: #fff;
        }

        .counter:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        .hover-shadow {
            transition: all 0.3s ease;
        }

        .service-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        .carousel,
        .carousel-inner,
        .carousel-item {
            overflow: hidden;
        }


        img {
            max-width: 100%;
            height: auto;
        }

        .product-slider {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 10px;
        }

        /* hide scrollbar */
        .product-slider::-webkit-scrollbar {
            display: none;
        }

        .product-slider {
            scrollbar-width: none;
        }

        /* 6 cards per row */
        .product-slide-item {
            flex: 0 0 calc(100% / 6 - 10px);
        }

        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #b0a5a5ff;
            color: #fff;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 22px;
            cursor: pointer;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.85;
        }

        .slider-arrow.left {
            left: -20px;
        }

        .slider-arrow.right {
            right: -20px;
        }

        .slider-arrow:hover {
            opacity: 1;
        }

        /* hide arrows on mobile */
        @media (max-width: 768px) {
            .slider-arrow {
                display: none;
            }
        }

        /* HOVER */
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }

        /* IMAGE WRAPPER */
        .category-img {
            width: 80%;
            aspect-ratio: 1 / 1;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* IMAGE FULL FIT */
        .category-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* 🔥 FULL FIT */
            border-radius: 50%;
        }


        .category-card:hover .category-img {
            transform: scale(1.05);
        }

        /* TITLE */
        .category-title {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            color: #333;
        }

        .category-row {
            display: flex;
            flex-wrap: wrap;
        }



        /* RESPONSIVE FIX */
        @media (max-width: 1200px) {
            .category-col {
                width: 20%;
            }

            /* 5 per row */
        }

        @media (max-width: 768px) {
            .category-col {
                width: 25%;
            }

            /* 4 per row */
        }

        @media (max-width: 576px) {
            .category-col {
                width: 33.33%;
            }

            /* 3 per row */
        }

        .hero-banner {
            height: 380px;
            width: 100%;
            position: relative;
            overflow: hidden;
            /* 🔴 KEY LINE */
            border-radius: 16px;
            background: #000;

        }

        .hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;

            object-position: center;
            display: block;
        }

        /* Overlay safe */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right,
                    rgba(46, 43, 43, 0.26),
                    rgba(0, 0, 0, 0.1));
            z-index: 1;
        }


        .carousel-control-prev,
        .carousel-control-next {
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: rgba(0, 0, 0, 0.45);
            border-radius: 50%;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-size: 18px 18px;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 22px;
                margin-top: 20px;
            }

            .hero-btn {
                margin-bottom: 20px;
            }
        }

        /* ===== BETTER TAB SEARCH UI ===== */

        .nav-pills {
            gap: 10px;
        }

        /* TAB BUTTON */
        .nav-pills .nav-link,
        .nav-pills a {
            background: #f5f5f5 !important;
            border-radius: 20px !important;
            padding: 8px 18px !important;
            transition: all 0.25s ease;
            border: 1px solid #e0e0e0;
        }

        /* TAB TEXT */
        .nav-pills span {
            font-size: 14px;
            font-weight: 600;
        }

        /* ACTIVE TAB */
        .nav-pills .active {
            background: #28a745 !important;
            border-color: #28a745 !important;
        }

        .nav-pills .active span {
            color: #fff !important;
        }

        /* ===== CATEGORY SEARCH DROPDOWN ===== */

        form .form-select {
            height: 44px;
            border-radius: 10px;
            border: 1px solid #dcdcdc;
            font-size: 14px;
            font-weight: 600;
            padding-left: 14px;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        /* Hover */
        form .form-select:hover {
            border-color: #28a745;
        }

        /* Focus */
        form .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.25);
        }

        /* Optional container look */
        form .col-md-4 {
            position: relative;
        }

        .product-sm-card {
            position: relative;
        }

        /* Mobile full width */
        @media (max-width: 768px) {
            form .col-md-4 {
                width: 100%;
            }
        }

        /* ALIGN RIGHT NICELY */
        @media (min-width: 992px) {
            .nav-pills {
                justify-content: flex-end;
            }
        }

        /* categry box  */

        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25D366;
            color: #fff;
            border-radius: 50%;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            z-index: 9999;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-decoration: none;
        }

        .whatsapp-float:hover {
            background: #1ebe5d;
            color: #fff;
        }

        .category-slider {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 5px;
        }

        /* scrollbar hide */
        .category-slider::-webkit-scrollbar {
            display: none;
        }

        .category-col {
            flex: 0 0 auto;
            width: 120px;
        }

        .category-card {
            display: block;
            background: #fff;
            border-radius: 10px;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }

        .category-img img {
            width: 100%;
            height: 100px;
            object-fit: contain;
        }

        .category-title {
            font-size: 14px;
            margin-top: 6px;
            font-weight: 500;
        }

        /* brand slider  */
        .brand-slider {
            overflow: hidden;
            width: 100%;
        }

        .brand-track {
            display: flex;
            gap: 16px;
            width: max-content;
        }

        .brand-col {
            width: 130px;
            flex-shrink: 0;
        }

        .brand-col img {
            width: 100%;
            height: 70px;
            object-fit: contain;
        }


        /* shop details page  */
        /* Product image hover effect */
        .product-image-wrapper {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .product-main-img {
            transition: transform 0.4s ease;
        }

        .product-image-wrapper:hover .product-main-img {
            transform: scale(1.08);
        }

        .product-hover-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-hover-overlay span {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid #fff;
            padding: 8px 18px;
            border-radius: 30px;
        }

        .product-image-wrapper:hover .product-hover-overlay {
            opacity: 1;
        }

        /* Related Products Card */
        .related-card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 6px;
            background-color: #fff;
        }

        .related-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .related-card img {
            height: 150px;
            object-fit: contain;
            transition: transform 0.3s;
        }

        .related-card:hover img {
            transform: scale(1.05);
        }

        .related-card .card-body {
            padding: 8px;
        }

        .related-card h6 {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .related-card p {
            font-size: 13px;
            margin-bottom: 6px;
        }

        .related-card .btn {
            font-size: 13px;
            padding: 6px 10px;
        }

        .offer-badge {
            background: #253bdf;
            color: #fff;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
            margin-left: 8px;
            vertical-align: middle;
        }



        .qty-box button {
            width: 32px;
            height: 32px;
            padding: 0;
        }

        .qty-input {
            height: 32px;
            font-size: 14px;
        }

        /* simi pro */

        .related-card img {
            height: 120px;
            object-fit: contain;
        }

        .related-card h6 {
            font-size: 13px;
            line-height: 1.2;
        }

        .related-card .btn {
            font-size: 12px;
            padding: 5px 10px;
        }
    </style>

    <style>
        .product-card {
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            transition: 0.3s;
            background: #fff;
        }

        .product-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .discount-ribbon {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #2563eb;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .delivery-time {
            font-size: 12px;
            color: #444;
            margin-top: 6px;
        }

        .product-img {
            height: 150px;
            object-fit: contain;
            padding: 10px;
        }

        .price {
            font-weight: 700;
            font-size: 16px;
        }

        .mrp {
            font-size: 13px;
            color: #888;
            text-decoration: line-through;
        }

        .add-btn {
            border: 1px solid #22c55e;
            color: #22c55e;
            font-weight: 700;
            border-radius: 8px;
            padding: 4px 14px;
            background: #fff;
        }

        .add-btn:hover {
            background: #22c55e;
            color: #fff;
        }

        .fruite-item {
            background: #fff;
            transition: transform .2s ease;
        }

        .fruite-item:hover {
            transform: translateY(-4px);
        }

        .product-title {
            font-size: 14px;
            line-height: 1.3;
        }

        .product-unit {
            font-size: 12px;
        }

        .price-new {
            font-size: 15px;
            font-weight: 600;
        }

        .price-old {
            font-size: 12px;
            text-decoration: line-through;
        }

        
    </style>

</head>

<body>

    {{-- Website Navbar --}}
    @include('website.partials.navbar')

    {{-- Page Content --}}
    @yield('content')

    {{-- Website Footer --}}
    @include('website.partials.footer')

    {{-- Website JS ONLY --}}
    <script src="{{ asset('website/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('website/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('website/lib/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('website/js/main.js') }}"></script>

    @stack('scripts')

    <!-- Whats app -->
    <!-- <a href="https://wa.me/918421309533" 
                target="_blank" 
                class="whatsapp-float">
                <i class="fab fa-whatsapp"></i>
            </a> -->

    <a href="https://wa.me/918421309533?text=Hello%20Team,%0A%0AI%20want%20to%20order%20grocery%20items.%0A%0APlease%20share%20today's%20price%20list%20and%20offers."
        target="_blank"
        class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

</body>

</html>