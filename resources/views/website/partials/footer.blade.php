<!-- Footer Start -->
<div class="container-fluid footer pt-4 mt-5" style="background-color:#bae6fd; color:#0c4a6e;">
    <div class="container py-4">

        <!-- Top Row -->
        <div class="row align-items-center pb-3 mb-4" style="border-bottom:1px solid rgba(12,74,110,0.3);">

            <!-- Logo -->
            <div class="col-lg-3 text-center text-lg-start mb-3 mb-lg-0">
                <a href="#">
                    <img src="{{ asset('website/img/samrudhi-kirana-logo1.png') }}" style="width:140px;" alt="Logo">
                </a>
            </div>

            <!-- Subscribe -->
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="position-relative">
                    <input type="email" id="subscribeEmail" class="form-control border rounded-pill py-2 px-4" placeholder="Enter your email">
                    <button type="button" id="subscribeBtn" class="btn btn-primary text-white rounded-pill px-4 position-absolute" style="top:2px; right:2px;">
                        Subscribe
                    </button>
                </div>
            </div>

            <!-- Social Icons -->
            <div class="col-lg-3 text-center text-lg-end">
                <a class="btn btn-sm rounded-circle me-1" href="#" style="border: 1px solid black; color: black;">
                    <i class="fab fa-twitter"></i>
                </a>
                <a class="btn btn-sm rounded-circle me-1" href="#" style="border: 1px solid black; color: black;">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a class="btn btn-sm rounded-circle me-1" href="#" style="border: 1px solid black; color: black;">
                    <i class="fab fa-youtube"></i>
                </a>
                <a class="btn btn-sm rounded-circle" href="#" style="border: 1px solid black; color: black;">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>

        </div>

        <!-- Footer Content -->
      <div class="container-fluid py-4" style="
    background: linear-gradient(135deg, #38bdf8, #0ea5e9);
    box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    min-height: 100px;
">
            <div class="container">
                <div class="row g-4 justify-content-center text-center text-lg-start">

                    <!-- Shop Info -->
                    <div class="col-lg-3 col-md-6">
                        <h6 class="text-white fw-semibold mb-3">Shop Info</h6>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">About Us</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Contact Us</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Privacy Policy</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Terms & Conditions</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Return Policy</a>
                        <a class="d-block text-white text-decoration-none" href="#">FAQs & Help</a>
                    </div>

                    <!-- Account -->
                    <div class="col-lg-3 col-md-6">
                        <h6 class="text-white fw-semibold mb-3">Account</h6>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">My Account</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Shop Details</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Shopping Cart</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Wishlist</a>
                        <a class="d-block text-white text-decoration-none mb-2" href="#">Order History</a>
                        <a class="d-block text-white text-decoration-none" href="#">International Orders</a>
                    </div>

                    <!-- Contact -->
                    <div class="col-lg-3 col-md-6">
                        <h6 class="text-white fw-semibold mb-3">Contact</h6>
                        <p class="text-white mb-1">📍 1429 Netus Rd, NY 48247</p>
                        <p class="text-white mb-1">✉️ example@gmail.com</p>
                        <p class="text-white mb-2">📞 +0123 4567 8910</p>
                        <span class="text-white fw-semibold">Payment Accepted</span><br>
                        <img src="img/payment.png" class="img-fluid mt-2" style="max-width:140px;" alt="Payment">
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- Footer End -->

<!-- Copyright -->
<div class="container-fluid py-3" style="background-color:#000000;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start text-white small">
                © SIT SOLUTIONS PVT LTD. All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end text-white small">
                Designed By <strong>Shekhar</strong>
            </div>
        </div>
    </div>
</div>

<!-- JS Includes -->
<!-- Bootstrap 5 JS & Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<!-- FontAwesome JS -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- Custom JS -->
<script>
    document.getElementById('subscribeBtn').addEventListener('click', function() {
        const email = document.getElementById('subscribeEmail').value.trim();
        if(email) {
            alert('Thank you for subscribing with: ' + email);
            document.getElementById('subscribeEmail').value = '';
        } else {
            alert('Please enter a valid email address.');
        }
    });
</script>
