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

                            <div class="col-12">
                                <div class="card shadow-sm border-0 rounded-3">

                                    {{-- Card Header --}}
                                    <div class="card-header bg-white fw-semibold">
                                        <i class="bx bx-category me-1"></i>
                                        @if ($mode === 'add')
                                        Add Agent
                                        @elseif($mode === 'edit')
                                        Edit Agent
                                        @else
                                        View Agent
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($driverVehicle) ? route('delivery-agents.update', $driverVehicle->id) : route('delivery-agents.store') }}"
                                            method="POST">
                                            @csrf
                                            @if (isset($driverVehicle))
                                            @method('PUT')
                                            @endif

                                            <div class="row g-3">

                                                {{-- Agent / Driver Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        name="name"
                                                        class="form-control"
                                                        value="{{ old('name', $agent->name ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Vehicle No --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Mobile <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        name="mobile"
                                                        class="form-control"
                                                        value="{{ old('mobile', $agent->mobile ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('mobile')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Vehicle Type --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Email
                                                    </label>
                                                    <input type="email"
                                                        name="email"
                                                        class="form-control"
                                                        value="{{ old('email', $agent->email ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('email')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror

                                                </div>

                                                {{-- License No --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Vehicle Type</label>
                                                    <input type="text"
                                                        name="vehicle_type"
                                                        class="form-control"
                                                        value="{{ old('vehicle_type', $agent->vehicle_type ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                </div>

                                                {{-- Active Status --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium d-block">Active <span
                                                            class="text-danger">*</span></label>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="active_status" value="1"
                                                            {{ old('active_status', $driverVehicle->active_status ?? '') == 1 ? 'checked' : '' }}
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <label class="form-check-label">Yes</label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="active_status" value="0" checked
                                                            {{ old('active_status', $driverVehicle->active_status ?? '') == 0 ? 'checked' : '' }}
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <label class="form-check-label">No</label>
                                                    </div>

                                                    @error('active_status')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>


                                            {{-- Buttons --}}
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('delivery-agents.index') }}"
                                                    class="btn btn-outline-secondary">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-primary">Save Agent</button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-primary">Update Agent</button>
                                                @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="slug"]');

        nameInput.addEventListener('keyup', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = generateSlug(this.value);
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.manual = true;
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });
</script>