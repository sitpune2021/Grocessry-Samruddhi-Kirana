@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card shadow-sm">

                            <div class="card-header bg-white fw-semibold">
                                <i class="bx bx-receipt me-1"></i>
                                @if ($mode === 'view')
                                    View Supplier Challan
                                @elseif($mode === 'edit')
                                    Edit Supplier Challan
                                @else
                                    Create Supplier Challan
                                @endif
                            </div>

                            <div class="card-body">
                                <form method="POST"
                                    action="{{ isset($challan) && $mode !== 'view'
                                        ? route('supplier_challan.update', $challan->id)
                                        : route('supplier_challan.store') }}">

                                    @csrf
                                    @if (isset($challan) && $mode === 'edit')
                                        @method('PUT')
                                    @endif


                                    <div class="row">
                                        {{-- Master Warehouse (Auto – Only Master Admin) --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Warehouse *</label>

                                            {{-- Show name --}}
                                            <input type="text" class="form-control" value="{{ $warehouse->name }}"
                                                readonly>

                                            {{-- Submit ID --}}
                                            <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
                                        </div>


                                        {{-- Supplier --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Supplier *</label>
                                            <select name="supplier_id" class="form-select"
                                                {{ $mode === 'view' ? 'disabled' : '' }}>
                                                <option value="">Select Supplier</option>
                                                @foreach ($suppliers as $s)
                                                    <option value="{{ $s->id }}"
                                                        {{ old('supplier_id', $challan->supplier_id ?? '') == $s->id ? 'selected' : '' }}>
                                                        {{ $s->supplier_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Auto Challan No --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Challan No *</label>
                                            <input type="text" name="challan_no" class="form-control"
                                                value="{{ old('challan_no', $challan->challan_no ?? $autoChallanNo) }}">
                                        </div>

                                        {{-- Date --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Challan Date *</label>
                                            <input type="date" name="challan_date" class="form-control"
                                                value="{{ old('challan_date', $challan->challan_date ?? date('Y-m-d')) }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>
                                    </div>

                                    <hr>
                                    @php
                                        $isView = $mode === 'view';
                                    @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6 class="fw-semibold">Products</h6>
                                        @if (!$isView)
                                            <button type="button" id="addRow" class="btn btn-sm btn-secondary">
                                                + Add Product
                                            </button>
                                        @endif

                                    </div>


                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Category *</th>
                                                <th>Sub Category *</th>
                                                <th>Product *</th>
                                                <th>Qty *</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">

                                            @if (isset($challan))
                                                @foreach ($challan->items as $i => $item)
                                                    <tr>
                                                        <td>
                                                            <select name="items[{{ $i }}][category_id]"
                                                                class="form-select category"
                                                                {{ $isView ? 'disabled' : '' }}>

                                                                <option value="">Select</option>
                                                                @foreach ($categories as $c)
                                                                    <option value="{{ $c->id }}"
                                                                        {{ $item->product->category_id == $c->id ? 'selected' : '' }}>
                                                                        {{ $c->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <select name="items[{{ $i }}][sub_category_id]"
                                                                class="form-select sub_category"
                                                                {{ $isView ? 'disabled' : '' }}>

                                                                <option value="{{ $item->product->sub_category_id }}"
                                                                    selected>
                                                                    {{ $item->product->subCategory->name ?? 'Selected' }}
                                                                </option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <select name="items[{{ $i }}][product_id]"
                                                                class="form-select product"
                                                                {{ $isView ? 'disabled' : '' }}>

                                                                <option value="{{ $item->product_id }}" selected>
                                                                    {{ $item->product->name }}
                                                                </option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="number"
                                                                name="items[{{ $i }}][received_qty]"
                                                                class="form-control" value="{{ $item->received_qty }}"
                                                                min="1" {{ $isView ? 'readonly' : '' }}>

                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm removeRow">X</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>
                                                        <select name="items[0][category_id]"
                                                            class="form-select category">
                                                            <option value="">Select</option>
                                                            @foreach ($categories as $c)
                                                                <option value="{{ $c->id }}">
                                                                    {{ $c->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="items[0][sub_category_id]"
                                                            class="form-select sub_category">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="items[0][product_id]" class="form-select product">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[0][received_qty]"
                                                            class="form-control" min="1">
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($mode !== 'view')
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm removeRow">X</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="text-end mt-3">
                                        <a href="{{ route('supplier_challan.index') }}" class="btn btn-secondary">
                                            Back
                                        </a>

                                        @if ($mode !== 'view')
                                            <button type="submit" class="btn btn-success">
                                                {{ isset($challan) ? 'Update Challan' : 'Save Challan' }}
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
</body>

{{-- JS --}}
<script>
    let rowIndex = 1;

    document.getElementById('addRow').addEventListener('click', () => {
        const row = document.querySelector('#itemsBody tr').cloneNode(true);
        row.querySelectorAll('select, input').forEach(el => el.value = '');
        row.querySelectorAll('select, input').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
        });
        document.getElementById('itemsBody').appendChild(row);
        rowIndex++;
    });

    document.addEventListener('change', function(e) {

        if (e.target.classList.contains('category')) {
            fetch(`/get-sub-categories/${e.target.value}`)
                .then(res => res.json())
                .then(data => {
                    const sub = e.target.closest('tr').querySelector('.sub_category');
                    sub.innerHTML = '<option value="">Select</option>';
                    data.forEach(s => sub.innerHTML += `<option value="${s.id}">${s.name}</option>`);
                });
        }

        if (e.target.classList.contains('sub_category')) {
            fetch(`/get-products-by-sub-category/${e.target.value}`)
                .then(res => res.json())
                .then(data => {
                    const prod = e.target.closest('tr').querySelector('.product');
                    prod.innerHTML = '<option value="">Select</option>';
                    data.forEach(p => prod.innerHTML += `<option value="${p.id}">${p.name}</option>`);
                });
        }

        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });
</script>
