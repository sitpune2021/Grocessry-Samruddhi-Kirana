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
        <!-- / Navbar -->
        @include('layouts.navbar')

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
                <div class="row">
                  <div class="col-lg-4 col-md-12 col-4 mb-4">
                    <a href="{{ route('category.index') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total Categories</p>
                          <h4 class="card-title mb-3">
                            {{ $categoryCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-4 col-md-12 col-4 mb-4">
                    <a href="{{ route('product.index') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total Products</p>
                          <h4 class="card-title mb-3">
                            {{ $ProductCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <a href="{{ route('batches.expiry') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Expiry Alerts</p>
                          <div style="display: flex; align-items: center; ">
                            <h4 class="card-title mb-3 text-danger">
                              {{ $expiredCount }}
                            </h4>
                            <small class="text-warning" style="margin-left: 10px;">
                              Expiring in 7 Days: {{ $expiringSoonCount }}
                            </small>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                </div>
              </div>


              <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
                <div class="row">
                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href="{{ route('batches.index') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total Batchs</p>
                          <h4 class="card-title mb-3">
                            {{ $BatchCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href="{{ route('index.addStock.warehouse') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total Warehouse</p>
                          <h4 class="card-title mb-3">
                            {{ $WarehouseCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href="{{ route('sale.store') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total Stock</p>
                          <h4 class="card-title mb-3">
                            {{ $StockMovementCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href="{{ route('transfer.index') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total W/H Transfer</p>
                          <h4 class="card-title mb-3">
                            {{ $WarehouseTransferCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href="{{ route('stock-returns.index') }}" class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Warehouse Stock Returns</p>
                          <h4 class="card-title mb-3">
                           {{ $warehouseStockReturnCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href=" " class="text-decoration-none">
                      <div class="card h-80 cursor-pointer">
                        <div class="card-body">
                          <p class="mb-1 text-muted">Total Users</p>
                          <h4 class="card-title mb-3">
                            {{ $UserCount }}
                          </h4>
                        </div>
                      </div>
                    </a>
                  </div>

                </div>
              </div>
             
              <!--/ Total Revenue -->
              <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-3 order-md-2 profile-report">
                <div class="row">
                  <div class="col-6 mb-6 payments">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title align-items-start justify-content-between mb-4"> All Warehouse
                        </div>
                        <div class="warehouse-list">
                          <ul class="list-group">
                            @foreach($warehouseDistrict as $warehouse)
                            <li class="list-group-item">{{ $warehouse }}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          
          </div>
        </div>
        <!-- / Content -->

        @include('layouts.footer')

        <div class="content-backdrop fade"></div>
      </div>
      <!-- Content wrapper -->
    </div>
    <!-- / Layout page -->
  </div>

  <!-- Overlay -->
  <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->
  <!-- Core JS -->
  <!-- Core JS -->
  <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>

  <!-- Vendors JS -->
  <script src="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/js/menu.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

  <!-- Main JS -->
  <script src="{{ asset('admin/assets/js/main.js') }}"></script>

  <!-- Page JS -->
  <script src="{{ asset('admin/assets/js/dashboards-analytics.js') }}"></script>

  <!-- GitHub Buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>


</body>

</html>