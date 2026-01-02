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
                        @if ($errors->any())
                        <div class="alert alert-danger mb-2">
                            {{ $errors->first() }}
                        </div>
                        @endif

                        @if (session('success'))
                        <div class="alert alert-success mb-2">
                            {{ session('success') }}
                        </div>
                        @endif

                        <div class="card mb-4">
                            <h4 class="card-header text-center">
                                purchase order
                            </h4>

                            <div class="card-body">

                                <!-- GRID 12 -->
                                <div class="row">

                                    <!-- LEFT : PRODUCTS (8) -->
                                    <div class="col-12 col-md-8">

                                        <div class="row mt-3 mb-4 align-items-end g-3">

                                            <!-- All Product Button -->
                                            <div class="col-md-2 col-12">
                                                <button type="button" class="btn btn-success w-100"
                                                    onclick="loadAllProducts()">
                                                    All Product
                                                </button>
                                            </div>

                                            <!-- Supplier -->
                                            <div class="col-md-3 col-12">
                                                <label class="form-label fw-semibold">Supplier</label>
                                                <select name="supplier_id" id="supplier" class="form-select" required>
                                                    <option value="">Select Supplier</option>
                                                    @foreach($suppliers as $sup)
                                                    <option value="{{ $sup->id }}">
                                                        {{ $sup->supplier_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Category -->
                                            <div class="col-md-4 col-12">
                                                <label class="form-label fw-semibold">Category</label>
                                                <select id="category" class="form-select">
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">
                                                        {{ $cat->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Sub Category -->
                                            <div class="col-md-3 col-12">
                                                <label class="form-label fw-semibold">Sub Category</label>
                                                <select id="sub_category" class="form-select">
                                                    <option value="">Select Sub Category</option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="mb-3 row">
                                            <div class="col-md-6 mt-3">
                                                <span class="text-bold font-bold fw-bolder">All Listed Products</span>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <input type="text" id="search" placeholder="Search..."
                                                    class="border p-2 rounded w-full">
                                            </div>
                                        </div>

                                        <!-- Products -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product</th>
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

                                        <form id="purchaseOrderForm" method="POST"
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
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bill-items"></tbody>
                                                </table>

                                                <div class="text-right space-y-2 mt-5">
                                                    <input type="hidden" name="supplier_id" id="supplier_hidden">
                                                    <input type="hidden" name="items" id="items_input">

                                                    <!-- <button
                                                        class="bg-green-600 text-white bg-success border-none rounded w-full py-2 mt-4 rounded">
                                                        Save Purchase Order
                                                    </button> -->
                                                    <button type="button" onclick="submitOrder()"
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
   
    function addProduc(id, name) {

    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = {
            product_id: id,
            name,
            qty: 1
        };
    }

    renderCart();
}


    document.getElementById('supplier').addEventListener('change', function() {
        document.getElementById('supplier_hidden').value = this.value;
    });

    document.getElementById('category').addEventListener('change', function() {
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

    document.getElementById('sub_category').addEventListener('change', function() {

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

               
                data.forEach(p => {
                    area.innerHTML += `
                    <tr style="border-bottom:1px solid black;">
                        <td>${p.name}</td>
                        <td>
                            <button class="btn btn-sm btn-primary text-white bg-success border-none rounded"
                                onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}')">
                                Add 
                            </button>
                        </td>
                    </tr>`;
                });
            })
            .catch(err => console.error(err));
    });

    function addToCart(id, name) {
    addProduct(id, name);
}

    function renderCart() {
        let tbody = document.getElementById('bill-items');
        tbody.innerHTML = '';

        let subtotal = 0;

        Object.values(cart).forEach(item => {
            tbody.innerHTML += `
            <tr style="border-bottom:1px solid black;">
                <td>${item.name}</td>
                <td>
                    <input type="number"
                        min="1"
                        value="${item.qty}"
                        class="form-control form-control-sm text-center"
                        style="width:80px"
                        onchange="updateQty(${item.product_id}, this.value)">

                    </td>
                <td><button type="button" onclick="delete cart[${item.product_id}]; renderCart()">X</button></td>
            </tr>`;
        });

        document.getElementById('items_input').value = JSON.stringify(Object.values(cart));
    }

</script>

<!-- All product show -->
<script>
    function loadAllProducts(page = 1) {
        fetch(`/po/all-products?page=${page}`)
            .then(res => res.json())
            .then(data => {

                let area = document.getElementById('products-area');
                area.innerHTML = '';

                data.data.forEach(p => {
                    area.innerHTML += `
                    <tr>
                        <td>${p.name}</td>
                        <td>
                            <button class="btn btn-sm btn-success"
                                onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}')">
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
    function renderPagination(data) {
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

<!-- all product show on page reaload -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadAllProducts();
    });

<!-- validation massage for empty cart -->
 function submitOrder() {

    // FORCE supplier sync
    document.getElementById('supplier_hidden').value =
        document.getElementById('supplier').value;

    let items = document.getElementById('items_input').value;

    if (!items || items === '[]') {
        alert('❌ Please add at least one product before saving order');
        return;
    }

    if (!document.getElementById('supplier_hidden').value) {
        alert('❌ Please select supplier');
        return;
    }

    document.getElementById('purchaseOrderForm').submit();
}
</script>
