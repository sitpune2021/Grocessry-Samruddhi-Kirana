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
                                    <div class="card-body">
                                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        <form>
                                            <div class="row g-3">
                                                {{-- Shop Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label">
                                                        Customer <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        value="   {{ $return->customer->first_name ?? '-' }}">
                                                </div>

                                                {{-- Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Order Number <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        value="{{ $return->order->order_number ?? '-' }}">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Product<span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        value="{{ $return->product->name ?? '-' }}">
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Quantity <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        value="{{ $return->quantity }}">
                                                </div>

                                                {{-- Email --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Reason
                                                    </label>
                                                    <input type="text"

                                                        class="form-control"

                                                        value="{{ $return->reason }}">

                                                </div>

                                                {{-- DOB --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Status
                                                    </label>
                                                    <input type="text"
                                                        class="form-control"
                                                        value="{{ ucfirst($return->status) }}">
                                                </div>

                                                {{-- Gender --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium d-inline-flex align-items-center gap-2">
                                                        QC Status
                                                        <span class="badge {{
                                                        match (strtolower($return->qc_status ?? 'pending')) {
                                                        'approved'  => 'bg-success',
                                                        'rejected'  => 'bg-danger',
                                                        'in_review' => 'bg-warning text-dark',
                                                        'received'  => 'bg-info',
                                                         default     => 'bg-secondary',
                                                        }
                                                        }}">
                                                            {{ ucfirst(str_replace('_', ' ', $return->qc_status ?? 'pending')) }}
                                                        </span>
                                                    </label>
                                                </div>

                                            </div>

                                            {{-- Buttons --}}
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('customer-returns.index') }}"
                                                    class="btn btn-success">
                                                    Back
                                                </a>
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