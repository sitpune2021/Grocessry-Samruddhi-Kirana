@extends('website.layout')

@section('title', 'My Account')

@section('content')

<style>
    body {
        background: #f5f7fa;
    }

    .account-wrapper {
        margin-top: 150px;
        margin-bottom: 60px;
    }

    /* Sidebar */
    .account-sidebar {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .account-sidebar .list-group-item {
        border: none;
        padding: 14px 20px;
        font-weight: 500;
        font-size: 14px;
        color: #444;
        transition: 0.2s ease;
    }

    .account-sidebar .list-group-item:hover {
        background: #f0fff4;
        color: #0aad0a;
    }

    .account-sidebar .active {
        background: #e6f9ed;
        color: #0aad0a;
        font-weight: 600;
        border-left: 4px solid #0aad0a;
    }

    /* Main Card */
    .account-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    /* Order Card */
    .order-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #f0f0f0;
        margin-bottom: 20px;
        transition: 0.2s;
    }

    .order-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .price-highlight {
        color: #0aad0a;
        font-weight: 600;
    }

    .address-card {
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        padding: 20px;
        background: #fff;
        margin-bottom: 20px;
        transition: 0.2s;
    }

    .address-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }

    .profile-card {
        border-radius: 14px;
        padding: 30px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #e6f9ed;
    }

    h5,
    h6 {
        font-weight: 600;
    }

    .table th {
        font-size: 13px;
        background: #fafafa;
    }

    .table td {
        font-size: 13px;
    }
</style>

<div class="container account-wrapper p-5">
    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <div class="account-sidebar">
                <ul class="list-group list-group-flush">

                    <a href="{{ route('my_orders', ['tab' => 'orders']) }}"
                        class="list-group-item {{ $tab == 'orders' ? 'active' : '' }}">
                        <i class="bi bi-bag me-2"></i> My Orders
                    </a>

                    <a href="{{ route('my_orders', ['tab' => 'address']) }}"
                        class="list-group-item {{ $tab == 'address' ? 'active' : '' }}">
                        <i class="bi bi-geo-alt me-2"></i> My Address
                    </a>

                    <a href="{{ route('my_orders', ['tab' => 'profile']) }}"
                        class="list-group-item {{ $tab == 'profile' ? 'active' : '' }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>

                    <form method="POST" action="{{ route('websitelogout') }}">
                        @csrf
                        <button class="list-group-item text-danger w-100 text-start border-0 bg-white">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>

                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="account-card">

                {{-- ORDERS TAB --}}
                @if($tab == 'orders')

                <h5 class="text-success mb-4">My Orders</h5>

                @forelse($orders as $order)

                <div class="order-card">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            Order #: <strong>{{ $order->order_number }}</strong>
                        </div>

                        @php
                        $statusClass = match($order->status) {
                        'pending' => 'status-pending',
                        'completed' => 'status-completed',
                        'cancelled' => 'status-cancelled',
                        default => 'bg-primary'
                        };
                        @endphp

                        <span class="badge-status {{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
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
                                    <td class="text-end">₹{{ number_format($item->price,2) }}</td>
                                    <td class="text-end fw-semibold">
                                        ₹{{ number_format($item->total,2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end border-top pt-3">
                        <div>
                            Total:
                            <span class="price-highlight">
                                ₹{{ number_format($order->total_amount,2) }}
                            </span>
                        </div>
                    </div>
                </div>

                @empty
                <div class="text-center py-5">
                    <h6 class="mb-3">No Orders Found</h6>
                    <a href="{{ route('home') }}" class="btn btn-success rounded-pill px-4">
                        Start Shopping
                    </a>
                </div>
                @endforelse

                @endif

                @if($tab == 'orders' && $orders->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
                @endif
                {{-- ADDRESS TAB --}}
                @if($tab == 'address')

                <h5 class="text-success mb-4">My Addresses</h5>

                @forelse($addresses as $address)
                <div class="address-card">
                    <h6>{{ $address->first_name }} {{ $address->last_name }}</h6>
                    <p class="mb-1">
                        {{ $address->flat_house }}, {{ $address->area }} ,
                        Floor: {{ $address->floor }}, {{ $address->landmark }}<br>
                        {{ $address->city }} - {{ $address->postcode }}
                    </p>
                    <p class="mb-0">
                        {{ $address->phone }} 
                    </p>
                </div>
                @empty
                <p>No address found.</p>
                @endforelse

                @endif


                {{-- PROFILE TAB --}}
                @if($tab == 'profile')

                <h5 class="text-success mb-4">My Profile</h5>

                <div class="profile-card text-center text-md-start">
                    <div class="row align-items-center">

                        <div class="col-md-auto mb-3 mb-md-0 text-center">
                            @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                                class="profile-avatar">
                            @else
                            <img src="https://via.placeholder.com/120"
                                class="profile-avatar">
                            @endif
                        </div>

                        <div class="col-md">
                            <h4>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
                            <p class="mb-1 text-muted">{{ Auth::user()->email }}</p>
                            <p class="text-muted">{{ Auth::user()->mobile }}</p>
                        </div>

                    </div>
                </div>

                @endif

            </div>
        </div>

    </div>
</div>


<style>
    .pagination {
        gap: 6px;
    }

    .page-link {
        border-radius: 8px !important;
        border: 1px solid #e0e0e0;
        color: #0aad0a;
        font-weight: 500;
    }

    .page-link:hover {
        background: #e6f9ed;
        color: #0aad0a;
    }

    .page-item.active .page-link {
        background: #0aad0a;
        border-color: #0aad0a;
        color: #fff;
    }
</style>

@endsection