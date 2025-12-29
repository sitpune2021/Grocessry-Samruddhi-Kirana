@include('layouts.header')

<body>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.navbar')
                <!-- / Navbar -->

                    <!-- Content wrapper -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="w-full">
                            <div class="card mb-4">
                                <h4 class="card-header text-center">
                                    purchase order
                                </h4>

                               <div class="card-body">

                                    <!-- GRID 12 -->
                                    <div class="row">

                                        <!-- LEFT : PRODUCTS (8) -->
                                        <div class="col-12 col-md-8">

                                            <div class="row">
                                                <!-- All Button -->
                                                <div class="mb-4 col-md-3 mt-3">
                                                    <button type="button"
                                                        class="p-2 text-white bg-success border-none rounded bg-blue-600 text-white rounded"
                                                        onclick="loadAllProducts()">
                                                        All Product
                                                    </button>
                                                </div>

                                                <!-- Filters -->
                                                <div class="col-md-8  mt-3 row gap-4 mb-4">
                                                    <div class="col-7">
                                                    <select id="category" class="border p-2 rounded w-full">
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-2">
                                                    <select id="sub_category" class="border p-2 rounded w-full">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>                                       
                                            </div>
                                                
                                        </div>

                                            <div class="mb-3 row">
                                                <div class="col-md-6 mt-3">
                                              <span class="text-bold font-bold fw-bolder">All Listed Products</span></div>
                                              <div class="col-md-6 text-end">
                                                <input type="text" id="search"
                                                        placeholder="Search..."
                                                        class="border p-2 rounded w-full"></div>
                                            </div>
                                          
                                            <!-- Products -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Product</th>
                                                                <th width="120">Price (₹)</th>
                                                                <th width="100">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="products-area">
                                                            <!-- products will load here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="mt-3 text-center">
                                                <ul id="pagination" class="pagination justify-content-center"></ul>
                                            </div>

                                        </div>

                                        <!-- RIGHT : BILLING (4) -->
                                        <div class="col-12 col-md-4">

                                            @if ($errors->any())
                                                <div class="alert alert-danger mb-2">
                                                    {{ $errors->first() }}
                                                </div>
                                            @endif

                                            <form id="purchaseOrderForm"
                                                method="POST"
                                                action="{{ url('/purchase-orders/store') }}">
                                                @csrf

                                                <div class="border p-4 rounded bg-white shadow sticky top-20">

                                                    <h3 class="font-bold mb-3 text-lg">
                                                        Billing Section
                                                    </h3>

                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr class="bg-gray-200 p-3">
                                                                <th style="font-weight: 800;">Item</th>
                                                                <th style="font-weight: 800;">Qty</th>
                                                                <th style="font-weight: 800;">Price</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="bill-items"></tbody>
                                                    </table>

                                                    <div class="text-right space-y-2 mt-5">
                                                        <p style="font-weight: 800;">Subtotal: ₹ <span id="subtotal">0</span></p>
                                                        <p style="font-weight: 800;">Tax: ₹ <span id="tax">0</span></p>

                                                        <p style="font-weight: 800;">Shipping</p>
                                                        <p>
                                                            <input type="number" id="shipping"
                                                                class="border w-24 text-right p-1"
                                                                value="0">
                                                        </p>

                                                        <p style="font-weight: 800;">Discount</p>
                                                        <p>
                                                            <input type="number" id="discount"
                                                                class="border w-24 text-right p-1"
                                                                value="0">
                                                        </p>

                                                        <h3 class="font-bold text-lg">
                                                            Grand Total ₹ <span id="grand-total">0</span>
                                                        </h3>
                                                    </div>

                                                    <!-- hidden inputs -->
                                                    <input type="hidden" name="subtotal" id="subtotal_input">
                                                    <input type="hidden" name="tax" id="tax_input">
                                                    <input type="hidden" name="shipping_charge" id="shipping_input">
                                                    <input type="hidden" name="discount" id="discount_input">
                                                    <input type="hidden" name="grand_total" id="grand_total_input">
                                                    <input type="hidden" name="items" id="items_input">

                                                    <!-- <button
                                                        class="bg-green-600 text-white bg-success border-none rounded w-full py-2 mt-4 rounded">
                                                        Save Purchase Order
                                                    </button> -->
                                                    <button
                                                        type="button"
                                                        onclick="submitOrder()"
                                                        class="bg-green-600 text-white bg-success border-none rounded w-full py-2 mt-4 rounded">
                                                        Save Purchase Order
                                                    </button>

                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                
                                </div>

                            </div>
                        </div>
                    </div>

                
            </div>
            <!-- / Layout page -->
        </div>
    </div>

    <!-- / Layout wrapper -->
