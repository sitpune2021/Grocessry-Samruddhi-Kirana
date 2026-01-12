@extends('website.layout')

@section('title', 'Home')


@section('content')

    <body>

        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Cart</h1>
        </div>
        <!-- Single Page Header End -->

        <!-- Cart Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Products</th>
                            <th scope="col">Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Handle</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if($cart && $cart->items->count())
                                @foreach($cart->items as $item)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/products/'.$item->product->product_images[0]) }}"
                                            style="width:80px;height:80px;" class="rounded-circle">
                                    </td>

                                    <td>{{ $item->product->name }}</td>

                                    <td>₹ {{ $item->price }}</td>

                                    <td>{{ $item->qty }}</td>

                                    <td>₹ {{ $item->line_total }}</td>

                                    <td>
                                        <form action="{{ route('remove_cart_item', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center">Cart is empty</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- <div class="mt-5">
                    <input type="text" class="border-0 border-bottom rounded me-5 py-3 mb-4" placeholder="Coupon Code">
                    <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="button">Apply Coupon</button>
                </div> -->

                <div class="row g-4 justify-content-end">
                    <div class="col-8"></div>
                    <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                        <div class="bg-light rounded">

                            <div class="p-4">
                                <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                                
                                <div class="d-flex justify-content-between mb-4">
                                    <h5 class="mb-0 me-4">Subtotal:</h5>
                                    <p class="mb-0">
                                        ₹ {{ $cart ? number_format($cart->subtotal, 2) : '0.00' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                                <h5 class="mb-0 ps-4 me-4">Total</h5>
                                <p class="mb-0 pe-4">
                                    ₹ {{ $cart ? number_format($cart->total, 2) : '0.00' }}
                                </p>
                            </div>

                            <a href="{{ route('checkout') }}" 
                                class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4">
                                Proceed Checkout
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart Page End -->

    </body>