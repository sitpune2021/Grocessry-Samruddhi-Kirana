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
          animation: autoScroll 5s linear infinite;
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
                   <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
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
                     <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
                    <div class="card-body">
                      <p>Expired Batches</p>
                      <h3 class="text-warning">{{ $expiredCount }}</h3>
                      <small class="text-success">
                          Expiring in 7 days: {{ $expiringSoonCount }}
                        </small>
                    </div>
                  </div>
                </a>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
                  <div class="card-body">
                    <p>Total Warehouses</p>
                    <h3 class="text-warning">{{ $WarehouseCount }}</h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
                  <div class="card-body">
                    <p>Total Stock</p>
                    <h3 class="text-warning">{{ $StockMovementCount }}</h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
                  <div class="card-body">
                    <p>Warehouse Transfers</p>
                    <h3 class="text-warning">{{ $WarehouseTransferCount }}</h3>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card border-0 shadow-sm" style="background:linear-gradient(135deg,#fff,#fff5f5);">
                  <div class="card-body position-relative" style="height:114px;">
                      <!-- Top color strip -->
                      <div class="position-absolute top-0 start-0 w-100"
                          style="height:4px; background:gray;"></div>

                      <div class="d-flex justify-content-between align-items-center h-100">
                        <div>
                            <p class="mb-1 text-muted fw-semibold">
                                Today Dispatch
                            </p>
                            <h3 class="text-danger mb-0">
                                {{ $todayDispatchCount }}
                            </h3>
                            <small class="text-muted">
                                Qty: {{ $todayDispatchQty }}
                            </small>
                        </div>
                      </div>
                  </div>
                </div>
              </div>

            </div>           

            <!-- WAREHOUSE LIST + STOCK UTILIZATION -->
            <div class="row g-3 mt-4">

              <!-- Left: Warehouse List -->
              <div class="col-lg-7 col-md-12">
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

              <!-- Right: Stock Utilization -->
              <div class="col-lg-5 col-md-12">
                <div class="card h-100">
                  <div class="card-body text-center">
                    <h6 class="mb-3">Stock Utilization</h6>

                    <div style="height:160px">
                      <canvas id="stockUtilizationChart"></canvas>
                    </div>

                    <strong class="mt-2 d-block">
                      {{ $stockUtilization }}% Used
                    </strong>
                  </div>
                </div>
              </div>

            </div>

            <!-- Full width: IN vs OUT Trend -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="mb-3">Stock IN vs OUT (Last 7 days)</h5>
                    <canvas id="inOutChart" height="150"></canvas>
                  </div>
                </div>
              </div>
            </div>
         
             <!-- SECOND ROW -->
            <div class="row g-3 mt-3">                       

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <a href="{{ route('category.index') }}" class="text-decoration-none">
                <div class="card stat-card border-warning">
                   <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
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
                   <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
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
                   <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
                  <div class="card-body">
                    <p>Total Users</p>
                    <h3 class="text-warning">{{ $UserCount }}</h3>
                  </div>
                </div>
                </a>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card stat-card">
                  <div class="position-absolute top-0 start-0 w-100"
                    style="height:4px; background:gray;"></div>
                  <div class="card-body">
                    <p>Total Batches</p>
                    <h3 class="text-warning">{{ $BatchCount }}</h3>
                  </div>
                </div>
              </div>

            </div>

            <!-- low stock bar chart -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-3">Warehouse-wise Low Stock</h5>

                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="warehouseBarChart"></canvas>
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
  
  <script>
  document.addEventListener('DOMContentLoaded', function () {

      new Chart(document.getElementById('stockUtilizationChart'), {
          type: 'doughnut',
          data: {
              labels: ['Used', 'Available'],
              datasets: [{
                  data: [
                      {{ $usedStock }},
                      {{ max($totalStock - $usedStock, 0) }}
                  ],
                  backgroundColor: ['red', '#198754'],
                  borderWidth: 0
              }]
          },
          options: {
              cutout: '70%',
              plugins: {
                  legend: { display: false }
              },
              responsive: true
          }
      });

  });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('inOutChart').getContext('2d');
    const inOutChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($trendLabels),
            datasets: [
                {
                    label: 'IN',
                    data: @json($trendIn),
                    backgroundColor: '#198754'
                },
                {
                    label: 'OUT',
                    data: @json($trendOut),
                    backgroundColor: 'red'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    stacked: false,
                    beginAtZero: true
                }
            }
        }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
  document.addEventListener('DOMContentLoaded', function () {

      const ctx = document.getElementById('warehouseBarChart');

      const warehouseLabels = [
          @foreach($warehouseWise as $row)
              "{{ $row->warehouse->name ?? 'N/A' }}",
          @endforeach
      ];

      const lowStockData = [
          @foreach($warehouseWise as $row)
              {{ $row->total }},
          @endforeach
      ];

      new Chart(ctx, {
          type: 'bar',
          data: {
              labels: warehouseLabels,
              datasets: [{
                  label: 'Low Stock Count',
                  data: lowStockData,
                  backgroundColor: 'red',
                  borderRadius: 6,
                  barThickness: 28
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  legend: { display: false },
                  tooltip: {
                      callbacks: {
                          label: function(context) {
                              return ' Low Stock: ' + context.raw;
                          }
                      }
                  }
              },
              scales: {
                  x: {
                      ticks: {
                          autoSkip: false,
                          maxRotation: 45,
                          minRotation: 30
                      },
                      grid: { display: false }
                  },
                  y: {
                      beginAtZero: true,
                      grid: { color: '#f1f1f1' }
                  }
              }
          }
      });

  });
  </script>


</body>

</html>