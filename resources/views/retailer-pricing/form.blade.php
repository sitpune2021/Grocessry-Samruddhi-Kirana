@include('layouts.header')

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

        @include('layouts.sidebar')

        <div class="layout-page">

            @include('layouts.navbar')

            <div class="content-wrapper">

                <div class="container-xxl flex-grow-1 container-p-y">

                    <div class="row justify-content-center">

                        <div class="col-12">

                            <div class="card shadow-sm border-0 rounded-3">

                                {{-- Header --}}
                                <div class="card-header bg-white py-3 px-4 border-bottom">

                                    <h4 class="mb-0">
                                        <i class="bx bx-purchase-tag me-1"></i>

                                        {{ isset($pricing)
                                            ? 'Edit Retailer Pricing'
                                            : 'Assign Retailer Pricing' }}
                                    </h4>

                                </div>

                                {{-- Card Body --}}
                                <div class="card-body px-4 py-4">

                                    <form method="POST"
                                        action="{{ isset($pricing)
                                            ? route('retailer-pricing.update', $pricing)
                                            : route('retailer-pricing.store') }}">

                                        @csrf

                                        @isset($pricing)
                                            @method('PUT')
                                        @endisset


                                        {{-- ========================================= --}}
                                        {{-- Retailer + Distribution Center --}}
                                        {{-- ========================================= --}}

                                        <div class="row g-4">


                                            {{-- ========================================= --}}
                                            {{-- Distribution Center --}}
                                            {{-- ========================================= --}}

                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Distribution Center
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select
                                                    name="warehouse_id"
                                                    id="warehouse_id"
                                                    class="form-select @error('warehouse_id') is-invalid @enderror"
                                                    required
                                                >

                                                    @foreach($warehouses as $warehouseItem)

                                                        <option
                                                            value="{{ $warehouseItem->id }}"
                                                            @selected(
                                                                old(
                                                                    'warehouse_id',
                                                                    $warehouse->id
                                                                ) == $warehouseItem->id
                                                            )
                                                        >
                                                            {{ $warehouseItem->name }}
                                                        </option>

                                                    @endforeach

                                                </select>

                                                @error('warehouse_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                                <small class="text-muted">
                                                    Your assigned Distribution Center
                                                </small>

                                            </div>

                                            {{-- ========================================= --}}
                                            {{-- Retailer --}}
                                            {{-- ========================================= --}}

                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Retailer
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select
                                                    name="retailer_id"
                                                    id="retailer_id"
                                                    class="form-select @error('retailer_id') is-invalid @enderror"
                                                    required
                                                >

                                                    <option value="">
                                                        Select Retailer
                                                    </option>

                                                    @forelse($retailers as $retailer)

                                                        <option
                                                            value="{{ $retailer->id }}"
                                                            @selected(
                                                                old(
                                                                    'retailer_id',
                                                                    $pricing->retailer_id ?? ''
                                                                ) == $retailer->id
                                                            )
                                                        >
                                                            {{ $retailer->name }}

                                                            @if($retailer->shop_name)
                                                                - {{ $retailer->shop_name }}
                                                            @endif
                                                        </option>

                                                    @empty

                                                        <option value="" disabled>
                                                            No retailers found for this Distribution Center
                                                        </option>

                                                    @endforelse

                                                </select>

                                                @error('retailer_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>


                                            {{-- ========================================= --}}
                                            {{-- Category + Product --}}
                                            {{-- ========================================= --}}

                                            {{-- Category --}}
                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Category
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select
                                                    name="category_id"
                                                    id="category_id"
                                                    class="form-select @error('category_id') is-invalid @enderror"
                                                    required
                                                    disabled>

                                                    <option value="">
                                                        Select Category
                                                    </option>

                                                    @foreach($categories as $category)

                                                        <option
                                                            value="{{ $category->id }}"
                                                            @selected(
                                                                old(
                                                                    'category_id',
                                                                    $pricing->category_id ?? ''
                                                                ) == $category->id
                                                            )>

                                                            {{ $category->name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                                @error('category_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>


                                            {{-- Product --}}
                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Product
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select
                                                    name="product_id"
                                                    id="product_id"
                                                    class="form-select @error('product_id') is-invalid @enderror"
                                                    required
                                                    disabled>

                                                    <option value="">
                                                        Select Product
                                                    </option>

                                                </select>

                                                @error('product_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                                <small
                                                    id="productHelp"
                                                    class="text-muted">

                                                    First select Distribution Center
                                                    and Category.

                                                </small>

                                            </div>


                                            {{-- Base Price --}}
                                            {{-- ========================================= --}}
                                            {{-- Base Price / Product MRP --}}
                                            {{-- ========================================= --}}

                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Base Price
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    type="number"
                                                    name="base_price"
                                                    id="base_price"
                                                    class="form-control @error('base_price') is-invalid @enderror"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="Select product"
                                                    value="{{ old(
                                                        'base_price',
                                                        $pricing->base_price ?? ''
                                                    ) }}"
                                                    readonly
                                                    required>

                                                @error('base_price')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                                <small class="text-muted">
                                                    Product MRP will be automatically used as Base Price.
                                                </small>

                                            </div>


                                            {{-- Discount Percent --}}
                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Discount Percent (%)
                                                </label>

                                                <input
                                                    type="number"
                                                    name="discount_percent"
                                                    id="discount_percent"
                                                    class="form-control @error('discount_percent') is-invalid @enderror"
                                                    step="0.01"
                                                    min="0"
                                                    max="100"
                                                    placeholder="Enter discount %"
                                                    value="{{ old(
                                                        'discount_percent',
                                                        $pricing->discount_percent ?? '0'
                                                    ) }}">

                                                @error('discount_percent')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>


                                            {{-- Discount Amount --}}
                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Discount Amount
                                                </label>

                                                <input
                                                    type="number"
                                                    name="discount_amount"
                                                    id="discount_amount"
                                                    class="form-control @error('discount_amount') is-invalid @enderror"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="Auto calculated"
                                                    value="{{ old(
                                                        'discount_amount',
                                                        $pricing->discount_amount ?? '0'
                                                    ) }}">

                                                @error('discount_amount')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>


                                            {{-- Effective From --}}
                                            <div class="col-12 col-md-6">

                                                <label class="form-label fw-semibold">
                                                    Effective From
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    type="date"
                                                    name="effective_from"
                                                    class="form-control @error('effective_from') is-invalid @enderror"
                                                    value="{{ old(
                                                        'effective_from',
                                                        $pricing->effective_from ?? date('Y-m-d')
                                                    ) }}"
                                                    required>

                                                @error('effective_from')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>

                                        </div>


                                        {{-- ========================================= --}}
                                        {{-- Buttons --}}
                                        {{-- ========================================= --}}

                                        <div class="mt-4 d-flex justify-content-end gap-2 text-end">

                                            <a
                                                href="{{ route('retailer-pricing.index') }}"
                                                class="btn btn-success">

                                                <i class="bx bx-arrow-back me-1"></i>
                                                Back

                                            </a>

                                            <button
                                                type="submit"
                                                class="btn btn-success">

                                                <i class="bx bx-save me-1"></i>

                                                {{ isset($pricing)
                                                    ? 'Update Price'
                                                    : 'Assign Price' }}

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @include('layouts.footer')

            </div>

        </div>

    </div>
