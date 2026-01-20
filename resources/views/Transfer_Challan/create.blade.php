@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="card shadow-sm border-0 rounded-3">

                            {{-- Header --}}
                            <div class="card-header bg-white fw-semibold">
                                @if ($mode === 'add')
                                    <h4>Add Transfer Challan</h4>
                                @elseif ($mode === 'edit')
                                    <h4>Edit Transfer Challan</h4>
                                @else
                                    <h4>View Transfer Challan</h4>
                                @endif
                            </div>

                            <div class="card-body">
                                @php
                                    $readonly = $mode === 'view' ? 'readonly' : '';
                                @endphp

                                <form method="POST"
                                    action="{{ $mode === 'edit'
                                        ? route('transfer-challans.update', $transferChallan->id)
                                        : route('transfer-challans.store') }}">
                                    @csrf
                                    @if ($mode === 'edit')
                                        @method('PUT')
                                    @endif

                                    <div class="row g-3">

                                        {{-- Challan No --}}
                                        <!-- <div class="col-md-4">
                                            <label class="form-label">Challan No <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="challan_no" class="form-control"
                                                placeholder="Enter challan no"
                                                value="{{ $transferChallan->challan_no ?? old('challan_no') }}"
                                                {{ $readonly ? 'readonly' : '' }}>
                                            @error('challan_no')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div> -->

                                        {{-- Transfer Date --}}
                                        <div class="col-md-4">
                                            <label class="form-label">Transfer Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="transfer_date" class="form-control"
                                                value="{{ $transferChallan->transfer_date ?? date('Y-m-d') }}"
                                                {{ $readonly ? 'readonly' : '' }}>
                                            @error('transfer_date')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- From Warehouse --}}
                                        <div class="col-md-6">
                                            <label class="form-label">From Warehouse <span
                                                    class="text-danger">*</span></label>
                                            <select name="from_warehouse_id" class="form-select" readonly>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $fromWarehouse == $warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('from_warehouse_id')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- To Warehouse --}}
                                        <div class="col-md-6">
                                            <label class="form-label">To Warehouse <span
                                                    class="text-danger">*</span></label>
                                            <select name="to_warehouse_id" class="form-select" readonly>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $toWarehouse == $warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('to_warehouse_id')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    {{-- ITEMS --}}
                                    <hr class="my-4">

                                    <h6 class="fw-semibold mb-3">Transfer Items</h6>

                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:50%">Product</th>
                                                <th style="width:30%">Quantity</th>
                                                @if ($mode !== 'view')
                                                    <th style="width:20%">Action</th>
                                                @endif
                                            </tr>
                                        </thead>

                                        <tbody id="itemsTable">

                                            @if(!empty($transferItems) && count($transferItems))
                                                @foreach ($transferItems as $item)
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="products[]" value="{{ $item->product_id }}">
                                                            <input type="text" class="form-control" 
                                                                value="{{ $item->product->name }}" readonly>
                                                        </td>

                                                        <td>
                                                            <input type="number" name="quantities[]" 
                                                                class="form-control"
                                                                value="{{ $item->quantity }}"
                                                                min="1"
                                                                max="{{ $item->quantity }}">
                                                        </td>

                                                        <td>
                                                            <span class="text-muted">Locked</span>
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                            @endif

                                        </tbody>

                                    </table>

                                    <!-- @if ($mode !== 'view')
                                        <button type="button" id="addRow" class="btn btn-outline-primary btn-sm">
                                            + Add Item
                                        </button>
                                    @endif -->

                                    {{-- Buttons --}}
                                    <div class="mt-4 d-flex justify-content-end gap-2">
                                        <a href="{{ route('transfer-challans.index') }}" class="btn btn-success">
                                            Back
                                        </a>

                                        @if ($mode === 'add')
                                            <button class="btn btn-success">Save Transfer</button>
                                        @elseif ($mode === 'edit')
                                            <button class="btn btn-primary">Update Transfer</button>
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

    <script>
        document.getElementById('addRow')?.addEventListener('click', function() {
            const table = document.getElementById('itemsTable');
            table.insertAdjacentHTML('beforeend', `
            <tr>
                <td>
                    <select name="products[]" class="form-select">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="quantities[]" class="form-control" step="0.01">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeRow">Remove</button>
                </td>
            </tr>
        `);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('tr').remove();
            }
        });
    </script>

</body>
