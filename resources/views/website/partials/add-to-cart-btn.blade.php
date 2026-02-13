@php
$cartQty = $cartItems[$product->id]->qty ?? 0;
@endphp

<form action="{{ route('add_cart') }}" method="POST" class="add-cart-form">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="qty" value="1">

    {{--Warehouse not selected --}}
    @if(!session('dc_warehouse_id'))
    <button type="button" class="btn btn-availability" disabled>
        Check Availability
    </button>

    {{-- In Stock --}}
    @elseif(($product->available_stock ?? 0) > 0)

    <div class="qty-wrapper"
        data-product-id="{{ $product->id }}">

        {{-- ADD BUTTON --}}
        <button type="button"
            class="btn btn-add-active add-btn {{ $cartQty > 0 ? 'd-none' : '' }}"
            onclick="addToCartUI(this)">
            ADD
        </button>

        {{-- QTY CONTROLS --}}
        <div class="qty-box {{ $cartQty > 0 ? '' : 'd-none' }}">
            <button type="button" onclick="changeQty(this, -1)">−</button>

            <span class="qty">{{ $cartQty > 0 ? $cartQty : 1 }}</span>

            <button type="button" onclick="changeQty(this, 1)">+</button>
        </div>


    </div>

    {{-- Out of stock --}}
    @else
    <button type="button" class="btn btn-danger  btn-out-stock" disabled>
        Out of Stock
    </button>
    @endif
</form>


<style>
    .custom-alert {
        position: fixed;
        top: 80%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #545454;
        color: #fff;
        padding: 14px 25px;
        border-radius: 8px;
        z-index: 9999;
        font-weight: 500;
        font-size: 14px;
        box-shadow: 0 8px 25px rgba(105, 105, 105, 0.3);
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translate(-50%, -45%);
        }

        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    /* button css  */
    /* 🟢 ADD */
    .btn-add-active {
        background-color: #28a745;
        color: #fff;
        border: 1px solid #28a745;
        border-radius: 6px;
        height: 32px;
        width: 75px;
        /* SAME HEIGHT */
        padding: 0 16px;
        /* SAME LOOK */
        font-weight: 600;
    }


    .btn-add-active:hover {
        background-color: #fff;
        color: #067420;
    }

    /* ⚪ CHECK AVAILABILITY */
    .btn-availability {
        background: transparent;
        color: #6c757d;
        border: 1px dashed #ced4da;
        cursor: not-allowed;
        border-radius: 10px;
    }

    /* 🔴 OUT OF STOCK */
    .btn-out-stock {
        background-color: #ff001994;
        color: #fff;
        border: 1px solid #f1aeb5;
        cursor: not-allowed;
        border-radius: 10px;
    }

    .qty-box {
        display: inline-flex;
        align-items: center;
        border: 1px solid #28a745;
        border-radius: 6px;
        overflow: hidden;
    }

    .qty-box button {
        background-color: #28a745;
        color: #fff;
        border: none;
        width: 30px;
        height: 32px;
        font-size: 18px;
        font-weight: 600;
        padding: 0;
    }

    .qty-box button:hover {
        background-color: #1e7e34;
    }

    .qty-box .qty {
        background-color: #28a745;
        color: #fff;
        width: 15px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
</style>

<script>
    function addToCartUI(btn) {

        const form = btn.closest('form');
        const wrapper = btn.closest('.qty-wrapper');
        const qtyBox = wrapper.querySelector('.qty-box');
        const qtyInput = form.querySelector('input[name="qty"]');

        qtyInput.value = 1;

        let formData = new FormData(form);

        fetch(form.action, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {

                    btn.classList.add('d-none');
                    qtyBox.classList.remove('d-none');

                    wrapper.querySelector('.qty').innerText = data.qty;

                    updateCartIcon(data.cart_count);

                } else {
                    alert(data.message);
                }

            })
            .catch(err => {
                console.error(err);
            });
    }

    function changeQty(btn, delta) {

        const wrapper = btn.closest('.qty-wrapper');
        const form = btn.closest('form');
        const qtySpan = wrapper.querySelector('.qty');
        const qtyInput = form.querySelector('input[name="qty"]');

        let qty = parseInt(qtySpan.innerText);
        qty += delta;

        if (qty <= 0) {
            wrapper.querySelector('.add-btn').classList.remove('d-none');
            wrapper.querySelector('.qty-box').classList.add('d-none');
            return;
        }

        qtyInput.value = qty;

        let formData = new FormData(form);

        fetch(form.action, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {

                    qtySpan.innerText = data.qty;
                    updateCartIcon(data.cart_count);

                } else {
                    showCustomAlert(data.message);
                }

            });
    }

    function updateCartIcon(count) {

        const countEl = document.getElementById('cart-count');
        if (!countEl) return;

        if (count > 0) {
            countEl.innerText = count;
            countEl.style.display = 'flex';
        } else {
            countEl.innerText = 0;
            countEl.style.display = 'none';
        }
    }
</script>

<script>
    function addToCart(btn) {
        const form = btn.closest('form');

        // qty = 1
        const qtyInput = form.querySelector('input[name="qty"]');
        qtyInput.value = 1;

        // 🔥 form submit (backend hit)
        form.submit();
    }
</script>