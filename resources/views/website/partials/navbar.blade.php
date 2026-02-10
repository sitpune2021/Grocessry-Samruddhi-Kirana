 <!-- Spinner Start -->
 <!-- <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div> -->
 <!-- Spinner End -->
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

     .address-card {
         cursor: pointer;
         transition: 0.2s ease;
     }

     .address-card:hover {
         border-color: #198754;
         background: #f6fffa;
     }
 </style>
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


 <!-- Navbar start -->
 <div class="container-fluid fixed-top">

     <!-- TOP LOCATION BAR -->
     <div class="container-fluid bg-primary text-white  " style="z-index:1050;">
         <div class="container">
             <div class="d-flex justify-content-between align-items-center py-1">

                 <!-- HEADER LOCATION -->
                 <div class="d-flex align-items-center gap-2"
                     style="cursor:pointer"
                     data-bs-toggle="modal"
                     data-bs-target="#locationModal">

                     <i class="fas fa-map-marker-alt text-success"></i>

                     @php
                     $area = session('delivery_address.area') ?? null;
                     $pincode = session('delivery_address.postcode') ?? session('delivery_pincode') ?? null;
                     @endphp

                     @if($area || $pincode)
                     <span class="fw-semibold" id="headerLocation">
                         Delivering to
                         {{ $area ?? 'Your location' }}{{ $pincode ? ', '.$pincode : '' }}
                     </span>
                     <small class="opacity-75 d-flex align-items-center gap-1">
                         Change <i class="fas fa-chevron-down small"></i>
                     </small>
                     @else
                     <span class="fw-semibold" id="headerLocation">
                         Select delivery location
                     </span>
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


 @php
 $addresses = auth()->check()
 ? \App\Models\UserAddress::where('user_id', auth()->id())
 ->orderByDesc('is_default')
 ->get()
 : collect();
 @endphp

 <div class="modal fade" id="locationModal" tabindex="-1">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content rounded-4">

             <div class="modal-header">
                 <h5 class="modal-title">Change delivery location</h5>
                 <button class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <div class="modal-body">

                 <!-- PINCODE -->
                 <div class="d-flex gap-2 mb-3">
                     <input id="modalPincode" class="form-control"
                         maxlength="6" placeholder="Enter pincode">
                     <button class="btn btn-success" onclick="changePincode()">Change</button>
                 </div>

                 <hr>

                 <button class="btn btn-outline-success mb-3"
                     onclick="billingAddressModal()">+ Add new address</button>

                 <h6 class="mb-3">Your saved addresses</h6>

                 @foreach($addresses as $addr)
                 <div class="address-card border rounded-3 p-3 mb-2
                    {{ session('delivery_address.id') == $addr->id ? 'border-success bg-light' : '' }}">

                     <div class="form-check">
                         <input class="form-check-input address-radio"
                             type="radio"
                             name="selected_address"
                             value="{{ $addr->id }}"
                             {{ ($addr->is_default || session('delivery_address.id') == $addr->id) ? 'checked' : '' }}
                             onchange="selectAddress({{ $addr->id }})">

                         <label class="form-check-label w-100 ps-2"
                             style="cursor:pointer">

                             <b>{{ ['1'=>'Home','2'=>'Work','3'=>'Other'][$addr->type] }}</b>

                             @if($addr->is_default)
                             <span class="badge bg-success ms-1">Default</span>
                             @endif

                             <div class="text-muted small mt-1">
                                 {{ $addr->flat_house }},
                                 {{ $addr->area }},
                                 {{ $addr->city }} - {{ $addr->postcode }}
                             </div>

                         </label>
                     </div>
                 </div>
                 @endforeach

             </div>
         </div>
     </div>
 </div>



 <div class="modal fade" id="billingAddressModal" tabindex="-1">
     <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
         <div class="modal-content rounded-4">

             <div class="modal-header">
                 <h5 class="modal-title">Billing Details</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <div class="modal-body">
                 <div class="card shadow-sm border-0 rounded-4">
                     <div class="card-body p-4">
                         <form id="addressForm" action="{{ url('/place-order') }}" method="POST">
                             @csrf
                             <label class="fw-semibold d-block mb-2">Saved Addresses *</label>

                             <input type="hidden" name="type" id="address_type" value="1">
                             <input type="hidden" name="coupon_code" id="applied_coupon">
                             <input type="hidden" name="coupon_discount" id="coupon_discount">
                             <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                             <input type="hidden" name="razorpay_amount" id="razorpay_amount">
                             <input type="hidden" name="address_id" id="address_id">
                             <input type="hidden" name="final_total" id="final_total_input">

                             <div class="d-flex gap-2 mb-3" id="addressTabs">
                                 <button type="button" class="btn btn-outline-success address-tab" data-type="1">🏠 Home</button>
                                 <button type="button" class="btn btn-outline-primary address-tab" data-type="2">🏢 Work</button>
                                 <button type="button" class="btn btn-outline-warning address-tab" data-type="3">📍 Other</button>
                             </div>

                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <div class="floating-group">
                                         <input type="text" name="first_name" class="floating-input"
                                             placeholder=" "
                                             value="{{ old('first_name', $address->first_name ?? '') }}" required>
                                         <span class="floating-placeholder">First Name *</span>
                                     </div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <div class="floating-group">
                                         <input type="text" name="last_name" class="floating-input"
                                             placeholder=" "
                                             value="{{ old('last_name', $address->last_name ?? '') }}" required>
                                         <span class="floating-placeholder">Last Name *</span>
                                     </div>
                                 </div>
                             </div>

                             <div class="mb-3">
                                 <div class="floating-group">
                                     <input type="text" name="flat_house" class="floating-input"
                                         placeholder=" "
                                         value="{{ old('flat_house', $address->flat_house ?? '') }}" required>
                                     <span class="floating-placeholder">Flat / House no / Building *</span>
                                 </div>
                             </div>

                             <div class="mb-3">
                                 <div class="floating-group">
                                     <input type="text" name="floor" class="floating-input"
                                         placeholder=" "
                                         value="{{ old('floor', $address->floor ?? '') }}">
                                     <span class="floating-placeholder">Floor (optional)</span>
                                 </div>
                             </div>

                             <div class="mb-3">
                                 <div class="floating-group">
                                     <input type="text" name="area" class="floating-input"
                                         placeholder=" "
                                         value="{{ old('area', $address->area ?? '') }}" required>
                                     <span class="floating-placeholder">Area / Sector / Locality *</span>
                                 </div>
                             </div>

                             <div class="mb-3">
                                 <div class="floating-group">
                                     <input type="text" name="landmark" class="floating-input"
                                         placeholder=" "
                                         value="{{ old('landmark', $address->landmark ?? '') }}">
                                     <span class="floating-placeholder">Nearby Landmark</span>
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <div class="floating-group">
                                         <input type="text" name="city" class="floating-input"
                                             placeholder=" "
                                             value="{{ old('city', $address->city ?? '') }}" required>
                                         <span class="floating-placeholder">City *</span>
                                     </div>
                                 </div>

                                 <div class="col-md-6 mb-3">
                                     <div class="floating-group">
                                         <input type="text"
                                             name="postcode"
                                             id="pincode"
                                             class="floating-input"
                                             maxlength="6"
                                             value="{{ session('delivery_pincode') }}"
                                             required>
                                     </div>
                                 </div>
                             </div>

                             <div class="mb-3">
                                 <div class="floating-group">
                                     <input type="text" name="phone" class="floating-input"
                                         placeholder=" "
                                         maxlength="10"
                                         value="{{ old('phone', $address->phone ?? '') }}" required>
                                     <span class="floating-placeholder">Mobile *</span>
                                 </div>
                             </div>

                             <div class="modal-footer">
                                 <button type="button" class="btn btn-success w-100" onclick="saveAddress()">
                                     Save Address
                                 </button>

                             </div>

                         </form>
                     </div>
                 </div>
             </div>

         </div>
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
                 status = "are CLOSED ";
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
     function saveAddress() {

         let form = document.getElementById('addressForm');
         let formData = new FormData(form);

         fetch("{{ route('save.address') }}", {
                 method: "POST",
                 headers: {
                     "X-CSRF-TOKEN": "{{ csrf_token() }}"
                 },
                 body: formData
             })
             .then(async res => {
                 if (!res.ok) {
                     let err = await res.json();
                     alert(Object.values(err.errors)[0][0]);
                     throw err;
                 }
                 return res.json();
             })
             .then(data => {
                 if (!data.status) {
                     alert('Failed to save address');
                     return;
                 }

                 bootstrap.Modal.getInstance(
                     document.getElementById('billingAddressModal')
                 ).hide();

                 location.reload();
             })
             .catch(err => console.error(err));
     }
 </script>


 <script>
     function editAddress(e, id) {
         e.stopPropagation();

         fetch(`/get-address/${id}`)
             .then(res => res.json())
             .then(data => {

                 // hidden field for update
                 document.querySelector('[name=address_id]').value = data.id;

                 document.querySelector('[name=first_name]').value = data.first_name;
                 document.querySelector('[name=last_name]').value = data.last_name;
                 document.querySelector('[name=flat_house]').value = data.flat_house;
                 document.querySelector('[name=floor]').value = data.floor ?? '';
                 document.querySelector('[name=area]').value = data.area;
                 document.querySelector('[name=landmark]').value = data.landmark ?? '';
                 document.querySelector('[name=city]').value = data.city;
                 document.querySelector('[name=postcode]').value = data.postcode;
                 document.querySelector('[name=phone]').value = data.phone;

                 // type set
                 document.getElementById('address_type').value = data.type;

                 document.querySelectorAll('.address-tab').forEach(btn => {
                     btn.classList.toggle('active', btn.dataset.type == data.type);
                 });

                 new bootstrap.Modal(
                     document.getElementById('billingAddressModal')
                 ).show();
             });
     }
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

         setPincode(pin);
     }

     function setPincode(pin) {
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
                     document.getElementById('pinError').classList.remove('d-none');
                     document.getElementById('pinError').innerText = data.message;
                     return;
                 }
                 location.reload();
             });
     }
 </script>


 <script>
     function billingAddressModal() {
         let modalEl = document.getElementById('billingAddressModal');
         let modal = new bootstrap.Modal(modalEl);
         modal.show();
     }


     document.querySelectorAll('.address-tab').forEach(btn => {
         btn.addEventListener('click', function() {
             document.getElementById('address_type').value = this.dataset.type;

             document.querySelectorAll('.address-tab').forEach(b =>
                 b.classList.remove('active')
             );
             this.classList.add('active');
         });
     });
 </script>