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
</style>
<div class="container py-3" style="margin-top:160px;">
    <div class="row g-3">

        <!-- LEFT SIDEBAR (SMALL) -->
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
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

                    <li class="dropdown-item py-2">
                        <i class="bi bi-gift me-2"></i> E-Gift Cards
                    </li>
                    <li class="dropdown-item py-2">
                        <i class="bi bi-shield-lock me-2"></i> Account Privacy
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
                    <div class="border rounded-3 p-3 mb-3">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold fs-5">
                                {{ $address->first_name }} {{ $address->last_name }}
                            </span>

                            @if($address->is_default)
                            <span class="badge bg-success px-2">Default</span>
                            @endif
                        </div>

                        <p class="mb-1 fs-6 text-muted">
                            {{ $address->address }},
                            {{ $address->city }} - {{ $address->postcode }},
                            {{ $address->country }}
                        </p>

                        <p class="mb-0 fs-6">
                            📞 {{ $address->phone }} &nbsp; | &nbsp; ✉ {{ $address->email }}
                        </p>

                    </div>

                    @endforeach
                    @else
                    <p class="text-muted small">No address found.</p>
                    @endif

                    @endif
                    {{-- ================= END ADDRESS ================= --}}

                    @if($tab == 'profile')
                    <h6 class="fw-bold text-primary mb-3">My Profile</h6>

                    <div class="border rounded-3 p-3">
                        <p><strong>Name:</strong> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        <p><strong>Mobile:</strong> {{ Auth::user()->phone }}</p>
                    </div>
                    @endif


                </div>
            </div>
        </div>


    </div>
</div>
@endsection