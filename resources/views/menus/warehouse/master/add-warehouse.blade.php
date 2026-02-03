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
                                            method="POST"
                                            onsubmit="return validateLocation()">
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
                                                        <option value="distribution_center"
                                                            {{ ($warehouse->type ?? '') == 'distribution_center' ? 'selected' : '' }}>
                                                            Distribution Center</option>
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
                                                            data-district-id="{{ $w->district_id }}"
                                                            data-taluka-id="{{ $w->taluka_id }}"
                                                            data-district="{{ $w->district->name ?? '' }}"
                                                            data-taluka="{{ $w->taluka->name ?? '' }}"
                                                            {{ ($warehouse->parent_id ?? '') == $w->id ? 'selected' : '' }}>

                                                            {{ $w->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>

                                                    <!-- <select name="parent_id" id="parent_id" class="form-select"
                                                    //******************Do Not Remove Advance TO Do Later******************
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Parent</option>
                                                        @foreach ($warehouses as $w)
                                                        <option value="{{ $w->id }}"
                                                            data-type="{{ $w->type }}"
                                                            data-district-id="{{ $w->district_id }}"
                                                            {{ ($warehouse->parent_id ?? '') == $w->id ? 'selected' : '' }}>
                                                            {{ $w->name }} ({{ ucfirst($w->type) }})
                                                        </option>
                                                        @endforeach
                                                    </select> -->

                                                    @error('type')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <input type="hidden"
                                                    id="savedTalukaId"
                                                    value="{{ $warehouse->taluka_id ?? '' }}">

                                                <input type="hidden"
                                                    id="savedDistrictId"
                                                    value="{{ $warehouse->district_id ?? '' }}">

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
                                                    <label class="form-label">
                                                        Taluka <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="taluka_id"
                                                        id="taluka_id"
                                                        class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Taluka</option>
                                                        @foreach ($talukas as $taluka)
                                                        <option value="{{ $taluka->id }}"
                                                            {{ old('taluka_id', $warehouse->taluka_id ?? '') == $taluka->id ? 'selected' : '' }}>
                                                            {{ $taluka->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('taluka_id')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Pincode --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">
                                                        Pincode <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        name="pincode"
                                                        id="pincode"
                                                        maxlength="6"
                                                        class="form-control"
                                                        placeholder="Enter pincode"
                                                        value="{{ old('pincode', $warehouse->pincode ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                </div>

                                                {{-- Service Radius --}}
                                                <div class="col-md-3 mb-3" id="radiusDiv">
                                                    <label class="form-label">
                                                        Service Radius (KM)
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                        min="1"
                                                        name="service_radius_km"
                                                        id="service_radius_km"
                                                        class="form-control"
                                                        value="{{ old('service_radius_km', $warehouse->service_radius_km ?? 10) }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
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

                                                {{-- Latitude / Longitude (hidden) --}}
                                                <input type="hidden" name="latitude" id="latitude"
                                                    value="{{ old('latitude', $warehouse->latitude ?? '') }}">

                                                <input type="hidden" name="longitude" id="longitude"
                                                    value="{{ old('longitude', $warehouse->longitude ?? '') }}">

                                                {{-- Location Picker --}}
                                                <div class="col-md-12 mb-3" id="locationPicker">
                                                    <label class="form-label">
                                                        Warehouse Location (Drag pin to exact location)
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div id="map" style="height: 400px; border-radius: 8px;"></div>

                                                    <small class="text-muted d-block mt-1">
                                                        Selected Location:
                                                        <strong id="coordText"></strong>
                                                    </small>
                                                </div>

                                                @if($mode !== 'add' && $mode !== 'edit')
                                                {{-- GSTIN --}}
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
                                                    @endif
                                                </div>

                                                {{-- Buttons --}}
                                                <div class="col-lg-12">
                                                    <div class="text-end">
                                                        <a href="{{ route('warehouse.index') }}"
                                                            class="btn btn-success">Back</a>
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

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- //************Do Not Remove Advance TO Do Later  ********************** --}}
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {

            const warehouseType = document.getElementById('warehouseType');
            const parentSelect = document.getElementById('parent_id');
            const districtSelect = document.getElementById('district_id');

            function autoSetDistrictFromParent() {
                const selectedParent = parentSelect.options[parentSelect.selectedIndex];
                if (!selectedParent) return;

                const districtId = selectedParent.getAttribute('data-district-id');

                if (districtId) {
                    districtSelect.value = districtId;
                }
            }

            // When warehouse type changes
            warehouseType.addEventListener('change', function() {
                if (this.value === 'district' || this.value === 'taluka') {
                    autoSetDistrictFromParent();
                }
            });

            // When parent warehouse changes
            parentSelect.addEventListener('change', function() {
                if (warehouseType.value === 'district' || warehouseType.value === 'taluka') {
                    autoSetDistrictFromParent();
                }
            });

            // Auto-trigger on edit page load
            if (warehouseType.value === 'district' || warehouseType.value === 'taluka') {
                autoSetDistrictFromParent();
            }
        });
    </script> -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const typeSelect = document.getElementById('warehouseType');
            const parentDiv = document.getElementById('parentDiv');
            const parentSelect = document.getElementById('parent_id');
            const districtSelect = document.getElementById('district_id');
            const talukaSelect = document.getElementById('taluka_id');
            const savedDistrictId = document.getElementById('savedDistrictId').value;
            const savedTalukaId = document.getElementById('savedTalukaId').value;

            if (savedDistrictId) {
                districtSelect.value = savedDistrictId;
                loadTalukas(savedDistrictId, savedTalukaId);
            }


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
                            (type === 'master' || opt.value === '') ? 'block' : 'none';
                    } else if (selectedType === 'taluka') {
                        opt.style.display =
                            (type === 'district' || opt.value === '') ? 'block' : 'none';
                    } else if (selectedType === 'distribution_center') {
                        opt.style.display =
                            (type === 'taluka' || opt.value === '') ? 'block' : 'none';
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
                    <option value="${t.id}" ${selectedTalukaId == t.id ? 'selected' : ''}>
                        ${t.name}
                    </option>`;
                        });
                    })
                    .catch(() => {
                        talukaSelect.innerHTML = '<option value="">Select Taluka</option>';
                    });
            }


            // District change
            districtSelect.addEventListener('change', function() {
                loadTalukas(this.value);
            });


            /*=======================
                    parent location 
                ======================--  */
            function updateParentLabels() {
                const selectedType = typeSelect.value;

                [...parentSelect.options].forEach(option => {
                    if (!option.value) return;

                    const name = option.text.split('(')[0].trim();
                    const district = option.dataset.district;
                    const taluka = option.dataset.taluka;

                    let suffix = '';

                    if (selectedType === 'district') {
                        suffix = district ? ` (${district})` : '';
                    }

                    if (selectedType === 'taluka') {
                        suffix = district ? ` (${district})` : '';
                    }

                    if (selectedType === 'distribution_center') {
                        suffix = taluka ? ` (${taluka})` : '';
                    }

                    option.text = name + suffix;
                });
            }

            typeSelect.addEventListener('change', updateParentLabels);
            updateParentLabels();



            /* ==================================
           Auto Fill Distric And Taluka ON Paret selection 
            ===================================== */

            function resetSelect(select, placeholder) {
                select.value = '';
                if (placeholder) {
                    select.innerHTML = `<option value="">${placeholder}</option>`;
                }
            }
            parentSelect.addEventListener('change', function() {

                const opt = this.options[this.selectedIndex];
                const districtId = opt.dataset.districtId;
                const talukaId = opt.dataset.talukaId;
                const parentType = opt.dataset.type;

                // Auto-select district
                if (districtId) {
                    districtSelect.value = districtId;

                    // IMPORTANT: load talukas AFTER district is set
                    const savedTalukaId = document.getElementById('savedTalukaId').value;

                    if (districtId) {
                        districtSelect.value = districtId;
                        loadTalukas(districtId, savedTalukaId);
                    }

                }
            });

            // Trigger on edit page load
            if (parentSelect.value) {
                parentSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>

    <script>
        let locationConfirmed = false;

        document.addEventListener('DOMContentLoaded', function() {

            const typeSelect = document.getElementById('warehouseType');
            const mapDiv = document.getElementById('locationPicker');
            const radiusDiv = document.getElementById('radiusDiv');

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('service_radius_km');

            const pincodeInput = document.getElementById('pincode');
            const coordText = document.getElementById('coordText');

            /* ===============================
               INITIAL COORDINATES
            =============================== */
            let lat = latInput.value ? parseFloat(latInput.value) : 18.5204;
            let lng = lngInput.value ? parseFloat(lngInput.value) : 73.8567;

            // Force write initial values (VERY IMPORTANT)
            latInput.value = lat;
            lngInput.value = lng;
            updateCoordText(lat, lng);

            /* ===============================
               MAP INIT
            =============================== */
            const map = L.map('map').setView([lat, lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            let circle = null;

            /* ===============================
               HELPERS
            =============================== */
            function updateCoordText(lat, lng) {
                if (coordText) {
                    coordText.innerText = lat.toFixed(6) + ', ' + lng.toFixed(6);
                }
            }

            function syncLatLng() {
                const pos = marker.getLatLng();
                latInput.value = pos.lat;
                lngInput.value = pos.lng;
                updateCoordText(pos.lat, pos.lng);
                locationConfirmed = true;
                fetchPincode(pos.lat, pos.lng);
                updateRadiusCircle();
            }

            /* ===============================
               MARKER EVENTS
            =============================== */
            marker.on('dragend', syncLatLng);

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                syncLatLng();
            });

            /* ===============================
               SERVICE RADIUS CIRCLE
            =============================== */
            function updateRadiusCircle() {
                if (circle) {
                    map.removeLayer(circle);
                    circle = null;
                }

                if (typeSelect.value === 'distribution_center') {
                    circle = L.circle(marker.getLatLng(), {
                        radius: (radiusInput.value || 10) * 1000,
                        color: 'green',
                        fillColor: '#28a745',
                        fillOpacity: 0.25
                    }).addTo(map);
                }
            }

            /* ===============================
               TOGGLE MAP / RADIUS BY TYPE
            =============================== */
            function toggleByType() {
                const type = typeSelect.value;

                if (type === 'distribution_center') {
                    mapDiv.style.display = 'block';
                    radiusDiv.style.display = 'block';
                    updateRadiusCircle();
                } else {
                    mapDiv.style.display = 'none';
                    radiusDiv.style.display = 'none';
                    if (circle) map.removeLayer(circle);
                }

                setTimeout(() => map.invalidateSize(), 200);
            }

            /* ===============================
               FREE PINCODE AUTO-FILL (OSM)
            =============================== */
            function fetchPincode(lat, lng) {
                fetch('/reverse-geocode', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            lat,
                            lng
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.pincode && pincodeInput) {
                            pincodeInput.value = data.pincode;
                        }
                    })
                    .catch(() => {});
            }


            /* ===============================
               EVENTS
            =============================== */
            radiusInput?.addEventListener('input', updateRadiusCircle);
            typeSelect.addEventListener('change', toggleByType);

            /* ===============================
               INITIAL LOAD
            =============================== */
            toggleByType();
        });

        function validateLocation() {
            if (!locationConfirmed) {
                alert('Please select warehouse location on the map');
                return false;
            }
            return true;
        }
    </script>