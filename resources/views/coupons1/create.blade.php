@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <!-- Layout page -->
            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="row g-6">
                            <div class="col-12">
                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">
                                        @if ($mode === 'add')
                                        <h4>Add Coupon</h4>
                                        @elseif($mode === 'edit')
                                        <h4>Edit Coupon</h4>
                                        @else
                                        <h4>View Coupon</h4>
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($coupon) ? route('coupons.update', $coupon->id) : route('coupons.store') }}"
                                            method="POST">
                                            @csrf
                                            @if (isset($coupon))
                                            @method('PUT')
                                            @endif

                                            <div class="row g-3">

                                                {{-- Coupon Code --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Coupon Code <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="code"
                                                        class="form-control @error('code') is-invalid @enderror"
                                                        value="{{ old('code', $coupon->code ?? '') }}"
                                                        placeholder="e.g. SAVE10"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Coupon Type --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Discount Type <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="type" class="form-control"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="flat"
                                                            {{ old('type', $coupon->type ?? '') == 'flat' ? 'selected' : '' }}>
                                                            Flat (₹)
                                                        </option>
                                                        <option value="percent"
                                                            {{ old('type', $coupon->type ?? '') == 'percent' ? 'selected' : '' }}>
                                                            Percentage (%)
                                                        </option>
                                                        <option value="free_shipping"
                                                            {{ old('type', $coupon->type ?? '') == 'free_shipping' ? 'selected' : '' }}>
                                                            Free Shipping
                                                        </option>
                                                    </select>
                                                </div>

                                                {{-- Discount Value --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Discount Value <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="value" step="0.01"
                                                        class="form-control @error('value') is-invalid @enderror"
                                                        value="{{ old('value', $coupon->value ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Minimum Cart Amount --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Minimum Cart Amount</label>
                                                    <input type="number" name="min_cart_amount" step="0.01"
                                                        class="form-control"
                                                        value="{{ old('min_cart_amount', $coupon->min_cart_amount ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                </div>

                                                {{-- Start Date --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Start Date <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" name="start_date"
                                                        class="form-control"
                                                        value="{{ old('start_date', $coupon->start_date ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                </div>

                                                {{-- End Date --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        End Date <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" name="end_date"
                                                        class="form-control"
                                                        value="{{ old('end_date', $coupon->end_date ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                </div>

                                                {{-- Usage Limit --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Usage Limit
                                                        <small class="text-muted">(Total uses)</small>
                                                    </label>

                                                    <input type="number"
                                                        name="usage_limit"
                                                        min="1"
                                                        class="form-control @error('usage_limit') is-invalid @enderror"
                                                        value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                                                        placeholder="e.g. 100"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                </div>

                                                {{-- Per User Limit --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Per User Limit
                                                        <small class="text-muted">(Uses per user)</small>
                                                    </label>

                                                    <input type="number"
                                                        name="per_user_limit"
                                                        min="1"
                                                        class="form-control @error('per_user_limit') is-invalid @enderror"
                                                        value="{{ old('per_user_limit', $coupon->per_user_limit ?? '') }}"
                                                        placeholder="e.g. 1"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                </div>


                                                {{-- Status --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Status <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="is_active" class="form-control"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="1"
                                                            {{ old('is_active', $coupon->is_active ?? 1) == 1 ? 'selected' : '' }}>
                                                            Active
                                                        </option>
                                                        <option value="0"
                                                            {{ old('is_active', $coupon->is_active ?? 1) == 0 ? 'selected' : '' }}>
                                                            Inactive
                                                        </option>
                                                    </select>
                                                </div>

                                            </div>

                                            {{-- Buttons --}}
                                            <div class="mt-4 d-flex justify-content-end gap-2 text-end">
                                                <a href="{{ route('coupons.index') }}" class="btn btn-success">
                                                    Back
                                                </a>

                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-success">
                                                    Save Coupon
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-success">
                                                    Update Coupon
                                                </button>
                                                @endif
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
</body>