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
                        <div class="row justify-content-center">
                            <div class="col-12">

                                <div class="card shadow-sm border-0 rounded-3">

                                    <div class="card-header d-flex align-items-center">
                                        <h4 class="mb-0 flex-grow-1">
                                            {{ isset($shop) ? 'Update Shop' : 'Create Shop' }}
                                        </h4>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form method="POST"
                                            action="{{ isset($shop)
                                                    ? route('grocery-shops.update', $shop->id)
                                                    : route('grocery-shops.store') }}">

                                            @csrf
                                            @if(isset($shop))
                                            @method('PUT')
                                            @endif

                                            {{-- Row 1 --}}
                                            <div class="row">

                                                {{-- District --}}
                                                <div class="col-md-6 col-12 mb-3">
                                                    <label class="form-label">District Warehouse</label>
                                                    <select name="district_warehouse_id" id="district_id" class="form-select">
                                                        <option value="">Select District Warehouse</option>

                                                        @foreach($districtWarehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}">
                                                            {{ $warehouse->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>



                                                    @error('district_warehouse_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>



                                                {{-- Taluka --}}
                                                <div class="col-md-6 col-12 mb-3">
                                                    <label class="form-label">Taluka Warehouse</label>
                                                    <select name="taluka_id" id="taluka_id" class="form-select">
                                                        <option value="">Select Taluka</option>
                                                    </select>
                                                    @error('taluka_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            </div>


                                            {{-- Row 2 --}}
                                            <div class="row">
                                                <div class="col-md-6 col-12 mb-3">
                                                    <label class="form-label">Shop Name</label>
                                                    <input type="text"
                                                        id="shop_name"
                                                        name="shop_name"
                                                        class="form-control"
                                                        placeholder="Shop Name"
                                                        value="{{ old('shop_name', $shop->shop_name ?? '') }}">
                                                    @error('shop_name')
                                                    <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 col-12 mb-3">
                                                    <label class="form-label">Owner Name</label>
                                                    <input type="text"
                                                        id="owner_name"
                                                        name="owner_name"
                                                        class="form-control"
                                                        placeholder="Owner Name"
                                                        value="{{ old('owner_name', $shop->owner_name ?? '') }}">
                                                    @error('owner_name')
                                                    <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>


                                            {{-- Row 3 --}}
                                            <div class="row">


                                                <div class="col-md-6 col-12 mb-3">
                                                    <label class="form-label">Mobile No</label>
                                                    <input type="text"
                                                        name="mobile_no"
                                                        maxlength="10"
                                                        class="form-control"
                                                        placeholder="Mobile No"
                                                        value="{{ old('mobile_no', $shop->mobile_no ?? '') }}">
                                                    @error('mobile_no')
                                                    <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <div class="col-md-6 col-12 mb-3">
                                                    <label class="form-label">Address</label>
                                                    <textarea name="address"
                                                        id="address"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Address">{{ old('address', $shop->address ?? '') }}</textarea>

                                                    @error('address')
                                                    <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>


                                            <!-- Buttons -->
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('grocery-shops.index') }}" class="btn btn-success">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                <button type="submit" class="btn btn-success">
                                                    {{ isset($shop) ? 'Update Shop' : 'Save Shop' }}
                                                </button>
                                            </div>

                                        </form>
                                    </div>

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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    $(document).ready(function() {


        $('#district_id').on('change', function() {


            let districtWarehouseId = $(this).val();
            let talukaSelect = $('#taluka_id');

            talukaSelect.html('<option value="">Select Taluka</option>');

            if (!districtWarehouseId) return;

            talukaSelect.html('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ url('grocery-shops/get-taluka-warehouses') }}/" + districtWarehouseId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let options = '<option value="">Select Taluka Warehouse</option>';

                    if (data.length > 0) {
                        $.each(data, function(index, warehouse) {
                            options += `
                    <option value="${warehouse.id}">
                        ${warehouse.name}
                    </option>`;
                        });
                    } else {
                        options += '<option value="">No Taluka Warehouse Found</option>';
                    }

                    $('#taluka_id').html(options);
                }
            });

        });
    });
</script>



<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {

        const districtSelect = document.getElementById('district_id');
        const talukaSelect = document.getElementById('taluka_id');
        const selectedTaluka = "{{ old('taluka_id', $shop->taluka_id ?? '') }}";

        function loadTalukas(districtId) {
            talukaSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/talukas/by-district/${districtId}`)
                .then(res => res.json())
                .then(data => {
                    talukaSelect.innerHTML = '<option value="">Select Taluka</option>';

                    data.forEach(taluka => {
                        const option = document.createElement('option');
                        option.value = taluka.id;
                        option.text = taluka.name;

                        if (taluka.id == selectedTaluka) {
                            option.selected = true;
                        }

                        talukaSelect.appendChild(option);
                    });
                });
        }

        districtSelect.addEventListener('change', function() {
            if (this.value) {
                loadTalukas(this.value);
            } else {
                talukaSelect.innerHTML = '<option value="">Select Taluka</option>';
            }
        });

        // EDIT time auto load
        if (districtSelect.value) {
            loadTalukas(districtSelect.value);
        }
    });
</script> -->