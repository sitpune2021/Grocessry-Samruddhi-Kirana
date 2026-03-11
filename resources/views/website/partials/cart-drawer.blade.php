 <!-- RIGHT SIDE CART DRAWER -->
 
 <div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer">
     <div class="offcanvas-header border-bottom">
         <h5 class="fw-bold m-0">My Cart</h5>
         <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
     </div>
     <div class="offcanvas-body p-0 d-flex flex-column">

         <div id="cartDrawerItems">
             <!-- SCROLL AREA -->
             <div class="cart-scroll-area flex-grow-1">
                 @if(!empty($globalCart) && $globalCart->items->isNotEmpty())
                 @foreach($globalCart->items as $item)
                 @php
                 $images = $item->product->product_images ?? [];
                 $firstImage = is_array($images) ? ($images[0] ?? null) : null;
                 @endphp
                 <div class="cart-item-box">
                     <div class="d-flex align-items-center">

                         <!-- Image -->
                         <img src="{{ $firstImage 
                                ? asset('storage/products/'.$firstImage) 
                                : asset('website/img/no-image.png') }}"
                             class="cart-img">

                         <!-- Details -->
                         <div class="flex-grow-1 ms-3">
                             <div class="fw-semibold product-title">
                                 {{ $item->product->name }}
                             </div>

                             <div class="fw-bold mt-1">
                                 ₹ {{ number_format($item->price,2) }}
                             </div>
                         </div>
                         @include('website.partials.add-to-cart-btn', [
                         'product' => $item->product,
                         'cartItems' => $globalCart->items->keyBy('product_id')
                         ])
                        
                     </div>

                     <!-- Item Total -->
                     <div class="d-flex justify-content-between mt-2 small">
                         <span class="text-muted">Item Total</span>
                         <strong id="item-total-{{ $item->id }}">
                             ₹ {{ number_format($item->line_total,2) }}
                         </strong>
                     </div>
                 </div>
                 @endforeach
                 <!-- BILL DETAILS -->
                 <div class="bill-box">

                     <h6 class="bill-title">Bill Details</h6>

                     <div class="bill-row">
                         <div class="bill-left">
                             <i class="ri-shopping-bag-3-line"></i>
                             <span class="bill-amount">Items total</span>
                         </div>
                         <span id="cart-subtotal" class="bill-amount">
                             ₹ {{ number_format($globalCart->subtotal ?? 0,2) }}
                         </span>
                     </div>

                     <div class="bill-row">
                         <div class="bill-left">
                             <i class="ri-truck-line"></i>
                             <span class="bill-amount">Delivery charge</span>
                         </div>
                         <span class="text-success bill-free">FREE</span>
                     </div>

                     <div class="bill-row">
                         <div class="bill-left">
                             <i class="ri-hand-coin-line"></i>
                             <span class="bill-amount">Handling charge</span>
                         </div>
                         <span class="text-success bill-free">FREE</span>
                     </div>

                     <hr class="bill-divider">

                     <div class="bill-row total-row">
                         <span>Grand total</span>
                         <span id="cart-total" class="grand-total">
                             ₹ {{ number_format($globalCart->total ?? 0,2) }}
                         </span>
                     </div>
                 </div>
                 @else
                 <div class="text-center p-5">
                     <p>Your cart is empty</p>
                 </div>
                 @endif
             </div>

             <!-- STICKY BOTTOM BAR -->
             @if(!empty($globalCart) && $globalCart->items->isNotEmpty())

             <div class="cart-bottom-bar">
                 <div>
                     <div class="fw-bold">
                         ₹ {{ number_format($globalCart->total ?? 0,2) }}
                     </div>
                     <small>Total</small>
                 </div>

                 <a href="{{ route('checkout') }}" class="proceed-btn">
                     Proceed →
                 </a>
             </div>

             @endif
         </div>
     </div>

     <script>
        function refreshCartDrawer() {

    fetch("/cart/drawer")
        .then(res => res.text())
        .then(html => {

            document.getElementById("cartDrawerItems").innerHTML = html;

        });

}
     </script>