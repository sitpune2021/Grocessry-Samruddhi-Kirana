<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3 text-white">
                    <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                    123 Street, New York
                </small>
            </div>
        </div>
    </div>

    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="#" class="navbar-brand">
                <h1 class="text-primary display-6">Fruitables</h1>
            </a>
        </nav>
    </div>
</div>

<!-- Spinner Start -->
<!-- <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div> -->
<!-- Spinner End -->

<!-- Modal Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="input-group w-75 mx-auto d-flex">
                    <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Search End -->
 <style>
.blink-alert {
    animation: blink 1s infinite;
}

@keyframes blink {
    0%   { opacity: 1; }
    50%  { opacity: 0.3; }
    100% { opacity: 1; }
}
</style>



<!-- Navbar start -->
<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">123 Street, New York</a></small>
                <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">Email@Example.com</a></small>
            </div>
            <!-- Alert Message -->
            <div class="alert text-center m-0 py-1" id="order-alert" style="font-size: 12px;">
                Online orders are <span id="order-status"></span> (6 AM - 7 PM)
            </div>
        </div>
    </div>
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img src="{{ asset('website/img/samrudhi-kirana-logo1.png') }}" style="width:120px">
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="{{ route('home') }}" class="nav-item nav-link active">Home</a>
                    <a href="{{ route('shop') }}" class="nav-item nav-link">Shop</a>

                    <!-- <a href="shop-detail.html" class="nav-item nav-link">Shop Detail</a>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="cart.html" class="dropdown-item">Cart</a>
                                    <a href="chackout.html" class="dropdown-item">Chackout</a>
                                    <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                    <a href="404.html" class="dropdown-item">404 Page</a>
                                </div>
                            </div> -->
                    <a href="{{ route('contact') }}" class="nav-item nav-link">Contact</a>
                </div>

                <div class="d-flex m-3 me-0">

                    <!-- Search -->
                    <!-- <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4"
                                data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="fas fa-search text-primary"></i>
                            </button> -->

                    <a href="{{ route('cart') }}" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-cart fa-2x"></i>


                        <span id="cart-count"
                            class="position-absolute bg-secondary rounded-circle d-flex align-items-center 
                         justify-content-center text-dark px-1"
                            style="top:-5px; left:15px; height:20px; min-width:20px;
                      {{ $cartCount > 0 ? '' : 'display:none;' }}">
                            {{ $cartCount }}
                        </span>
                    </a>


                    <!-- User -->
                    @auth
                    <div class="dropdown">
                        <a href="#" class="my-auto dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user fa-2x"></i>
                            <span class="ms-1">{{ Auth::user()->first_name ?? '' }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('websitelogout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="my-auto">
                        <i class="fas fa-user fa-2x"></i>
                    </a>
                    @endauth

                </div>

            </div>
        </nav>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const now = new Date();
    const hour = now.getHours(); // 0 - 23

    const alertDiv = document.getElementById('order-alert');
    const statusSpan = document.getElementById('order-status');

    if (hour >= 6 && hour < 19) {
        // ✅ OPEN
        alertDiv.classList.add('alert-success');
        alertDiv.classList.remove('alert-danger');
        statusSpan.textContent = 'सुरू';
    } else {
        // ❌ CLOSED
        alertDiv.classList.add('alert-danger');
        alertDiv.classList.remove('alert-success');
        statusSpan.textContent = 'बंद';
    }
});
</script>

<!-- Navbar End -->