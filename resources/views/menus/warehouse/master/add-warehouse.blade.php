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
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h4 class="mb-0 flex-grow-1">
                                            @if($mode === 'add')
                                            Add Warehouse
                                            @elseif($mode === 'edit')
                                            Edit Warehouse
                                            @else
                                            View Warehouse
                                            @endif
                                        </h4>
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ $mode === 'edit'
                                            ? route('warehouse.update', $warehouse->id)
                                            : route('warehouse.store') }}"
                                            method="POST">
                                            @csrf
                                            @if($mode === 'edit')
                                            @method('PUT')
                                            @endif
                                            <div class="row">

                                                {{-- Warehouse Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Warehouse Name <span class="mandatory">*</span></label>
                                                        <input type="text" name="name"
                                                            class="form-control"
                                                            value="{{ old('name', $warehouse->name ?? '') }}"
                                                            placeholder="Warehouse Name"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- Warehouse Type --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Warehouse Type *</label>
                                                    <select name="type" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Type</option>
                                                        <option value="master" {{ ($warehouse->type ?? '') == 'master' ? 'selected' : '' }}>Master</option>
                                                        <option value="district" {{ ($warehouse->type ?? '') == 'district' ? 'selected' : '' }}>District</option>
                                                        <option value="taluka" {{ ($warehouse->type ?? '') == 'taluka' ? 'selected' : '' }}>Taluka</option>
                                                    </select>
                                                </div>

                                                {{-- Parent Warehouse --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Parent Warehouse</label>
                                                    <select name="parent_id" class="form-select"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        <option value="">Select Parent</option>
                                                        @foreach($warehouses as $w)
                                                        <option value="{{ $w->id }}"
                                                            {{ ($warehouse->parent_id ?? '') == $w->id ? 'selected' : '' }}>
                                                            {{ $w->name }} ({{ ucfirst($w->type) }})
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @php
                                                dd($warehouse)
                                                @endphp
                                                {{-- Country --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Country</label>
                                                    <select name="country_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Country</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ ($warehouse->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- State --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">State</label>
                                                    <select name="state_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        @if(isset($warehouse->state))
                                                        <option value="{{ $warehouse->state_id }}" selected>
                                                            {{ $warehouse->state->name }}
                                                        </option>
                                                        @else
                                                        <option value="">Select State</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                {{-- District --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">District</label>
                                                    <select name="district_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        @if(isset($warehouse->district))
                                                        <option value="{{ $warehouse->district_id }}" selected>
                                                            {{ $warehouse->district->name }}
                                                        </option>
                                                        @else
                                                        <option value="">Select District</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                {{-- Taluka --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Taluka</label>
                                                    <select name="taluka_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        @if(isset($warehouse->taluka))
                                                        <option value="{{ $warehouse->taluka_id }}" selected>
                                                            {{ $warehouse->taluka->name }}
                                                        </option>
                                                        @else
                                                        <option value="">Select Taluka</option>
                                                        @endif
                                                    </select>
                                                </div>



                                                {{-- Address --}}
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Address</label>
                                                    <textarea name="address" class="form-control" rows="2"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>{{ $warehouse->address ?? '' }}</textarea>
                                                </div>

                                                {{-- Contact Person --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Contact Person</label>
                                                    <input type="text" name="contact_person" class="form-control"
                                                        value="{{ $warehouse->contact_person ?? '' }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mobile</label>
                                                        <input type="text" name="mobile"
                                                            class="form-control"
                                                            maxlength="10"
                                                            value="{{ $warehouse->mobile ?? '' }}"
                                                            placeholder="Mobile"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- User Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">User Name</label>
                                                        <input type="text" name="user_name"
                                                            class="form-control"
                                                            placeholder="User Name"
                                                            value="{{ $warehouse->user_name ?? '' }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- User Email --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email"
                                                            class="form-control"
                                                            placeholder="Email"
                                                            value="{{ $warehouse->email ?? '' }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>
                                                {{-- Buttons --}}
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <a href="{{ route('warehouse.index') }}" class="btn btn-info">Cancel</a>

                                                    @if($mode === 'add')
                                                    <button type="submit" class="btn btn-primary">
                                                        Save Category
                                                    </button>
                                                    @elseif($mode === 'edit')
                                                    <button type="submit" class="btn btn-primary">
                                                        Update Category
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
    const typeSelect = document.getElementById('warehouseType');
    const parentDiv = document.getElementById('parentDiv');

    function toggleParent() {
        if (typeSelect.value === 'master') {
            parentDiv.style.display = 'none';
        } else {
            parentDiv.style.display = 'block';
        }
    }

    typeSelect.addEventListener('change', toggleParent);
    toggleParent();
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#category_id').change(function() {
        let categoryId = $(this).val();
        $('#product_id').html('<option value="">Loading...</option>');

        if (categoryId) {
            $.ajax({
                url: '/get-products/' + categoryId,
                type: 'GET',
                success: function(products) {
                    let options = '<option value="">-- Select Product --</option>';
                    products.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                    $('#product_id').html(options);
                }
            });
        } else {
            $('#product_id').html('<option value="">-- Select Product --</option>');
        }
    });
</script>
<script>
    document.getElementById('country_id').addEventListener('change', function() {
        let countryId = this.value;

        fetch(`/get-states/${countryId}`)
            .then(res => res.json())
            .then(data => {
                let state = document.getElementById('state_id');
                state.innerHTML = '<option value="">Select State</option>';

                data.forEach(item => {
                    state.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
            });
    });

    document.getElementById('state_id').addEventListener('change', function() {
        let stateId = this.value;

        fetch(`/get-districts/${stateId}`)
            .then(res => res.json())
            .then(data => {
                let district = document.getElementById('district_id');
                district.innerHTML = '<option value="">Select District</option>';

                data.forEach(item => {
                    district.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
            });
    });

    document.getElementById('district_id').addEventListener('change', function() {
        let districtId = this.value;

        fetch(`/get-talukas/${districtId}`)
            .then(res => res.json())
            .then(data => {
                let taluka = document.getElementById('taluka_id');
                taluka.innerHTML = '<option value="">Select Taluka</option>';

                data.forEach(item => {
                    taluka.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
            });
    });
</script>