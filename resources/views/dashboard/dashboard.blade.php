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
        @include('layouts.navbar')

        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">

              <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
                <div class="row">
                  <div class="col-lg-6 col-md-12 col-6 mb-6">
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


                  <div class="col-lg-6 col-md-12 col-6 mb-6">
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

                  @if($expiredCount > 0 || $expiringSoonCount > 0)
                  <script>
                    document.addEventListener('DOMContentLoaded', function() {
                      let modal = new bootstrap.Modal(
                        document.getElementById('expiryAlertModal')
                      );
                      modal.show();
                    });
                  </script>
                  @endif
                </div>
              </div>
              <!-- Expiry Alert Modal -->
              @if($expiredCount > 0 || $expiringSoonCount > 0)
              <div class="expiry-toast-container">
                <div class="alert alert-danger shadow d-flex align-items-start justify-content-between gap-3" role="alert">

                  <!-- Left Content -->
                  <div>
                    <h6 class="alert-heading mb-1">
                      <i class="bx bx-error-circle me-1"></i>
                      Expiry Alert
                    </h6>

                    @if($expiredCount > 0)
                    <div class="small">
                      <i class="bx bx-x-circle me-1"></i>
                      <strong>{{ $expiredCount }}</strong> expired batch(es)
                    </div>
                    @endif

                    @if($expiringSoonCount > 0)
                    <div class="small text-warning">
                      <i class="bx bx-time-five me-1"></i>
                      <strong>{{ $expiringSoonCount }}</strong> expiring in 7 days
                    </div>
                    @endif
                  </div>

                  <!-- Right Actions -->
                  <div class="text-end">
                    <a href="{{ route('batches.expiry') }}" class="btn btn-sm btn-danger mb-1">
                      View
                    </a>
                    <button type="button"
                      class="btn-close ms-2"
                      data-bs-dismiss="alert"
                      aria-label="Close"></button>
                  </div>

                </div>
              </div>
              @endif


              <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
                <div class="row">
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
              <!-- <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
                <div class="row">
                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        </div>
                        <p class="mb-1">Profit</p>
                        <h4 class="card-title mb-3">$12,628</h4>
                        <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +72.80%</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        </div>
                        <p class="mb-1">Profit</p>
                        <h4 class="card-title mb-3">$12,628</h4>
                        <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +72.80%</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        </div>
                        <p class="mb-1">Profit</p>
                        <h4 class="card-title mb-3">$12,628</h4>
                        <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +72.80%</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        </div>
                        <p class="mb-1">Profit</p>
                        <h4 class="card-title mb-3">$12,628</h4>
                        <small class="text-success fw-medium"><i class="icon-base bx bx-up-arrow-alt"></i> +72.80%</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->
              <!-- Total Revenue -->
              <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6 total-revenue">
                <div class="card">
                  <div class="row row-bordered g-0">
                    <div class="col-lg-8">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-title mb-0">
                          <h5 class="m-0 me-2">Total Revenue</h5>
                        </div>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="totalRevenue"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalRevenue">
                            <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                            <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                            <a class="dropdown-item" href="javascript:void(0);">Share</a>
                          </div>
                        </div>
                      </div>
                      <div id="totalRevenueChart" class="px-3"></div>
                    </div>
                    <div class="col-lg-4">
                      <div class="card-body px-xl-9 py-12 d-flex align-items-center flex-column">
                        <div class="text-center mb-6">
                          <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary">
                              <script>
                                document.write(new Date().getFullYear() - 1);
                              </script>
                            </button>
                            <button
                              type="button"
                              class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                              data-bs-toggle="dropdown"
                              aria-expanded="false">
                              <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="javascript:void(0);">2021</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">2020</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">2019</a></li>
                            </ul>
                          </div>
                        </div>

                        <div id="growthChart"></div>
                        <div class="text-center fw-medium my-6">62% Company Growth</div>

                        <div class="d-flex gap-11 justify-content-between">
                          <div class="d-flex">
                            <div class="avatar me-2">
                              <span class="avatar-initial rounded-2 bg-label-primary"><i class="icon-base bx bx-dollar icon-lg text-primary"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                              <small>
                                <script>
                                  document.write(new Date().getFullYear() - 1);
                                </script>
                              </small>
                              <h6 class="mb-0">$32.5k</h6>
                            </div>
                          </div>
                          <div class="d-flex">
                            <div class="avatar me-2">
                              <span class="avatar-initial rounded-2 bg-label-info"><i class="icon-base bx bx-wallet icon-lg text-info"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                              <small>
                                <script>
                                  document.write(new Date().getFullYear() - 2);
                                </script>
                              </small>
                              <h6 class="mb-0">$41.2k</h6>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Total Revenue -->
              <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-3 order-md-2 profile-report">
                <div class="row">
                  <div class="col-6 mb-6 payments">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="card-title align-items-start justify-content-between mb-4"> District Warehouse
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
                  <div class="col-6 mb-6 transactions">
                    <div class="card h-100">
                       <div class="card-body">
                        <div class="card-title align-items-start justify-content-between mb-4"> Taluka Warehouse
                        </div>
                        <div class="warehouse-list">
                          <ul class="list-group">
                            @foreach($warehouseTaluka as $warehouse)
                            <li class="list-group-item">{{ $warehouse }}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 mb-6 transactions">
                    <div class="card h-100">
                       <div class="card-body">
                        <div class="card-title align-items-start justify-content-between mb-4"> Shop
                        </div>
                        <div class="warehouse-list">
                          <ul class="list-group">
                            @foreach($shops as $shop)
                            <li class="list-group-item">{{ $shop }}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <!-- Order Statistics -->
              <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
                <div class="card h-100">
                  <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                      <h5 class="mb-1 me-2">Order Statistics</h5>
                      <p class="card-subtitle">42.82k Total Sales</p>
                    </div>
                    <div class="dropdown">
                      <button
                        class="btn text-body-secondary p-0"
                        type="button"
                        id="orederStatistics"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                        <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                        <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                      <div class="d-flex flex-column align-items-center gap-1">
                        <h3 class="mb-1">8,258</h3>
                        <small>Total Orders</small>
                      </div>
                      <div id="orderStatisticsChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                      <li class="d-flex align-items-center mb-5">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-mobile-alt"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Electronic</h6>
                            <small>Mobile, Earbuds, TV</small>
                          </div>
                          <div class="user-progress">
                            <h6 class="mb-0">82.5k</h6>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center mb-5">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-closet"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Fashion</h6>
                            <small>T-shirt, Jeans, Shoes</small>
                          </div>
                          <div class="user-progress">
                            <h6 class="mb-0">23.8k</h6>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center mb-5">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-home-alt"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Decor</h6>
                            <small>Fine Art, Dining</small>
                          </div>
                          <div class="user-progress">
                            <h6 class="mb-0">849k</h6>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-secondary"><i class="icon-base bx bx-football"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Sports</h6>
                            <small>Football, Cricket Kit</small>
                          </div>
                          <div class="user-progress">
                            <h6 class="mb-0">99</h6>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <!--/ Order Statistics -->

              <!-- Expense Overview -->
              <div class="col-md-6 col-lg-4 order-1 mb-6">
                <div class="card h-100">
                  <div class="card-header nav-align-top">
                    <ul class="nav nav-pills flex-wrap row-gap-2" role="tablist">
                      <li class="nav-item">
                        <button
                          type="button"
                          class="nav-link active"
                          role="tab"
                          data-bs-toggle="tab"
                          data-bs-target="#navs-tabs-line-card-income"
                          aria-controls="navs-tabs-line-card-income"
                          aria-selected="true">
                          Income
                        </button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab">Expenses</button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab">Profit</button>
                      </li>
                    </ul>
                  </div>
                  <div class="card-body">
                    <div class="tab-content p-0">
                      <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
                        <div class="d-flex mb-6">
                          <div class="avatar flex-shrink-0 me-3">
                            <img src="../assets/img/icons/unicons/wallet.png" alt="User" />
                          </div>
                          <div>
                            <p class="mb-0">Total Balance</p>
                            <div class="d-flex align-items-center">
                              <h6 class="mb-0 me-1">$459.10</h6>
                              <small class="text-success fw-medium">
                                <i class="icon-base bx bx-chevron-up icon-lg"></i>
                                42.9%
                              </small>
                            </div>
                          </div>
                        </div>
                        <div id="incomeChart"></div>
                        <div class="d-flex align-items-center justify-content-center mt-6 gap-3">
                          <div class="flex-shrink-0">
                            <div id="expensesOfWeek"></div>
                          </div>
                          <div>
                            <h6 class="mb-0">Income this week</h6>
                            <small>$39k less than last week</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Expense Overview -->

              <!-- Transactions -->
              <div class="col-md-6 col-lg-4 order-2 mb-6">
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Transactions</h5>
                    <div class="dropdown">
                      <button
                        class="btn text-body-secondary p-0"
                        type="button"
                        id="transactionID"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                        <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body pt-4">
                    <ul class="p-0 m-0">
                      <li class="d-flex align-items-center mb-6">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="../assets/img/icons/unicons/paypal.png" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <small class="d-block">Paypal</small>
                            <h6 class="fw-normal mb-0">Send money</h6>
                          </div>
                          <div class="user-progress d-flex align-items-center gap-2">
                            <h6 class="fw-normal mb-0">+82.6</h6>
                            <span class="text-body-secondary">USD</span>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center mb-6">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="../assets/img/icons/unicons/wallet.png" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <small class="d-block">Wallet</small>
                            <h6 class="fw-normal mb-0">Mac'D</h6>
                          </div>
                          <div class="user-progress d-flex align-items-center gap-2">
                            <h6 class="fw-normal mb-0">+270.69</h6>
                            <span class="text-body-secondary">USD</span>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center mb-6">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="../assets/img/icons/unicons/chart.png" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <small class="d-block">Transfer</small>
                            <h6 class="fw-normal mb-0">Refund</h6>
                          </div>
                          <div class="user-progress d-flex align-items-center gap-2">
                            <h6 class="fw-normal mb-0">+637.91</h6>
                            <span class="text-body-secondary">USD</span>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center mb-6">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="../assets/img/icons/unicons/cc-primary.png" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <small class="d-block">Credit Card</small>
                            <h6 class="fw-normal mb-0">Ordered Food</h6>
                          </div>
                          <div class="user-progress d-flex align-items-center gap-2">
                            <h6 class="fw-normal mb-0">-838.71</h6>
                            <span class="text-body-secondary">USD</span>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center mb-6">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="../assets/img/icons/unicons/wallet.png" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <small class="d-block">Wallet</small>
                            <h6 class="fw-normal mb-0">Starbucks</h6>
                          </div>
                          <div class="user-progress d-flex align-items-center gap-2">
                            <h6 class="fw-normal mb-0">+203.33</h6>
                            <span class="text-body-secondary">USD</span>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                          <img src="../assets/img/icons/unicons/cc-warning.png" alt="User" class="rounded" />
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <small class="d-block">Mastercard</small>
                            <h6 class="fw-normal mb-0">Ordered Food</h6>
                          </div>
                          <div class="user-progress d-flex align-items-center gap-2">
                            <h6 class="fw-normal mb-0">-92.45</h6>
                            <span class="text-body-secondary">USD</span>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <!--/ Transactions -->
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