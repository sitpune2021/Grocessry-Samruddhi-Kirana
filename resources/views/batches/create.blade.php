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

                            <!-- Form controls -->
                            <div class="col-md-6">
                                <div class="card">
                                    <h4 class="card-header">                                       
                                        Add Batch                                    
                                    </h4>
                                    <div class="card-body">


                                        <form method="POST" action="/batches">
                                            @csrf

                                            
                                            <label for="product_id">Product</label>
                                            <div class="form-floating mb-4">  
                                                <select name="product_id" 
                                                    class="form-control @error('product_id') is-invalid @enderror"
                                                    id="product_id">
                                                    <option value="">-- Select Product --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('product_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            
                                            <!-- Batch Number -->
                                            <label for="batch_no">Batch Number</label>
                                            <div class="form-floating mb-4">                        
                                                <input
                                                    type="text"
                                                    name="name"
                                                    class="form-control @error('batch_no') is-invalid @enderror"
                                                    id="batch_no"
                                                    value=""
                                                    placeholder="Batch Number">                                                
                                                @error('batch_no')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <label for="mfg_date">MFG Date</label>
                                            <div class="form-floating mb-4"> 
                                                <input type="date" class="form-control @error('mfg_date') is-invalid @enderror"
                                                        id="mfg_date"
                                                        value="" name="mfg_date">
                                                    @error('mfg_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                            </div>

                                            <label for="expiry_date">Expiry Date</label>
                                            <div class="form-floating mb-4"> 
                                                <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                                                    id="expiry_date"
                                                    value="" name="expiry_date">
                                                @error('expiry_date')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <label for="quantity">Quantity</label>
                                            <div class="form-floating mb-4"> 
                                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                                    id="quantity"
                                                    value="" name="quantity" min="1">
                                                    @error('quantity')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                            </div>

                                            
                                            <!-- Submit Button -->
                                            <div class="text-end">
                                                {{-- Back Button (Always visible) --}}
                                                <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary">
                                                    Back
                                                </a>

                                                <button type="submit" class="btn btn-outline-primary">
                                                    Save Batch
                                                </button>                                                       
                                            </div>
                                        </form>

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