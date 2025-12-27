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
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row g-6">

                            <!-- Form controls -->
                            <div class="col-12">
                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">

                                        @if ($mode === 'add')
                                            <h4> Add Supplier </h4>
                                        @elseif($mode === 'edit')
                                            Edit Supplier
                                        @else
                                            View Supplier
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        @php
                                            // $mode = add | edit | view
                                            $readonly = $mode === 'view' ? 'readonly' : '';
                                        @endphp

                                        <form
                                            action="{{ $mode === 'edit' ? route('supplier.update', $supplier->id) : route('supplier.store') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @if ($mode === 'edit')
                                                @method('PUT')
                                            @endif

                                            <div class="row g-3">

                                                {{-- Supplier Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Supplier Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="supplier_name" class="form-control"
                                                        value="{{ $supplier->supplier_name ?? '' }}"
                                                        placeholder="Enter supplier name" {{ $readonly }}>
                                                    @error('supplier_name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Mobile <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="mobile" name="mobile"
                                                        class="form-control" value="{{ $supplier->mobile ?? '' }}"
                                                        placeholder="Enter mobile" maxlength="10" {{ $readonly }}>
                                                    <div id="mobile-error" class="text-danger mt-1"></div>
                                                </div>



                                                {{-- Email --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $supplier->email ?? '' }}" placeholder="Enter email"
                                                        {{ $readonly }}>

                                                </div>

                                                {{-- Address --}}
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium">Address</label>
                                                    <textarea name="address" class="form-control" rows="2" placeholder="Enter address"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>{{ $supplier->address ?? '' }}</textarea>
                                                </div>

                                                {{-- Logo --}}
                                              <div class="col-md-4">
    <label class="form-label fw-medium">Supplier Logo</label>

    {{-- Upload input for edit --}}
    <input type="file" name="logo" class="form-control" accept="image/*">

    {{-- Display existing logo if available --}}
    @if (isset($supplier) && $supplier->logo)
        <div class="mt-2">
            {{-- Show small preview --}}
            {{-- <a href="{{ asset('storage/suppliers/' . $supplier->logo) }}" target="_blank">
                <img src="{{ asset('storage/suppliers/' . $supplier->logo) }}" width="80" class="rounded border" alt="Supplier Logo">
            </a> --}}
            <p class="mt-1"><a href="{{ asset('storage/suppliers/' . $supplier->logo) }}" target="_blank">View Logo</a></p>
        </div>
    @endif
</div>




                                            </div>

                                            {{-- Buttons --}}
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('supplier.index') }}"
                                                    class="btn btn-outline-secondary">
                                                    Back
                                                </a>

                                                @if ($mode === 'add')
                                                    <button type="submit" class="btn btn-primary">Save
                                                        Supplier</button>
                                                @elseif($mode === 'edit')
                                                    <button type="submit" class="btn btn-primary">Update
                                                        Supplier</button>
                                                @endif
                                            </div>

                                        </form>


                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <!-- / Content -->
                    @include('layouts.footer')
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

    </div>
    <!-- / Layout wrapper -->
</body>

<script>
    const mobileInput = document.getElementById('mobile');
    const submitBtn = document.getElementById('submitBtn');
    const errorDiv = document.getElementById('mobile-error');

    // Prevent non-digit characters and limit to 10 digits
    mobileInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, ''); // remove non-digits
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10); // limit to 10 digits
        }
    });

    // Validate on submit
    submitBtn.addEventListener('click', function(e) {
        const mobile = mobileInput.value.trim();
        errorDiv.textContent = '';

        if (!/^\d{10}$/.test(mobile)) {
            e.preventDefault(); // stop form submission
            errorDiv.textContent = 'Mobile number must be exactly 10 digits';
            mobileInput.focus();
            return false;
        }
    });
</script>
