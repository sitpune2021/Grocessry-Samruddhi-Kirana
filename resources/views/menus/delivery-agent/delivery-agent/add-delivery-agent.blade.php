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
                                        <form enctype="multipart/form-data"
                                            action="{{ isset($agent) ? route('delivery-agents.update', $agent->id) : route('delivery-agents.store') }}"
                                            method="POST">
                                            @csrf
                                            @if (isset($agent))
                                                @method('PUT')
                                            @endif

                                            <div class="row g-3">

                                                {{-- Shop Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label">
                                                        Shop Name <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="warehouse_id" id="warehouse_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                        <option value="">Select Shop</option>

                                                        @foreach ($shops as $shop)
                                                            <option value="{{ $shop->id }}"
                                                                {{ old('warehouse_id', $agent->warehouse_id ?? '') == $shop->id ? 'selected' : '' }}>
                                                                {{ $shop->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>

                                                    @error('shop_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                {{-- Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        First Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="name" name="name"
                                                        class="form-control" placeholder="Enter first name"
                                                        value="{{ old('name', $agent->user->first_name ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Last Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="last_name" name="last_name"
                                                        class="form-control" placeholder="Enter last name"
                                                        value="{{ old('last_name', $agent->user->last_name ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('last_name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                {{-- DOB --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Date of Birth
                                                    </label>
                                                    <input type="date" name="dob" class="form-control"
                                                        value="{{ old('dob', $agent->dob ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('dob')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Gender --}}
                                                <div class="col-md-4">
                                                    <label class="form-label mt-1 fw-medium d-block">
                                                        Gender
                                                    </label>

                                                    <div class="form-check mt-2 form-check-inline">
                                                        <input class="form-check-input" type="radio" name="gender"
                                                            value="male"
                                                            {{ old('gender', $agent->gender ?? '') === 'male' ? 'checked' : '' }}
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <label class="form-check-label">Male</label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="gender"
                                                            value="female"
                                                            {{ old('gender', $agent->gender ?? '') === 'female' ? 'checked' : '' }}
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <label class="form-check-label">Female</label>
                                                    </div>

                                                    @error('gender')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Mobile <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" maxlength="10" name="mobile"
                                                        class="form-control" placeholder="Enter mobile number"
                                                        value="{{ old('mobile', $agent->user->mobile ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('mobile')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Email --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Email
                                                    </label>
                                                    <input type="email" name="email" class="form-control"
                                                        placeholder="Enter email "
                                                        value="{{ old('email', $agent->user->email ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('email')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Address --}}
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium">Address</label>
                                                    <textarea name="address" class="form-control" rows="3" placeholder="Enter address"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>{{ old('address', $agent->address ?? '') }}</textarea>

                                                    @error('address')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Profile Image --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Profile Image</label>

                                                    {{-- SHOW FILE INPUT IN CREATE & EDIT --}}
                                                    @if ($mode !== 'view')
                                                        <input type="file" name="profile_photo" class="form-control"
                                                            accept="image/*">
                                                    @endif

                                                    {{-- SHOW IMAGE IF EXISTS --}}
                                                    @if (!empty($agent?->user?->profile_photo))
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/profile_photos/' . $agent->user->profile_photo) }}"
                                                                target="_self" class="text-primary">
                                                                View Profile Image
                                                            </a>
                                                        </div>
                                                    @else
                                                        {{-- SHOW "NO IMAGE" ONLY IN VIEW --}}
                                                        @if ($mode === 'view')
                                                            <div class="text-muted mt-1">No Profile Image</div>
                                                        @endif
                                                    @endif
                                                </div>

                                                {{-- Aadhaar Card --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Aadhaar Card
                                                    </label>


                                                    @if ($mode !== 'view')
                                                        <input type="file" name="aadhaar_card"
                                                            class="form-control" accept="image/*,.pdf">
                                                    @endif

                                                    @error('aadhaar_card')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror


                                                    @if (!empty($agent->aadhaar_card))
                                                        <div class="mb-2">
                                                            <a href="{{ asset('storage/delivery_agents/aadhaar/' . $agent->aadhaar_card) }}"
                                                                class="img-thumbnail" width="120">
                                                                View Aadhaar Card

                                                            </a>
                                                        </div>
                                                    @endif
                                                    {{-- SHOW "NO IMAGE" ONLY IN VIEW MODE --}}
                                                    @if ($mode === 'view')
                                                        <div class="text-muted mt-1">No Aadhaar Card</div>
                                                    @endif
                                                </div>

                                                {{-- Driving License --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Driving License
                                                    </label>

                                                    {{-- Show existing file in edit/view mode --}}


                                                    {{-- File input for add/edit mode --}}
                                                    @if ($mode !== 'view')
                                                        <input type="file" name="driving_license"
                                                            class="form-control" accept="image/*,.pdf">
                                                    @endif

                                                    @error('driving_license')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror

                                                    @if (!empty($agent->driving_license))
                                                        <div class="mb-2">
                                                            <a href="{{ asset('storage/delivery_agents/license/' . $agent->driving_license) }}"
                                                                class="img-thumbnail" width="120">
                                                                View Driving License
                                                            </a>
                                                        </div>
                                                    @endif
                                                    {{-- SHOW "NO IMAGE" ONLY IN VIEW MODE --}}
                                                    @if ($mode === 'view')
                                                        <div class="text-muted mt-1">No Driving License</div>
                                                    @endif
                                                </div>


                                                {{-- Active Status --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium d-block">
                                                        Active Status <span class="text-danger">*</span>
                                                    </label>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="active_status" value="1"
                                                            {{ old('active_status', $agent->active_status ?? 1) == 1 ? 'checked' : '' }}
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <label class="form-check-label">Yes</label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="active_status" value="0"
                                                            {{ old('active_status', $agent->active_status ?? '') == 0 ? 'checked' : '' }}
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
                                                    class="btn btn-success">
                                                    Back
                                                </a>

                                                @if ($mode === 'add')
                                                    <button type="submit" class="btn btn-success">Save Agent</button>
                                                @elseif($mode === 'edit')
                                                    <button type="submit" class="btn btn-primary">Update
                                                        Agent</button>
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