</body>


<!-- category -> sub category wise product load  -->
<script>
let cart = {};
let productStock = {};   

// limit QTY.
function addProductWithLimit(id, name, price) {

    let currentQty = cart[id] ? cart[id].qty : 0;
    let maxQty = productStock[id];

    if (currentQty + 1 > maxQty) {
        alert(`❌ Cannot add more than ${maxQty} quantity`);
        return;
    }

    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = {
            product_id: id,
            name,
            price,
            qty: 1
        };
    }

    renderCart();
}


document.getElementById('category').addEventListener('change', function () {
    fetch(`/po/subcategories/${this.value}`)
        .then(res => res.json())
        .then(data => {
            let sub = document.getElementById('sub_category');
            sub.innerHTML = '<option value="">Select Sub Category</option>';
            data.forEach(sc => {
                sub.innerHTML += `<option value="${sc.id}">${sc.name}</option>`;
            });
        });
});

document.getElementById('sub_category').addEventListener('change', function () {

    let subCategoryId = this.value;

    if (!subCategoryId) {
        document.getElementById('products-area').innerHTML = '';
        return;
    }

    fetch(`/po/products/${subCategoryId}`)
        .then(res => res.json())
        .then(data => {

            console.log('Sub category products:', data); // 🔍 DEBUG

            let area = document.getElementById('products-area');
            area.innerHTML = '';

            if (data.length === 0) {
                area.innerHTML = `<p class="text-red-500">No products found</p>`;
                return;
            }

            // data.forEach(p => {
            //     area.innerHTML += `
            //         <div class="border p-2 cursor-pointer hover:bg-gray-100"
            //             onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.base_price})">
            //             <strong>${p.name}</strong><br>
            //             ₹${p.base_price}
            //         </div>`;
            // });
            data.forEach(p => {
                area.innerHTML += `
                    <tr style="border-bottom:1px solid black;">
                        <td>${p.name}</td>
                        <td>₹${p.base_price}</td>
                        <td>
                            <button class="btn btn-sm btn-primary text-white bg-success border-none rounded"
                                onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.base_price})">
                                Add 
                            </button>
                        </td>
                    </tr>`;
            });
        })
        .catch(err => console.error(err));
});


// function addToCart(id, name, price) {
//     if (cart[id]) {
//         cart[id].qty++;
//     } else {
//         cart[id] = { product_id: id, name, price, qty: 1 };
//     }
//     renderCart();
// }

function addToCart(id, name, price) {

    // If stock not loaded → fetch first
    if (!productStock[id]) {
        fetch(`/po/product-available-qty/${id}`)
            .then(res => res.json())
            .then(data => {
                productStock[id] = data.available_qty;

                if (data.available_qty <= 0) {
                    alert('❌ Product out of stock');
                    return;
                }

                addProductWithLimit(id, name, price);
            });
    } else {
        addProductWithLimit(id, name, price);
    }
}


// function changeQty(id, type) {
//     if (type === 'plus') cart[id].qty++;
//     else cart[id].qty--;

//     if (cart[id].qty <= 0) delete cart[id];
//     renderCart();
// }
function changeQty(id, type) {

    let maxQty = productStock[id];

    if (type === 'plus') {
        if (cart[id].qty + 1 > maxQty) {
            alert(`❌ Max allowed qty is ${maxQty}`);
            return;
        }
        cart[id].qty++;
    } else {
        cart[id].qty--;
    }

    if (cart[id].qty <= 0) {
        delete cart[id];
    }

    renderCart();
}


