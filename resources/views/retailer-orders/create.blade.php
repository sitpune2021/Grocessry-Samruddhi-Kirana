<form method="POST" action="{{ route('retailer-orders.store') }}">
    @csrf

    <!-- Retailer -->
    <select id="retailer_id" name="retailer_id" required>
        <option value="">Select Retailer</option>
        @foreach($retailers as $retailer)
            <option value="{{ $retailer->id }}">{{ $retailer->name }}</option>
        @endforeach
    </select>

    <!-- Category -->
    <select id="category_id" required></select>

    <!-- Product -->
    <select id="product_id" required></select>

    <!-- Locked Price -->
    <input type="number" id="price" readonly placeholder="Price">

    <!-- Quantity -->
    <input type="number" id="quantity" placeholder="Qty" min="1" required>

    <!-- 🔒 Hidden fields (actual submit data) -->
    <input type="hidden" name="items[0][category_id]" id="h_category">
    <input type="hidden" name="items[0][product_id]" id="h_product">
    <input type="hidden" name="items[0][price]" id="h_price">
    <input type="hidden" name="items[0][quantity]" id="h_quantity">

    <button type="submit">
        Place Order
    </button>
</form>

<script>
document.querySelector('form').addEventListener('submit', function () {

    document.getElementById('h_category').value = category.value;
    document.getElementById('h_product').value  = product.value;
    document.getElementById('h_price').value    = priceEl.value;
    document.getElementById('h_quantity').value = document.getElementById('quantity').value;

});
</script>



<script>
const retailer = document.getElementById('retailer_id');
const category = document.getElementById('category_id');
const product  = document.getElementById('product_id');
const priceEl  = document.getElementById('price');

retailer.addEventListener('change', function () {

    category.innerHTML = '<option value="">Loading...</option>';
    product.innerHTML  = '<option value="">Select Product</option>';
    priceEl.value = '';

    fetch(`/retailer-orders/get-categories-by-retailer/${this.value}`)
        .then(res => res.json())
        .then(data => {
            category.innerHTML = '<option value="">Select Category</option>';
            data.forEach(cat => {
                category.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        });
});
</script>

<script>
category.addEventListener('change', function () {

    product.innerHTML = '<option value="">Loading...</option>';
    priceEl.value = '';

    fetch(`/retailer-orders/get-products-by-retailer/${retailer.value}/${this.value}`)
        .then(res => res.json())
        .then(data => {
            product.innerHTML = '<option value="">Select Product</option>';
            data.forEach(p => {
                product.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
        });
});
</script>

<script>
product.addEventListener('change', function () {

    if (!retailer.value || !this.value) return;

    fetch(`/retailer-orders/get-retailer-price/${retailer.value}/${this.value}`)
        .then(res => res.json())
        .then(data => {
            priceEl.value = data.price ?? 0;
        });
});
</script>

