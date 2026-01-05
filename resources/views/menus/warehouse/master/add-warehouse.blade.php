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
                                            @if ($mode === 'add')
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
                                            action="{{ $mode === 'edit' ? route('warehouse.update', $warehouse->id) : route('warehouse.store') }}"
                                            method="POST">
                                            @csrf
                                            @if ($mode === 'edit')
                                                @method('PUT')
                                            @endif
                                            <div class="row">

                                                {{-- Warehouse Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Warehouse Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ old('name', $warehouse->name ?? '') }}"
                                                            placeholder="Warehouse Name"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('name')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Warehouse Type --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Warehouse Type <span
                                                            class="text-danger">*</span></label>
                                                    <select name="type" id="warehouseType" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Type</option>
                                                        <option value="master"
                                                            {{ ($warehouse->type ?? '') == 'master' ? 'selected' : '' }}>
                                                            Master</option>
                                                        <option value="district"
                                                            {{ ($warehouse->type ?? '') == 'district' ? 'selected' : '' }}>
                                                            District</option>
                                                        <option value="taluka"
                                                            {{ ($warehouse->type ?? '') == 'taluka' ? 'selected' : '' }}>
                                                            Taluka</option>
                                                    </select>
                                                    @error('type')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Parent Warehouse --}}
                                                <div class="col-md-4 mb-3" id="parentDiv">
                                                    <label class="form-label">Parent Warehouse <span
                                                            class="text-danger">*</span></label>
                                                    <select name="parent_id" id="parent_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Parent</option>
                                                        @foreach ($warehouses as $w)
                                                            <option value="{{ $w->id }}"
                                                                data-type="{{ $w->type }}"
                                                                {{ ($warehouse->parent_id ?? '') == $w->id ? 'selected' : '' }}>
                                                                {{ $w->name }} ({{ ucfirst($w->type) }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('type')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- District --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">District <span
                                                            class="text-danger">*</span></label>
                                                    <select name="district_id" id="district_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select District</option>
                                                        @foreach ($districts as $district)
                                                            <option value="{{ $district->id }}"
                                                                {{ old('district_id', $warehouse->district_id ?? '') == $district->id ? 'selected' : '' }}>
                                                                {{ $district->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('type')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Taluka --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Taluka <span
                                                            class="text-danger">*</span></label>
                                                    <select name="taluka_id" id="taluka_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        @if (isset($warehouse->taluka))
                                                            <option value="{{ $warehouse->taluka_id }}" selected>
                                                                {{ $warehouse->taluka->name }}
                                                            </option>
                                                        @else
                                                            <option value="">Select Taluka</option>
                                                        @endif
                                                    </select>
                                                    @error('type')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Address --}}
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Address<span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="address" class="form-control" placeholder="address" rows="2"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>{{ $warehouse->address ?? '' }}</textarea>
                                                    @error('address')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Contact Person --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Contact Person <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" name="contact_person" class="form-control"
                                                        placeholder="contact Person Name"
                                                        value="{{ $warehouse->contact_person ?? '' }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('contact_person')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mobile <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="contact_number" class="form-control"
                                                            maxlength="10"
                                                            value="{{ $warehouse->contact_number ?? '' }}"
                                                            placeholder="Mobile"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>

                                                        @error('contact_number')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- User Name --}}
                                                <!-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">User Name</label>
                                                        <input type="text" name="user_name" class="form-control"
                                                            placeholder="User Name"
                                                            value="{{ $warehouse->contact_person ?? '' }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div> -->

                                                {{-- Email --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email<span
                                                            class="text-danger">*</span></label>
                                                        <input type="email" name="email" class="form-control"
                                                            placeholder="Email" value="{{ $warehouse->email ?? '' }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>

                                                        @error('email')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Buttons --}}
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <a href="{{ route('warehouse.index') }}"
                                                            class="btn btn-success">Cancel</a>
                                                        @if ($mode === 'add')
                                                            <button type="submit" class="btn btn-success">Save
                                                                Warehouse</button>
                                                        @elseif($mode === 'edit')
                                                            <button type="submit" class="btn btn-success">Update
                                                                Warehouse</button>
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
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const typeSelect = document.getElementById('warehouseType');
            const parentDiv = document.getElementById('parentDiv');
            const parentSelect = document.getElementById('parent_id');
            const districtSelect = document.getElementById('district_id');
            const talukaSelect = document.getElementById('taluka_id');

            /* ===============================
               Parent Warehouse logic
            =============================== */
            function toggleParent() {
                const selectedType = typeSelect.value;

                if (selectedType === 'master') {
                    parentDiv.style.display = 'none';
                } else {
                    parentDiv.style.display = 'block';
                    filterParentOptions(selectedType);
                }
            }

            function filterParentOptions(selectedType) {
                const options = parentSelect.querySelectorAll('option');
                const currentValue = parentSelect.value;

                options.forEach(opt => {
                    const type = opt.getAttribute('data-type');

                    if (selectedType === 'district') {
                        opt.style.display =
                            (type === 'Master' || opt.value === '') ? 'block' : 'none';
                    } else if (selectedType === 'taluka') {
                        opt.style.display =
                            (type === 'district' || opt.value === '') ? 'block' : 'none';
                    } else {
                        opt.style.display = 'block';
                    }
                });

                // reset only if invalid
                if (![...options].some(o => o.value === currentValue && o.style.display !== 'none')) {
                    parentSelect.value = '';
                }
            }

            typeSelect.addEventListener('change', toggleParent);
            toggleParent(); // page load


            /* ===============================
               Taluka dynamic loading
            =============================== */
            function loadTalukas(districtId, selectedTalukaId = null) {
                if (!districtId) {
                    talukaSelect.innerHTML = '<option value="">Select Taluka</option>';
                    return;
                }

                fetch(`/get-talukas/${districtId}`)
                    .then(res => res.json())
                    .then(data => {
                        talukaSelect.innerHTML = '<option value="">Select Taluka</option>';

                        data.forEach(t => {
                            talukaSelect.innerHTML += `
                        <option value="${t.id}" ${t.id == selectedTalukaId ? 'selected' : ''}>
                            ${t.name}
                        </option>`;
                        });
                    });
            }

            // District change
            districtSelect.addEventListener('change', function() {
                loadTalukas(this.value);
            });

            // ✅ AUTO LOAD ON EDIT PAGE
            @if (isset($warehouse) && $warehouse->district_id)
                loadTalukas(
                    {{ $warehouse->district_id }},
                    {{ $warehouse->taluka_id ?? 'null' }}
                );
            @endif

        });
    </script>
