@extends('website.layout')

@section('title', 'My Orders')

@section('content')

<style>
    .table td,
    .table th {
        padding: 6px 8px;
    }

    .card-body {
        font-size: 13px;
    }

    .address-card {
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .address-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .profile-card {
        background: #fff;
        transition: all 0.3s ease;
    }

    .profile-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .avatar-placeholder {
        width: 90px;
        height: 90px;
        background: #f1f1f1;
    }
</style>

<div class="container py-3" style="margin-top:160px;">
    <div class="row g-3">

        <!-- LEFT SIDEBAR (SMALL) -->
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                <!-- MENU -->
                <ul class="list-group list-group-flush small">
                    <li>
                        <a href="{{ route('my_orders', ['tab' => 'orders']) }}"
                            class="dropdown-item py-2 {{ $tab == 'orders' ? 'active' : '' }}">
                            <i class="bi bi-bag me-2"></i> My Orders
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('my_orders', ['tab' => 'address']) }}"
                            class="dropdown-item py-2 {{ $tab == 'address' ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt me-2"></i> My Address
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('my_orders', ['tab' => 'profile']) }}"
                            class="dropdown-item py-2 {{ $tab == 'profile' ? 'active' : '' }}">
                            <i class="bi bi-file-text me-2"></i> My Profile
                        </a>
                    </li>

                    <li>
                        <form method="POST" action="{{ route('websitelogout') }}">
                            @csrf
                            <button class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>

                <!-- STATIC IMAGE -->
                <div class="p-3 text-center border-top bg-light">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVCTafSjCVi4fd95H6c-G0IpO6B4t5ZpmHTA&s}"
                        alt="Sidebar Banner"
                        class="img-fluid rounded-3">
                </div>

            </div>
        </div>


        <!-- RIGHT CONTENT -->
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">

                    {{-- ================= ORDERS TAB ================= --}}
                    @if($tab == 'orders')

                    <h6 class="fw-bold text-primary mb-3">My Orders</h6>

                    @if($orders->count() > 0)
                    @foreach($orders as $order)
                    <div class="border rounded-2 p-2 mb-3">

                        <!-- Order Header -->
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span>
                                Order #: <strong class="text-primary">{{ $order->order_number }}</strong>
                            </span>

                            @php
                            $statusClass = match($order->status) {
                            'pending' => 'badge bg-warning',
                            'completed' => 'badge bg-success',
                            'cancelled' => 'badge bg-danger',
                            default => 'badge bg-secondary',
                            };
                            @endphp

                            <span class="{{ $statusClass }} small">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Order Items -->
                        <div class="table-responsive">
                            <table class="table table-sm mb-1 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end fw-semibold">
                                            ₹{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div class="d-flex justify-content-end gap-3 small">
                            <span>Subtotal:
                                <strong>₹{{ number_format($order->subtotal, 2) }}</strong>
                            </span>
                            <span class="fw-bold text-primary">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <img src="{{ asset('images/779d7fb9-bf82-4920-a9d8-7c001ac12330.png') }}"
                            style="max-width:160px" class="mb-3">
                        <p class="fw-semibold mb-1 small">
                            Oops! You haven’t placed any orders yet
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                            Shop Now
                        </a>
                    </div>
                    @endif

                    @endif
                    {{-- ================= END ORDERS ================= --}}


                    {{-- ================= ADDRESS TAB ================= --}}
                    @if($tab == 'address')

                    <h6 class="fw-bold text-primary mb-3">My Addresses</h6>

                    @if($addresses->count())
                    @foreach($addresses as $address)
                    <div class="address-card border rounded-4 p-3 p-md-4 mb-3 shadow-sm">

                        <!-- Name + Default Badge -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2">
                            <h6 class="fw-semibold mb-1 mb-md-0">
                                {{ $address->first_name }} {{ $address->last_name }}
                            </h6>

                            @if($address->is_default)
                            <span class="badge bg-success px-3 py-1 mt-1 mt-md-0">
                                Default
                            </span>
                            @endif
                        </div>

                        <!-- Address -->
                        <p class="mb-2 text-muted">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $address->address }},
                            {{ $address->city }} - {{ $address->postcode }},
                            {{ $address->country }}
                        </p>

                        <!-- Contact -->
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <span>
                                <i class="bi bi-telephone me-1"></i> {{ $address->phone }}
                            </span>
                            <span class="d-none d-md-inline">|</span>
                            <span>
                                <i class="bi bi-envelope me-1"></i> {{ $address->email }}
                            </span>
                        </div>

                    </div>


                    @endforeach
                    @else
                    <p class="text-muted small">No address found.</p>
                    @endif

                    @endif
                    {{-- ================= END ADDRESS ================= --}}

                    @if($tab == 'profile')
                    <h6 class="fw-bold text-primary mb-3">My Profile</h6>

                    <div class="profile-card border rounded-4 p-3 p-md-4 shadow-sm">

                        <!-- Profile Image & Name -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/profile_photos/' . Auth::user()->profile_photo) }}"
                                class="rounded-circle border"
                                width="90"
                                height="90"
                                style="object-fit: cover;">
                            @else
                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-person fs-1 text-muted"></i>
                            </div>
                            @endif

                            <div>
                                <h5 class="mb-1 fw-semibold">
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                </h5>
                                <span class="text-muted small">User Profile</span>
                            </div>
                        </div>

                        <hr>

                        <!-- User Details -->
                        <div class="row g-2 small">
                            <div class="col-12 col-md-6">
                                <strong>Email:</strong><br>
                                <span class="text-muted">{{ Auth::user()->email }}</span>
                            </div>

                            <div class="col-12 col-md-6">
                                <strong>Mobile:</strong><br>
                                <span class="text-muted">{{ Auth::user()->mobile }}</span>
                            </div>
                        </div>

                    </div>

                    @endif


                </div>
            </div>
        </div>


    </div>
</div>
@endsection