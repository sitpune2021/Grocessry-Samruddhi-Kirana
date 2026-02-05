{{-- ADD TO CART --}}
<form action="{{ route('add_cart') }}" method="POST" class="add-cart-form">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="qty" value="1">

    {{-- ❌ Warehouse not selected --}}
    @if(!session('dc_warehouse_id'))
    <button type="button" class="btn btn-availability" disabled>
        Check Availability
    </button>

    {{-- ✅ In Stock --}}
    @elseif(($product->available_stock ?? 0) > 0)

    <div class="qty-wrapper" data-product-id="{{ $product->id }}">

        {{-- ADD BUTTON --}}
        <button type="button"
            class="btn btn-add-active add-btn"
            onclick="addToCart(this)">
            ADD
        </button>


        {{-- QTY CONTROLS --}}
        <div class="qty-box d-none">
            <button type="button" onclick="changeQty(this, -1)">−</button>
            <span class="qty">1</span>
            <button type="button" onclick="changeQty(this, 1)">+</button>
        </div>

    </div>

    {{-- ❌ Out of stock --}}
    @else
    <button type="button" class="btn btn-out-stock" disabled>
        Out of Stock
    </button>
    @endif
</form>





<style>
    /* 🟢 ADD */
    .btn-add-active {
        background-color: #28a745;
        color: #fff;
        border: 1px solid #28a745;
        border-radius: 10px;
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

    /* QTY BOX */
    .qty-box {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-box button {
        width: 28px;
        height: 28px;
    }
</style>

<script>
    function addToCartUI(btn) {
        const wrapper = btn.closest('.qty-wrapper');
        const qtyBox = wrapper.querySelector('.qty-box');
        const qtyInput = wrapper.closest('form').querySelector('input[name="qty"]');

        btn.classList.add('d-none');
        qtyBox.classList.remove('d-none');

        qtyInput.value = 1;
        updateCartCount(1);
    }

    function changeQty(btn, delta) {
        const wrapper = btn.closest('.qty-wrapper');
        const qtySpan = wrapper.querySelector('.qty');
        const qtyInput = wrapper.closest('form').querySelector('input[name="qty"]');

        let qty = parseInt(qtySpan.innerText);
        qty += delta;

        if (qty <= 0) {
            wrapper.querySelector('.add-btn').classList.remove('d-none');
            wrapper.querySelector('.qty-box').classList.add('d-none');
            qtyInput.value = 1;
            updateCartCount(-1);
        } else {
            qtySpan.innerText = qty;
            qtyInput.value = qty;
            updateCartCount(delta);
        }
    }

    function updateCartCount(change) {
        const countEl = document.getElementById('cart-count');
        if (!countEl) return;

        let count = parseInt(countEl.innerText || 0) + change;

        if (count <= 0) {
            countEl.style.display = 'none';
            countEl.innerText = 0;
        } else {
            countEl.innerText = count;
            countEl.style.display = 'flex';
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