@include('layouts.header')

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

        @include('layouts.sidebar')

        <div class="layout-page">

            @include('layouts.navbar')

            <div class="content-wrapper">

                <div class="container-xxl flex-grow-1 container-p-y">

                    <div class="row justify-content-center my-4">

                        <div class="col-12 px-4">

                            <div class="card shadow-sm border-0 rounded-3 mx-auto"
                                style="max-width:100%;">

                                <div class="card-header bg-white py-4 px-5">

                                    <h4 class="mb-0">
                                        {{ isset($retailer) ? 'Edit Retailer' : 'Create Retailer' }}
                                    </h4>

                                </div>

                                <div class="card-body px-5 py-4">

                                    <form method="POST"
                                        action="{{ isset($retailer)
                                            ? route('retailers.update',$retailer->id)
                                            : route('retailers.store') }}"
                                        class="mt-3">

                                        @csrf

                                        @if(isset($retailer))
                                        @method('PUT')
                                        @endif


                                        <div class="row g-3">

                                            {{-- Name --}}
                                            <div class="col-lg-6 col-md-6">
                                                <label class="form-label">
                                                    Name
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name',$retailer->name ?? '') }}"
                                                    placeholder="Enter Retailer Name">

                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                            {{-- Mobile --}}
                                            <div class="col-lg-6 col-md-6">
                                                <label class="form-label">
                                                    Mobile
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input type="text"
                                                    maxlength="10"
                                                    name="mobile"
                                                    class="form-control @error('mobile') is-invalid @enderror"
                                                    value="{{ old('mobile',$retailer->mobile ?? '') }}"
                                                    placeholder="Enter Mobile Number">

                                                @error('mobile')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                            {{-- Email --}}
                                            <div class="col-lg-6 col-md-6">
                                                <label class="form-label">
                                                    Email
                                                </label>

                                                <input type="email"
                                                    name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email',$retailer->email ?? '') }}"
                                                    placeholder="Enter Email">

                                                @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                            {{-- Shop Name --}}
                                            <div class="col-lg-6 col-md-6">
                                                <label class="form-label">
                                                    Shop Name
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <select class="form-select" disabled>
                                                    @foreach($shops as $shop)
                                                        <option value="{{ $shop->id }}" selected>
                                                            {{ $shop->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <input type="hidden"
                                                    name="shop_id"
                                                    value="{{ $shops->first()->id ?? '' }}">

                                                <input type="hidden"
                                                    name="shop_name"
                                                    value="{{ $shops->first()->name ?? '' }}">

                                                @error('shop_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- DOB --}}
                                            <div class="col-lg-6 col-md-6">
                                                <label class="form-label">
                                                    Date of Birth
                                                </label>

                                                <input type="date"
                                                    name="dob"
                                                    class="form-control"
                                                    value="{{ old('dob',$retailer->dob ?? '') }}">
                                            </div>


                                            {{-- Gender --}}
                                            <div class="col-lg-6 col-md-6">

                                                <label class="form-label">
                                                    Gender
                                                </label>

                                                <select name="gender" class="form-select">

                                                    <option value="">Select Gender</option>

                                                    <option value="male"
                                                        {{ old('gender',$retailer->gender ?? '')=='male' ? 'selected' : '' }}>
                                                        Male
                                                    </option>

                                                    <option value="female"
                                                        {{ old('gender',$retailer->gender ?? '')=='female' ? 'selected' : '' }}>
                                                        Female
                                                    </option>

                                                </select>

                                            </div>


                                            {{-- GST --}}
                                            <div class="col-lg-6 col-md-6">

                                                <label class="form-label">
                                                    GST Number
                                                </label>

                                                <input type="text"
                                                    name="gst_number"
                                                    class="form-control"
                                                    value="{{ old('gst_number',$retailer->gst_number ?? '') }}"
                                                    placeholder="Enter GST Number">

                                            </div>


                                            {{-- Status --}}
                                            <div class="col-lg-6 col-md-6">

                                                <label class="form-label">
                                                    Status
                                                </label>

                                                <select class="form-select" name="is_active">

                                                    <option value="1"
                                                        {{ old('is_active',$retailer->is_active ?? 1)==1 ? 'selected' : '' }}>
                                                        Active
                                                    </option>

                                                    <option value="0"
                                                        {{ old('is_active',$retailer->is_active ?? '')==0 ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>

                                                </select>

                                            </div>


                                            {{-- Address --}}
                                            <div class="col-12">

                                                <label class="form-label">
                                                    Address
                                                </label>

                                                <textarea
                                                    name="address"
                                                    rows="4"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    placeholder="Enter Address">{{ old('address',$retailer->address ?? '') }}</textarea>

                                                @error('address')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </div>

                                        </div>


                                        <hr class="my-4">


                                        <div class="row mt-4">

                                            <div class="col-12 text-end">

                                                <a href="{{ route('retailers.index') }}"
                                                    class="btn btn-success">

                                                    <i class="bx bx-arrow-back"></i>
                                                    Back

                                                </a>

                                                <button type="submit"
                                                    class="btn btn-success">

                                                    <i class="bx bx-save"></i>
                                                    {{ isset($retailer) ? 'Update Retailer' : 'Save Retailer' }}

                                                </button>

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

        </div>

    </div>
</div>