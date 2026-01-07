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
                                                Add User
                                            @elseif($mode === 'edit')
                                                Edit User
                                            @else
                                                View User
                                            @endif
                                        </h4>
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ $mode === 'edit' ? route('user.update', $user->id) : route('user.store') }}"
                                            method="POST" enctype="multipart/form-data"
                                            {{ $mode === 'view' ? 'onsubmit=return false;' : '' }}>

                                            @csrf
                                            @if ($mode === 'edit')
                                                @method('PUT')
                                            @endif

                                            <div class="row">

                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">
                                                        First Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="first_name" class="form-control" placeholder="Enter first name"
                                                        value="{{ old('first_name', $user->first_name ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    {{-- Field error --}}
                                                    @error('first_name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                {{-- Last Name --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">
                                                        Last Name <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="last_name" class="form-control" placeholder="Enter last name"
                                                        value="{{ old('last_name', $user->last_name ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    {{-- Display error message without red border --}}
                                                    @error('last_name')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                {{-- Mobile --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">
                                                        Mobile <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" name="mobile" class="form-control" placeholder="Enter mobile number"
                                                        value="{{ old('mobile', $user->mobile ?? '') }}"
                                                        oninput="validateMobile(this)"
                                                        onkeypress="return isNumber(event)"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>


                                                    {{-- Display error message without red border --}}
                                                    @error('mobile')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                {{-- Role --}}
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">
                                                        Role <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="role_id" class="form-control form-select" placeholder="Enter role name"
                                                        value="{{ old('role_id', $user->role_id ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Role</option>

                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @if ($mode === 'view')
                                                        <input type="hidden" name="role_id"
                                                            value="{{ $user->role_id }}">
                                                    @endif

                                                    {{-- Display error message without red border --}}
                                                    @error('role_id')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                                                    <select name="warehouse_id" class="form-select">
                                                        <option value="">Select Warehouse</option>
                                                        @foreach ($warehouses as $warehouse)
                                                            <option value="{{ $warehouse->id }}"
                                                                {{ old('warehouse_id', $user->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>
                                                                {{ $warehouse->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>



                                                {{-- Email --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">
                                                        Email <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="email" name="email"  placeholder="Enter email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        value="{{ old('email', $user->email ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Status --}}
                                                <div class="col-md-2 mb-3">
                                                    <label class="form-label">User Active <span
                                                            class="text-danger">*</span></label>

                                                    <select name="status"
                                                        class="form-select @error('status') is-invalid @enderror"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="1"
                                                            {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>
                                                            Yes</option>
                                                        <option value="0"
                                                            {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>
                                                            No</option>
                                                    </select>

                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                {{-- Profile Photo --}}
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Profile Photo</label>

                                                    {{-- Upload only in add/edit --}}
                                                    @if ($mode !== 'view')
                                                        <input type="file" name="profile_photo"
                                                            class="form-control @error('profile_photo') is-invalid @enderror">
                                                    @endif

                                                    {{-- Show existing image as clickable link in EDIT + VIEW --}}
                                                    @if (!empty($user->profile_photo))
                                                        @php
                                                            $photoUrl = asset('storage/' . $user->profile_photo);
                                                        @endphp

                                                        <p class="mt-2">
                                                            <a href="{{ $photoUrl }}" target="_blank"
                                                                class="text-primary">
                                                                View Photo
                                                            </a>
                                                        </p>
                                                    @else
                                                        @if ($mode !== 'add')
                                                            <p class="text-muted">No profile photo uploaded</p>
                                                        @endif
                                                    @endif

                                                    @error('profile_photo')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>



                                                {{-- Buttons --}}
                                                <div class="col-lg-12 text-end">
                                                    <a href="{{ route('user.profile') }}"
                                                        class="btn btn-success">Cancel</a>

                                                    @if ($mode !== 'view')
                                                        <button class="btn btn-success">
                                                            {{ $mode === 'edit' ? 'Update User' : 'Save User' }}
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

                    <br>
                    <br>
                    @include('layouts.footer')
                </div>

                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

    </div>
    <!-- / Layout wrapper -->
</body>








<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function isNumber(evt) {
        const charCode = evt.which ? evt.which : evt.keyCode;

        // Allow only numbers
        if (charCode < 48 || charCode > 57) {
            return false;
        }

        // Stop typing after 10 digits
        const input = evt.target;
        if (input.value.length >= 10) {
            return false;
        }

        return true;
    }

    function validateMobile(input) {
        // Remove non-digits (paste protection)
        input.value = input.value.replace(/\D/g, '');

        // Trim to 10 digits
        if (input.value.length > 10) {
            input.value = input.value.slice(0, 10);
        }

        const errorDiv = document.getElementById('mobileError');

        if (input.value.length !== 10) {
            errorDiv.innerText = 'Mobile number must be exactly 10 digits';
        } else {
            errorDiv.innerText = '';
        }
    }
</script>
