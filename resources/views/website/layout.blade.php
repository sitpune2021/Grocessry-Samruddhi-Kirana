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
