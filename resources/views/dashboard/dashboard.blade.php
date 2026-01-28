@include('layouts.header')

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <style>
        .stat-card {
          height: 130px;
          border-radius: 12px;
          transition: 0.3s ease;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-card p {
          font-size: 14px;
          color: #6c757d;
          margin-bottom: 4px;
        }

        .stat-card h3 {
          font-weight: 700;
          margin: 0;
        }

        .warehouse-scroll {
          max-height: 260px;
          overflow: hidden;
          position: relative;
        }

        .warehouse-scroll ul {
          animation: autoScroll 2s linear infinite;
        }

        @keyframes autoScroll {
          0% {
            transform: translateY(0);
          }

          100% {
            transform: translateY(-50%);
          }
        }
        .stat-card .card-body {
          height: 100%;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }

      </style>
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

            <!-- STAT CARDS -->
            <div class="row g-3">

              <div class="col-xl-6 col-lg-4 col-md-6 col-sm-12">
                <a href="{{ route('warehouse.transfer.index') }}" class="text-decoration-none">
                <div class="card stat-card border-warning">
                  <div class="card-body">
                    <p>Pending Transfer Requests</p>
                    <h3 class="text-warning">{{ $pendingTransferCount }}</h3>
                    <small class="text-muted">
                      Warehouse → Warehouse
                    </small>
                  </div>
                </div>
                </a>
              </div>

              <div class="col-xl-6 col-lg-4 col-md-6 col-sm-12">
                <a href="{{ route('batches.expiry') }}" class="text-decoration-none">
                  <div class="card stat-card">
                    <div class="card-body">
                      <p>Expired Batches</p>
                      <h3>{{ $expiredCount }}</h3>
                      <small class="text-warning">
                          Expiring in 7 days: {{ $expiringSoonCount }}
                        </small>
                    </div>
                  </div>
                </a>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <a href="{{ route('category.index') }}" class="text-decoration-none">
                <div class="card stat-card border-warning">
                  <div class="card-body">
                    <p>Total Categories</p>
                    <h3 class="text-warning">{{ $categoryCount }}</h3>
                  </div>
                </div>
                </a>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <a href="{{ route('product.index') }}" class="text-decoration-none">
                <div class="card stat-card border-warning">
                  <div class="card-body">
                    <p>Total Products</p>
                    <h3 class="text-warning">{{ $ProductCount }}</h3>
                  </div>
                </div>
                </a>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <a href="{{ route('product.index') }}" class="text-decoration-none">
                <div class="card stat-card border-warning">
                  <div class="card-body">
                    <p>Total Users</p>
                    <h3 class="text-warning">{{ $UserCount }}</h3>
                  </div>
                </div>
                </a>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="card-body">
                    <p>Total Batches</p>
                    <h3 class="text-warning">{{ $BatchCount }}</h3>
                  </div>
                </div>
              </div>

            </div>

            <!-- SECOND ROW -->
            <div class="row g-3 mt-3">

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="card-body">
                    <p>Total Warehouses</p>
                    <h3 class="text-warning">{{ $WarehouseCount }}</h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="card-body">
                    <p>Total Stock</p>
                    <h3 class="text-warning">{{ $StockMovementCount }}</h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="card-body">
                    <p>Warehouse Transfers</p>
                    <h3 class="text-warning">{{ $WarehouseTransferCount }}</h3>
                  </div>
                </div>
              </div>       

            </div>

            <!-- WAREHOUSE LIST + LOW STOCK -->
            <div class="row g-3 mt-4">

              <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                  <div class="card-body">
                    <h5 class="mb-3">All Warehouses</h5>

                    <div class="warehouse-scroll">
                      <ul class="list-group list-group-flush">
                        @forelse($warehouseDistrict as $warehouse)
                        <li class="list-group-item">{{ $warehouse }}</li>
                        @empty
                        <li class="list-group-item text-muted">No Warehouses Found</li>
                        @endforelse
                      </ul>
                    </div>

                  </div>
                </div>
              </div>


              <div class="col-lg-8 col-md-12">
                <div class="card h-100">
                  <div class="card-body">
                    <h5 class="mb-3">Low Stock Products</h5>

                    <div class="row mb-3">
                      <div class="col-md-4 text-center">
                        <h6>Total Low Stock</h6>
                        <h2 class="text-danger">{{ $totalLowStock }}</h2>
                      </div>
                    </div>

                    <div class="table-responsive">
                      <table class="table table-sm">
                        <thead>
                          <tr>
                            <th>Warehouse</th>
                            <th>Low Stock Count</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($warehouseWise as $row)
                          <tr>
                            <td>{{ $row->warehouse->name ?? '-' }}</td>
                            <td class="text-danger fw-bold">{{ $row->total }}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
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