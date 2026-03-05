<!-- Footer Start -->
<!-- MAIN FOOTER -->
<footer class="footer-section">
    <div class="container py-5">
        <div class="row g-4">

            <!-- LOGO & ABOUT -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-box">
                    <img src="{{ asset('website/img/samrudhi-kirana-logo1.png') }}"
                        class="mb-3 footer-logo" alt="Logo">

                    <p class="footer-text">
                        Fresh groceries delivered at your doorstep with quality and trust.
                        Your daily needs partner.
                    </p>

                    <div class="footer-social mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- SHOP LINKS -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Shop Info</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Return Policy</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>

            <!-- ACCOUNT -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">My Account</h5>
                <ul class="footer-links">
                    <li><a href="#">My Account</a></li>
                    <!-- <li><a href="{{ route('cart') }}">Shopping Cart</a></li> -->
                    <li><a href="{{ route('my_orders') }}">Order History</a></li>
                </ul>
            </div>

            <!-- CONTACT -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Contact Us</h5>

                <p><i class="fas fa-map-marker-alt"></i> 1429 Netus Rd, NY 48247</p>
                <p><i class="fas fa-envelope"></i> example@gmail.com</p>
                <p><i class="fas fa-phone"></i> +0123 4567 8910</p>

                <div class="mt-3">
                    <img src="{{ asset('website/img/payment.png') }}"
                        class="img-fluid payment-img" alt="Payments">
                </div>
            </div>

        </div>
    </div>

    <!-- COPYRIGHT -->
    <div class="footer-bottom text-center py-3">
        © 2026 <strong>Samrudhi Kirana</strong> | Designed by SIT Solutions Pvt Ltd
    </div>
</footer>

<style>
    .footer-section {
        background: white;
        color: #5b5959;
    }

    .footer-logo {
        max-width: 160px;
    }

    .footer-text {
        font-size: 14px;
        line-height: 1.6;
    }

    .footer-title {
        color: #070101;
        margin-bottom: 20px;
        font-weight: 600;
        position: relative;
    }

    .footer-title::after {
        content: '';
        width: 40px;
        height: 2px;
        background: #28a745;
        position: absolute;
        bottom: -8px;
        left: 0;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        text-decoration: none;
        color: #333333;
        font-size: 14px;
        transition: 0.3s;
    }

    .footer-links a:hover {
        color: #28a745;
        padding-left: 5px;
    }

    .footer-social a {
        display: inline-block;
        width: 35px;
        height: 35px;
        background: #222;
        color: #fff;
        text-align: center;
        line-height: 35px;
        border-radius: 50%;
        margin-right: 8px;
        transition: 0.3s;
    }

    .footer-social a:hover {
        background: #28a745;
        transform: translateY(-3px);
    }

    .payment-img {
        max-width: 180px;
    }

    .footer-bottom {
        background: #191919a8;
        color: #f4efef;
        font-size: 14px;
    }
</style>


<!-- JavaScript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/lightbox/js/lightbox.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Template Javascript -->
<script src="js/main.js"></script>

 