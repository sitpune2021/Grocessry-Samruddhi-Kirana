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
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }

        100% {
            opacity: 1;
        }
    }
</style>

<style>
    .blink {
        animation: blinkEffect 1s infinite;
    }

    @keyframes blinkEffect {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }

        100% {
            opacity: 1;
        }
    }
</style>

<style>
    .user-btn {
        color: #333;
        text-decoration: none;
    }

    .user-dropdown {
        min-width: 200px;
        border-radius: 10px;
    }

    .user-dropdown .dropdown-item {
        padding: 10px 15px;
        font-size: 15px;
    }

    .user-dropdown i {
        width: 20px;
    }

    .blink {
        animation: blink 1.4s infinite;
    }

    @keyframes blink {
        50% {
            opacity: .6;
        }
    }

    .nav-link.active {
        color: #0d6efd !important;
    }
</style>
<!-- Navbar start -->
<div class="container-fluid fixed-top">

    <!-- TOP LOCATION BAR -->
    <div class="container-fluid bg-primary text-white  " style="z-index:1050;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-1">

                <!-- Location -->
                <div class="d-flex align-items-center gap-2"
                    style="cursor:pointer"
                    data-bs-toggle="modal"
                    data-bs-target="#pincodeModal">

                    <i class="fas fa-map-marker-alt"></i>

                    @if(session('delivery_pincode'))
                    <span class="fw-semibold">
                        Delivering to {{ session('delivery_pincode') }}
                    </span>
                    <!-- <small class="opacity-75">(Change)</small> -->
                    @else
                    <span class="fw-semibold">Select delivery location</span>
                    @endif
                </div>

                <!-- Alert Message -->
                <div class="alert text-center m-0 py-1 blink" id="order-alert"
                    style="font-size: 13px; font-weight: bold; cursor:pointer;"
                    data-bs-toggle="modal" data-bs-target="#orderPopup">
                    Online orders <span id="order-status"></span> |
                    <span id="timer-text"></span>
                </div>

            </div>
        </div>
    </div>

    @if(!session('delivery_pincode'))
    <div class="modal" id="pincodeModal" style="display:block;background:rgba(0,0,0,.6)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Delivery Location</h6>
                </div>
                <div class="modal-body">
                    <input type="text" id="pincodeInput" class="form-control"
                        placeholder="Enter 6 digit pincode" maxlength="6">
                    <small class="text-danger d-none mt-2" id="pinError"></small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100" onclick="checkPincode()">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


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
                    <a href="{{ route('home') }}"
                        class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        Home
                    </a>

                    <a href="{{ route('shop') }}"
                        class="nav-item nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">
                        Shop
                    </a>

                    <a href="{{ route('contact') }}"
                        class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                        Contact
                    </a>
                </div>


                <div class="d-flex m-3 me-0">

                    <!-- Cart -->
                    <!-- <a href="{{ route('cart') }}" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-cart fa-2x"></i>

                        <span id="cart-count"
                            class="position-absolute bg-secondary rounded-circle d-flex align-items-center 
                            justify-content-center text-dark px-1"
                                            style="top:-5px; left:15px; height:20px; min-width:20px;
                            {{ $cartCount > 0 ? '' : 'display:none;' }}">
                            {{ $cartCount }}
                        </span>
                    </a> -->

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

                    <!-- User Account -->
                    @auth
                    <div class="dropdown">
                        <a href="#"
                            class="my-auto dropdown-toggle d-flex align-items-center user-btn"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fas fa-user-circle fs-3"></i>

                            <!-- Desktop name only -->
                            <span class="ms-2 d-none d-md-inline">
                                {{ Auth::user()->first_name }}
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow user-dropdown">

                            <li>
                                <a href="{{ route('my_orders', ['tab' => 'profile']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                    <i class="fas fa-id-card me-2 text-primary"></i>
                                    My Profile
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('my_orders') }}"
                                    class="dropdown-item d-flex align-items-center">
                                    <i class="fas fa-box me-2 text-success"></i>
                                    My Orders
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('websitelogout') }}">
                                    @csrf
                                    <button class="dropdown-item d-flex align-items-center text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>

                        </ul>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="my-auto d-flex align-items-center user-btn">
                        <i class="fas fa-user-circle fs-3"></i>
                    </a>
                    @endauth

                </div>

            </div>

        </nav>
    </div>

</div>



<script>
    document.addEventListener("DOMContentLoaded", function() {

        const alertDiv = document.getElementById('order-alert');
        const statusSpan = document.getElementById('order-status');
        const timerText = document.getElementById('timer-text');

        const popupStatus = document.getElementById('popup-status');
        const popupTimer = document.getElementById('popup-timer');

        function updateStatus() {

            const now = new Date();
            const hour = now.getHours();
            const minute = now.getMinutes();
            const second = now.getSeconds();

            const openTime = 6; // 6 AM
            const closeTime = 19; // 7 PM

            let targetTime;
            let status;

            if (hour >= openTime && hour < closeTime) {
                // OPEN
                status = "are OPEN 🟢";
                alertDiv.classList.add('alert-success');
                alertDiv.classList.remove('alert-danger');

                targetTime = new Date();
                targetTime.setHours(closeTime, 0, 0);

            } else {
                // CLOSED
                status = "are CLOSED 🔴";
                alertDiv.classList.add('alert-danger');
                alertDiv.classList.remove('alert-success');

                targetTime = new Date();
                if (hour >= closeTime) {
                    targetTime.setDate(targetTime.getDate() + 1);
                }
                targetTime.setHours(openTime, 0, 0);
            }

            const diff = targetTime - now;

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff / (1000 * 60)) % 60);
            const s = Math.floor((diff / 1000) % 60);

            const timeLeft = `For ${h}h ${m}m ${s}s`;

            statusSpan.textContent = status;
            timerText.textContent = timeLeft;

            popupStatus.textContent = "Online orders " + status;
            popupTimer.textContent = timeLeft;
        }

        updateStatus();
        setInterval(updateStatus, 1000);
    });
</script>

<script>
    function checkPincode() {
        let pin = document.getElementById('pincodeInput').value;
        let err = document.getElementById('pinError');

        if (pin.length !== 6) {
            err.classList.remove('d-none');
            err.innerText = 'Valid pincode enter kara';
            return;
        }

        fetch("{{ route('check.pincode') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    pincode: pin
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.status) {
                    err.classList.remove('d-none');
                    err.innerText = data.message;
                    return;
                }

                location.reload(); // 🔥 warehouse session set
            });
    }
</script>


<!-- Navbar End -->