</div>



{{-- ========================================================= --}}
{{-- JavaScript --}}
{{-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const warehouseSelect = document.getElementById('warehouse_id');
    const retailerSelect  = document.getElementById('retailer_id');
    const categorySelect  = document.getElementById('category_id');
    const productSelect   = document.getElementById('product_id');

    const productHelp = document.getElementById('productHelp');


    /*
    |--------------------------------------------------------------------------
    | Old / Edit Values
    |--------------------------------------------------------------------------
    */

    const oldRetailerId = @json(
        old('retailer_id', $pricing->retailer_id ?? '')
    );

    const oldCategoryId = @json(
        old('category_id', $pricing->category_id ?? '')
    );

    const oldProductId = @json(
        old('product_id', $pricing->product_id ?? '')
    );


    /*
    |--------------------------------------------------------------------------
    | Enable Category when DC exists
    |--------------------------------------------------------------------------
    */

    function enableCategory()
    {
        if (warehouseSelect.value) {

            categorySelect.disabled = false;

        } else {

            categorySelect.disabled = true;

            categorySelect.value = '';

            productSelect.disabled = true;

            productSelect.innerHTML =
                '<option value="">Select Product</option>';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Warehouse Change
    |--------------------------------------------------------------------------
    */

    warehouseSelect.addEventListener('change', function () {

        const warehouseId = this.value;


        /*
        | Reset Category
        */

        categorySelect.value = '';


        /*
        | Reset Product
        */

        productSelect.innerHTML =
            '<option value="">Select Product</option>';

        productSelect.disabled = true;


        /*
        | No DC selected
        */

        if (!warehouseId) {

            categorySelect.disabled = true;

            productHelp.innerText =
                'First select Distribution Center and Category.';

            return;
        }


        /*
        | DC selected
        */

        categorySelect.disabled = false;

        productHelp.innerText =
            'Select a category to load products.';

    });


    /*
    |--------------------------------------------------------------------------
    | Category Change
    |--------------------------------------------------------------------------
    */

    categorySelect.addEventListener('change', function () {

        const warehouseId = warehouseSelect.value;
        const categoryId  = this.value;


        /*
        | Reset Product
        */

        productSelect.innerHTML =
            '<option value="">Loading products...</option>';

        productSelect.disabled = true;


        /*
        | DC not selected
        */

        if (!warehouseId) {

            productSelect.innerHTML =
                '<option value="">Select Distribution Center first</option>';

            return;
        }


        /*
        | Category not selected
        */

        if (!categoryId) {

            productSelect.innerHTML =
                '<option value="">Select Product</option>';

            productHelp.innerText =
                'Select a category to load products.';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Products
        |--------------------------------------------------------------------------
        */

        const url =
            "{{ url('/retailer-pricing/get-products-by-warehouse-category') }}"
            + "/" + warehouseId
            + "/" + categoryId;


        fetch(url)

            .then(function (response) {

                if (!response.ok) {

                    throw new Error(
                        'Failed to load products'
                    );

                }

                return response.json();

            })


            .then(function (data) 
            {

                /*
                | Reset Product
                */

                productSelect.innerHTML =
                    '<option value="">Select Product</option>';


                /*
                | No Products
                */

                if (!Array.isArray(data) || data.length === 0) {

                    productSelect.innerHTML +=
                        '<option value="" disabled>'
                        + 'No products available in this DC'
                        + '</option>';

                    productHelp.innerText =
                        'No products found for selected category.';

                    return;
                }

                /*
                | Add Products
                */

                data.forEach(product => {

                    const option = document.createElement('option');

                    option.value = product.id;
                    option.textContent =
                        product.name + ' - MRP ₹' +
                        parseFloat(product.mrp).toFixed(2);

                    // Store MRP in option
                    option.dataset.mrp = product.mrp;

                    productSelect.appendChild(option);

                });

                /*
                | Enable Product
                */

                productSelect.disabled = false;

                /*
                |--------------------------------------------------------------------------
                | Product Select -> MRP -> Base Price
                |--------------------------------------------------------------------------
                */

                productSelect.addEventListener('change', function () {

                    const selectedOption =
                        this.options[this.selectedIndex];

                    if (!selectedOption || !selectedOption.value) {

                        basePrice.value = '';

                        return;
                    }

                    const mrp =
                        parseFloat(selectedOption.dataset.mrp) || 0;

                    basePrice.value =
                        mrp.toFixed(2);

                });

                productHelp.innerText =
                    data.length
                    + ' product(s) available in this DC.';


                /*
                | Edit / Old Product
                */

                if (oldProductId) {

                    productSelect.value = oldProductId;

                }

            })


            .catch(function (error) {

                console.error(
                    'Product loading error:',
                    error
                );


                productSelect.innerHTML =
                    '<option value="">'
                    + 'Error loading products'
                    + '</option>';


                productHelp.innerText =
                    'Unable to load products. Please try again.';

            });

    });


    /*
    |--------------------------------------------------------------------------
    | Initial Page Load
    |--------------------------------------------------------------------------
    */

    enableCategory();


    /*
    |--------------------------------------------------------------------------
    | Edit Mode
    |--------------------------------------------------------------------------
    */

    @if(isset($pricing))

        warehouseSelect.value =
            @json($pricing->warehouse_id);


        retailerSelect.value =
            @json($pricing->retailer_id);


        categorySelect.disabled = false;

        categorySelect.value =
            @json($pricing->category_id);


        /*
        | Load Products
        */

        categorySelect.dispatchEvent(
            new Event('change')
        );

    @endif


    /*
    |--------------------------------------------------------------------------
    | Validation Error / Old Input
    |--------------------------------------------------------------------------
    */

    @if(old('warehouse_id'))

        warehouseSelect.value =
            @json(old('warehouse_id'));

        categorySelect.disabled = false;

    @endif


    @if(old('category_id'))

        categorySelect.value =
            @json(old('category_id'));

        categorySelect.dispatchEvent(
            new Event('change')
        );

    @endif


    /*
    |--------------------------------------------------------------------------
    | Discount Calculation
    |--------------------------------------------------------------------------
    */

    const basePrice =
        document.getElementById('base_price');

    const discountPercent =
        document.getElementById('discount_percent');

    const discountAmount =
        document.getElementById('discount_amount');


    function calculateDiscount()
    {

        const price =
            parseFloat(basePrice.value) || 0;


        const percent =
            parseFloat(discountPercent.value) || 0;


        if (price > 0 && percent > 0) {

            const amount =
                (price * percent) / 100;


            discountAmount.value =
                amount.toFixed(2);

        }

    }


    /*
    | Discount Percent Change
    */

    discountPercent.addEventListener(
        'input',
        calculateDiscount
    );


    /*
    | Base Price Change
    */

    basePrice.addEventListener(
        'input',
        calculateDiscount
    );

});

</script>