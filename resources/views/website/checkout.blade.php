@extends('website.layout')

@section('title', 'Home')


@section('content')

    <body>
      

        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Checkout</h1>
        </div>
        <!-- Single Page Header End -->


        <!-- Checkout Page Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <h1 class="mb-4">Billing details</h1>
                <form action="/place-order" method="POST">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-12 col-lg-6 col-xl-7">

                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">First Name<sup>*</sup></label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $address->first_name ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Last Name<sup>*</sup></label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $address->last_name ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Address <sup>*</sup></label>
                                <input type="text" name="address" class="form-control" placeholder="House Number Street Name" value="{{ $address->address ?? '' }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Town/City<sup>*</sup></label>
                                <input type="text" name="city" class="form-control" value="{{ $address->city ?? '' }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Country<sup>*</sup></label>
                                <input type="text" name="country" class="form-control" value="{{ $address->country ?? '' }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Postcode/Zip<sup>*</sup></label>
                                <input type="text" name="postcode" class="form-control" value="{{ $address->postcode ?? '' }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Mobile<sup>*</sup></label>
                                <input type="tel" name="phone" class="form-control" value="{{ $address->phone ?? '' }}">
                            </div>
                            <div class="form-item">
                                <label class="form-label my-3">Email Address<sup>*</sup></label>
                                <input type="email" name="email" class="form-control" value="{{ $address->email ?? '' }}">
                            </div>

                        </div>

                        <div class="col-md-12 col-lg-6 col-xl-5">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Products</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
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
                                        <td>₹{{ $item->price }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>₹{{ $item->line_total }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                                        <td><strong>₹{{ $cart->subtotal }}</strong></td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                                        <td><strong>₹{{ $cart->total }}</strong></td>
                                    </tr>

                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">Your cart is empty</td>
                                    </tr>
                                    @endif
                                    </tbody>

                                </table>
                            </div>
                            
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input" name="payment_method" value="cod" id="cod">
                                        <label for="cod">Cash On Delivery</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input" name="payment_method" value="paypal" id="paypal">
                                        <label for="paypal">Paypal</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                                <button type="submit" class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">Place Order</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <!-- Checkout Page End -->

        
    </body>