function renderCart() {
    let tbody = document.getElementById('bill-items');
    tbody.innerHTML = '';

    let subtotal = 0;

    Object.values(cart).forEach(item => {
        subtotal += item.qty * item.price;
        tbody.innerHTML += `
            <tr style="border-bottom:1px solid black;">
                <td>${item.name}</td>
                <td>
                    <button type="button" onclick="changeQty(${item.product_id},'minus')">-</button>
                    ${item.qty}
                    <button type="button" onclick="changeQty(${item.product_id},'plus')">+</button>
                </td>
                <td>₹${item.price}</td>
                <td><button type="button" onclick="delete cart[${item.product_id}]; renderCart()">X</button></td>
            </tr>`;
    });

    let tax = 0;
    let shipping = Number(document.getElementById('shipping').value);
    let discount = Number(document.getElementById('discount').value);
    let grand = subtotal + tax + shipping - discount;

    document.getElementById('subtotal').innerText = subtotal;
    document.getElementById('tax').innerText = tax;
    document.getElementById('grand-total').innerText = grand;

    document.getElementById('subtotal_input').value = subtotal;
    document.getElementById('tax_input').value = tax;
    document.getElementById('shipping_input').value = shipping;
    document.getElementById('discount_input').value = discount;
    document.getElementById('grand_total_input').value = grand;
    document.getElementById('items_input').value = JSON.stringify(Object.values(cart));
}

document.getElementById('shipping').addEventListener('input', renderCart);
document.getElementById('discount').addEventListener('input', renderCart);
</script>

<!-- All product show -->
<script>
function loadAllProducts(page = 1) 
{
    fetch(`/po/all-products?page=${page}`)
        .then(res => res.json())
        .then(data => {

            let area = document.getElementById('products-area');
            area.innerHTML = '';

            data.data.forEach(p => {
                area.innerHTML += `
                    <tr>
                        <td>${p.name}</td>
                        <td>₹${p.base_price}</td>
                        <td>
                            <button class="btn btn-sm btn-success"
                                onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.base_price})">
                                Add
                            </button>
                        </td>
                    </tr>`;
            });

            renderPagination(data);
        });
}
</script>

<!-- pagination function -->
<script>
function renderPagination(data)
{
    let pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    for (let i = 1; i <= data.last_page; i++) {
        pagination.innerHTML += `
            <li class="page-item ${data.current_page === i ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)"
                    onclick="loadAllProducts(${i})">
                    ${i}
                </a>
            </li>`;
    }
}
</script>

<!-- without pagination All product show -->
<!-- <script>
    function loadAllProducts() 
    {
        fetch(`/po/all-products`)
            .then(res => res.json())
            .then(data => {
                let area = document.getElementById('products-area');
                area.innerHTML = '';

                // data.forEach(p => {
                //     area.innerHTML += `
                //         <div class="border p-2 cursor-pointer hover:bg-gray-100"
                //             onclick="addToCart(${p.id}, '${p.name}', ${p.base_price})">
                //             <strong>${p.name}</strong><br>
                //             ₹${p.base_price}
                //         </div>`;
                // });
                data.forEach(p => {
                area.innerHTML += `
                    <tr style="border-bottom:1px solid black;">
                        <td>${p.name}</td>
                        <td>₹${p.base_price}</td>
                        <td>
                            <button class="btn btn-sm btn-primary text-white bg-success border-none rounded"
                                onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.base_price})">
                                Add 
                            </button>
                        </td>
                    </tr>`;
            });
            });
    }
</script> -->

<!-- all product show on page reaload -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadAllProducts();
    });
</script>

<!-- validation massage for empty cart -->
<script>
function submitOrder()
{
    let items = document.getElementById('items_input').value;

    if (!items || items === '[]') {
        alert('❌ Please add at least one product before saving order');
        return;
    }

    document.getElementById('purchaseOrderForm').submit();
}
</script>

