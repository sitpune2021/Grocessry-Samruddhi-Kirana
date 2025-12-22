@extends('layouts.app')

@section('content')

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-semibold mb-4">
        {{ isset($retailer) ? 'Edit Retailer' : 'Create Retailer' }}
    </h2>

<form method="POST"
      action="{{ isset($pricing)
            ? route('retailer-pricing.update', $pricing)
            : route('retailer-pricing.store') }}">

    @csrf
    @isset($pricing) @method('PUT') @endisset

    <!-- Retailer -->
    <select name="retailer_id" required>
        <option value="">Select Retailer</option>
        @foreach($retailers as $r)
            <option value="{{ $r->id }}"
                @selected(old('retailer_id', $pricing->retailer_id ?? '') == $r->id)>
                {{ $r->name }}
            </option>
        @endforeach
    </select>

    <!-- Category -->
    <select id="category_id" name="category_id" required>
        <option value="">Select Category</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">
                {{ $cat->name }}
            </option>
        @endforeach
    </select>

    <!-- Product -->
    <select name="product_id" id="product_id" required>
        <option value="">Select Product</option>
    </select>

    <input name="base_price" type="number" placeholder="base price" step="0.01">
    <input name="discount_percent" type="number" placeholder="discount percent" step="0.01">
    <input name="discount_amount" type="number" placeholder="discount amount" step="0.01">

    <input type="date" name="effective_from">

    <button type="submit">
        {{ isset($pricing) ? 'Update Price' : 'Assign Price' }}
    </button>
</form>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const categorySelect = document.getElementById('category_id');
    const productSelect  = document.getElementById('product_id');

    categorySelect.addEventListener('change', function () {

        const categoryId = this.value;
        productSelect.innerHTML = '<option value="">Loading...</option>';

        if (!categoryId) {
            productSelect.innerHTML = '<option value="">Select Product</option>';
            return;
        }

        fetch("{{ url('/get-products-by-category') }}/" + categoryId)
            .then(res => res.json())
            .then(data => {

                productSelect.innerHTML = '<option value="">Select Product</option>';

                if (data.length === 0) {
                    productSelect.innerHTML +=
                        `<option value="">No products found</option>`;
                }

                data.forEach(product => {
                    productSelect.innerHTML +=
                        `<option value="${product.id}">${product.name}</option>`;
                });
            })
            .catch(() => {
                productSelect.innerHTML =
                    '<option value="">Error loading products</option>';
            });
    });
});
</script>

@if(isset($pricing))
@parent
<script>
window.addEventListener('load', function () {

    const categoryId = "{{ $pricing->product->category_id }}";
    const productId  = "{{ $pricing->product_id }}";

    const categorySelect = document.getElementById('category_id');
    categorySelect.value = categoryId;
    categorySelect.dispatchEvent(new Event('change'));

    setTimeout(() => {
        document.getElementById('product_id').value = productId;
    }, 300);
});
</script>
@endif

@endsection





