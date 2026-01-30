@include('layouts.header')

@php
    $isEdit = isset($shop);
    $isShow = request()->routeIs('grocery-shops.show');
@endphp

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl container-p-y">

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    {{ $isShow ? 'View Shop' : ($isEdit ? 'Update Shop' : 'Create Shop') }}
                                </h4>
                            </div>

                            <div class="card-body">
                                <form method="POST"
                                    action="{{ $isEdit ? route('grocery-shops.update', $shop->id) : route('grocery-shops.store') }}">
                                    @csrf
                                    @if ($isEdit)
                                        @method('PUT')
                                    @endif

                                    {{-- Row 1 --}}
                                    <div class="row">
                                        {{-- District --}}
                                        <div class="col-md-6 col-12 mb-3">
                                            <label class="form-label">District Warehouse</label>
                                            <select name="district_warehouse_id" id="district_id" class="form-select"
                                                {{ $isShow ? 'disabled' : '' }}>
                                                <option value="">Select District Warehouse</option>
                                                @foreach ($districtWarehouses as $dw)
                                                    <option value="{{ $dw->id }}"
                                                        {{ old('district_warehouse_id', $selectedDistrict ?? '') == $dw->id ? 'selected' : '' }}>
                                                        {{ $dw->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('district_warehouse_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Taluka --}}
                                        <div class="col-md-6 col-12 mb-3">
                                            <label class="form-label">Taluka Warehouse</label>
                                            <select name="taluka_id" id="taluka_id" class="form-select"
                                                {{ $isShow ? 'disabled' : '' }}>
                                                <option value="">Select Taluka</option>
                                            </select>
                                            @error('taluka_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-3">
                                            <label class="form-label">Shop Name</label>
                                            <input type="text" name="shop_name" class="form-control"
                                                value="{{ old('shop_name', $shop->shop_name ?? '') }}"
                                                {{ $isShow ? 'readonly' : '' }}>
                                            @error('shop_name')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 col-12 mb-3">
                                            <label class="form-label">Owner Name</label>
                                            <input type="text" name="owner_name" class="form-control"
                                                value="{{ old('owner_name', $shop->owner_name ?? '') }}"
                                                {{ $isShow ? 'readonly' : '' }}>
                                            @error('owner_name')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Row 3 --}}
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-3">
                                            <label class="form-label">Mobile No</label>
                                            <input type="text" name="mobile_no" maxlength="10" class="form-control"
                                                value="{{ old('mobile_no', $shop->mobile_no ?? '') }}"
                                                {{ $isShow ? 'readonly' : '' }}>
                                            @error('mobile_no')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 col-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea name="address" class="form-control" rows="2" {{ $isShow ? 'readonly' : '' }}>{{ old('address', $shop->address ?? '') }}</textarea>
                                            @error('address')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="mt-4 d-flex justify-content-end gap-2 text-end">
                                        <a href="{{ route('grocery-shops.index') }}" class="btn btn-success">
                                             Back
                                        </a>
                                        @if (!$isShow)
                                            <button type="submit" class="btn btn-success">
                                                {{ $isEdit ? 'Update Shop' : 'Save Shop' }}
                                            </button>
                                        @endif
                                    </div>

                                </form>
                            </div>

                        </div>

                    </div>
                </div>

                @include('layouts.footer')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            let selectedTaluka = "{{ old('taluka_id', $selectedTaluka ?? '') }}";

            function loadTaluka(districtId) {
                if (!districtId) return;
                $('#taluka_id').html('<option>Loading...</option>');

                $.get("{{ url('grocery-shops/get-taluka-warehouses') }}/" + districtId, function(data) {
                    let options = '<option value="">Select Taluka</option>';
                    data.forEach(w => {
                        options +=
                            `<option value="${w.id}" ${w.id == selectedTaluka ? 'selected' : ''}>${w.name}</option>`;
                    });
                    $('#taluka_id').html(options);
                });
            }

            $('#district_id').change(function() {
                loadTaluka(this.value);
            });

            // Preload Taluka on Edit/Show
            if ($('#district_id').val()) {
                loadTaluka($('#district_id').val());
            }
        });
    </script>

</body>
