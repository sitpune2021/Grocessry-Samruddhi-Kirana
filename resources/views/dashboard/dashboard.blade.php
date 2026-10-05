@include('layouts.header')

<style>
  /* =========================================================
   DASHBOARD STAT CARDS
========================================================= */

.dashboard-stat-row {
    width: 100%;
    margin: 0;
}


/* =========================================================
   MAIN CARD
========================================================= */

.stat-card {
    position: relative;
    height: 100%;
    min-height: 125px;

    overflow: hidden;

    border: 1px solid #e9ecef !important;
    border-radius: 14px !important;

    background: #ffffff;

    box-shadow:
        0 3px 12px rgba(0, 0, 0, 0.05);

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;

    box-sizing: border-box;
}


/* =========================================================
   HOVER EFFECT
========================================================= */

.stat-card:hover {
    transform: translateY(-5px);

    box-shadow:
        0 10px 25px rgba(0, 0, 0, 0.10);
}


/* =========================================================
   TOP COLOR STRIP
========================================================= */

.stat-card-strip {
    position: absolute;

    top: 0;
    left: 0;

    width: 100%;
    height: 4px;

    transition: height 0.25s ease;
}

.stat-card:hover .stat-card-strip {
    height: 5px;
}


/* =========================================================
   CARD BODY
========================================================= */

.stat-card .card-body {
    position: relative;

    min-height: 125px;

    padding: 20px;

    display: flex;
    align-items: center;

    box-sizing: border-box;
}


/* =========================================================
   CONTENT
========================================================= */

.stat-card-content {
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;
}


/* =========================================================
   INFORMATION
========================================================= */

.stat-card-info {
    min-width: 0;
    flex: 1;
}

.stat-card-title {
    margin: 0 0 7px 0;

    color: #6c757d;

    font-size: 13px;
    font-weight: 600;

    line-height: 1.4;
}

.stat-card-number {
    margin: 0 0 4px 0;

    font-size: 28px;
    font-weight: 700;

    line-height: 1.1;

    letter-spacing: -0.5px;
}

.stat-card-subtitle {
    display: block;

    margin: 0;

    color: #8a9299;

    font-size: 11px;
    font-weight: 500;

    line-height: 1.4;
}

.success-text {
    color: #198754;
}


/* =========================================================
   ICON
========================================================= */

.stat-card-icon {
    flex-shrink: 0;

    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    font-size: 21px;

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.stat-card:hover .stat-card-icon {
    transform: scale(1.08) rotate(-3deg);
}


/* =========================================================
   WARNING
========================================================= */

.stat-card-warning {
    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #fffaf0 100%
        );
}

.stat-card-warning .stat-card-strip {
    background: #ffc107;
}

.stat-card-warning .stat-card-number {
    color: #d39e00;
}

.stat-card-warning .stat-card-icon {
    background: #fff3cd;
    color: #d39e00;
}


/* =========================================================
   DANGER
========================================================= */

.stat-card-danger {
    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #fff6f6 100%
        );
}

.stat-card-danger .stat-card-strip {
    background: #dc3545;
}

.stat-card-danger .stat-card-number {
    color: #dc3545;
}

.stat-card-danger .stat-card-icon {
    background: #f8d7da;
    color: #dc3545;
}


/* =========================================================
   PRIMARY
========================================================= */

.stat-card-primary {
    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #f5f8ff 100%
        );
}

.stat-card-primary .stat-card-strip {
    background: #0d6efd;
}

.stat-card-primary .stat-card-number {
    color: #0d6efd;
}

.stat-card-primary .stat-card-icon {
    background: #e7f0ff;
    color: #0d6efd;
}


/* =========================================================
   SUCCESS
========================================================= */

.stat-card-success {
    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #f3fbf7 100%
        );
}

.stat-card-success .stat-card-strip {
    background: #198754;
}

.stat-card-success .stat-card-number {
    color: #198754;
}

.stat-card-success .stat-card-icon {
    background: #dff3e8;
    color: #198754;
}


/* =========================================================
   INFO
========================================================= */

.stat-card-info {
    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #f3faff 100%
        );
}

.stat-card-info .stat-card-strip {
    background: #0dcaf0;
}

.stat-card-info .stat-card-number {
    color: #0b8fa8;
}

.stat-card-info .stat-card-icon {
    background: #dff7fc;
    color: #0b8fa8;
}


/* =========================================================
   DISPATCH
========================================================= */

.stat-card-dispatch {
    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #fff5f5 100%
        );
}

.stat-card-dispatch .stat-card-strip {
    background: #dc3545;
}

.stat-card-dispatch .stat-card-number {
    color: #dc3545;
}

.stat-card-dispatch .stat-card-icon {
    background: #f8d7da;
    color: #dc3545;
}


/* =========================================================
   CLICKABLE CARD
========================================================= */

.dashboard-stat-row a {
    display: block;
    height: 100%;
}

.dashboard-stat-row a:hover {
    color: inherit;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 991px) {

    .stat-card {
        min-height: 120px;
    }

    .stat-card .card-body {
        min-height: 120px;
        padding: 17px;
    }

    .stat-card-number {
        font-size: 25px;
    }

    .stat-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 11px;
        font-size: 19px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 576px) {

    .dashboard-stat-row {
        margin-left: 0;
        margin-right: 0;
    }

    .stat-card {
        min-height: 112px;

        border-radius: 12px !important;
    }

    .stat-card .card-body {
        min-height: 112px;
        padding: 15px;
    }

    .stat-card-title {
        font-size: 12px;
    }

    .stat-card-number {
        font-size: 23px;
    }

    .stat-card-subtitle {
        font-size: 10px;
    }

    .stat-card-icon {
        width: 41px;
        height: 41px;

        border-radius: 10px;

        font-size: 18px;
    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 360px) {

    .stat-card .card-body {
        padding: 13px;
    }

    .stat-card-title {
        font-size: 11px;
    }

    .stat-card-number {
        font-size: 21px;
    }

    .stat-card-subtitle {
        font-size: 9px;
    }

    .stat-card-icon {
        width: 37px;
        height: 37px;

        border-radius: 9px;

        font-size: 16px;
    }

}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

    <div class="floating-leaves" aria-hidden="true">
        <span>🌿</span>
        <span>🍃</span>
        <span>🌿</span>
        <span>🍃</span>
    </div>

<style>
    .floating-leaves {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
}

.floating-leaves span {
    position: absolute;
    font-size: 24px;
    opacity: .12;
    animation: leafFloat 12s linear infinite;
}

.floating-leaves span:nth-child(1) {
    left: 8%;
    animation-delay: 0s;
}

.floating-leaves span:nth-child(2) {
    left: 35%;
    animation-delay: -4s;
}

.floating-leaves span:nth-child(3) {
    left: 65%;
    animation-delay: -8s;
}

.floating-leaves span:nth-child(4) {
    left: 90%;
    animation-delay: -2s;
}

@keyframes leafFloat {
    0% {
        transform: translateY(110vh) rotate(0deg);
    }

    50% {
        transform: translateY(50vh) translateX(40px) rotate(180deg);
    }

    100% {
        transform: translateY(-10vh) translateX(-30px) rotate(360deg);
    }
}
.stat-card {
    overflow: hidden;
}

.stat-card::after {
    content: "";

    position: absolute;

    top: 0;
    left: -120%;

    width: 70%;
    height: 100%;

    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,.45),
        transparent
    );

    transform: skewX(-20deg);

    transition: left .8s ease;

    pointer-events: none;
}

.stat-card:hover::after {
    left: 130%;
}

</style>

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
            <!-- <div class="row g-3">

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

            </div>            -->


            <!-- =========================================================
              DASHBOARD STAT CARDS
            ========================================================= -->

            <div class="row g-3 dashboard-stat-row">

                <!-- Pending Transfer Requests -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <a href="{{ route('warehouse.transfer.index') }}"
                      class="text-decoration-none">

                        <div class="card stat-card stat-card-warning">

                            <div class="stat-card-strip"></div>

                            <div class="card-body">

                                <div class="stat-card-content">

                                    <div class="stat-card-info">

                                        <p class="stat-card-title">
                                            Pending Transfer Requests
                                        </p>

                                        <h3 class="stat-card-number">
                                            {{ $pendingTransferCount }}
                                        </h3>

                                        <small class="stat-card-subtitle">
                                            Warehouse → Warehouse
                                        </small>

                                    </div>

                                    <div class="stat-card-icon">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </a>
                </div>


                <!-- Expired Batches -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">

                    <a href="{{ route('batches.expiry') }}"
                      class="text-decoration-none">

                        <div class="card stat-card stat-card-danger">

                            <div class="stat-card-strip"></div>

                            <div class="card-body">

                                <div class="stat-card-content">

                                    <div class="stat-card-info">

                                        <p class="stat-card-title">
                                            Expired Batches
                                        </p>

                                        <h3 class="stat-card-number">
                                            {{ $expiredCount }}
                                        </h3>

                                        <small class="stat-card-subtitle success-text">
                                            Expiring in 7 days:
                                            {{ $expiringSoonCount }}
                                        </small>

                                    </div>

                                    <div class="stat-card-icon">
                                        <i class="bi bi-calendar-x"></i>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>


                <!-- Total Warehouses -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">

                    <div class="card stat-card stat-card-primary">

                        <div class="stat-card-strip"></div>

                        <div class="card-body">

                            <div class="stat-card-content">

                                <div class="stat-card-info">

                                    <p class="stat-card-title">
                                        Total Warehouses
                                    </p>

                                    <h3 class="stat-card-number">
                                        {{ $WarehouseCount }}
                                    </h3>

                                </div>

                                <div class="stat-card-icon">
                                    <i class="bi bi-building"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Total Stock -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">

                    <div class="card stat-card stat-card-success">

                        <div class="stat-card-strip"></div>

                        <div class="card-body">

                            <div class="stat-card-content">

                                <div class="stat-card-info">

                                    <p class="stat-card-title">
                                        Total Stock
                                    </p>

                                    <h3 class="stat-card-number">
                                        {{ $StockMovementCount }}
                                    </h3>

                                </div>

                                <div class="stat-card-icon">
                                    <i class="bi bi-box-seam"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Warehouse Transfers -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">

                    <div class="card stat-card stat-card-info">

                        <div class="stat-card-strip"></div>

                        <div class="card-body">

                            <div class="stat-card-content">

                                <div class="stat-card-info">

                                    <p class="stat-card-title">
                                        Warehouse Transfers
                                    </p>

                                    <h3 class="stat-card-number">
                                        {{ $WarehouseTransferCount }}
                                    </h3>

                                </div>

                                <div class="stat-card-icon">
                                    <i class="bi bi-truck"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Today Dispatch -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">

                    <div class="card stat-card stat-card-dispatch">

                        <div class="stat-card-strip"></div>

                        <div class="card-body">

                            <div class="stat-card-content">

                                <div class="stat-card-info">

                                    <p class="stat-card-title">
                                        Today Dispatch
                                    </p>

                                    <h3 class="stat-card-number">
                                        {{ $todayDispatchCount }}
                                    </h3>

                                    <small class="stat-card-subtitle">
                                        Qty: {{ $todayDispatchQty }}
                                    </small>

                                </div>

                                <div class="stat-card-icon">
                                    <i class="bi bi-box-arrow-up"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- WAREHOUSE LIST + STOCK UTILIZATION -->
            <div class="row g-3 mt-4 warehouse-dashboard">

                <!-- Left: Warehouse List -->
                <div class="col-12 col-lg-7">
                    <div class="card warehouse-ui-card h-100">

                        <div class="card-body warehouse-card-body">

                            <div class="warehouse-heading">
                                <div>
                                    <h5 class="warehouse-title">
                                        All Warehouses
                                    </h5>

                                    <p class="warehouse-subtitle">
                                        Available warehouse locations
                                    </p>
                                </div>

                                <div class="warehouse-heading-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                            </div>


                            <div class="warehouse-scroll">

                                <ul class="list-group list-group-flush">

                                    @forelse($warehouseDistrict as $warehouse)

                                    <li class="list-group-item warehouse-list-item">

                                        <div class="warehouse-item-left">

                                            <span class="warehouse-item-icon">
                                                <i class="bi bi-building"></i>
                                            </span>

                                            <span class="warehouse-item-name">
                                                {{ $warehouse }}
                                            </span>

                                        </div>

                                        <i class="bi bi-chevron-right warehouse-item-arrow"></i>

                                    </li>

                                    @empty

                                    <li class="list-group-item warehouse-empty">
                                        <i class="bi bi-building-x"></i>
                                        <span>No Warehouses Found</span>
                                    </li>

                                    @endforelse

                                </ul>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right: Stock Utilization -->
                <div class="col-12 col-lg-5">

                    <div class="card warehouse-ui-card h-100">

                        <div class="card-body stock-card-body">

                            <div class="stock-heading">

                                <div>
                                    <h6 class="stock-title">
                                        Stock Utilization
                                    </h6>

                                    <p class="stock-subtitle">
                                        Current storage usage
                                    </p>
                                </div>

                                <div class="stock-heading-icon">
                                    <i class="bi bi-pie-chart"></i>
                                </div>

                            </div>


                            <div class="stock-chart-wrapper">
                                <canvas id="stockUtilizationChart"></canvas>
                            </div>


                            <strong class="stock-value">
                                {{ $stockUtilization }}%
                                <span>Used</span>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

            <style>

            /* =========================================================
            MAIN
            ========================================================= */

            .warehouse-dashboard {
                width: 100%;
            }


            /* =========================================================
            CARD
            ========================================================= */

            .warehouse-ui-card {
                border: 1px solid #e8eee9 !important;
                border-radius: 14px !important;

                background: #ffffff;

                box-shadow:
                    0 4px 18px rgba(0, 0, 0, 0.035);

                overflow: hidden;

                transition:
                    transform 0.2s ease,
                    box-shadow 0.2s ease;
            }

            .warehouse-ui-card:hover {
                box-shadow:
                    0 7px 24px rgba(25, 135, 84, 0.08);
            }

            .warehouse-card-body,
            .stock-card-body {
                padding: 20px;
            }


            /* =========================================================
            WAREHOUSE HEADER
            ========================================================= */

            .warehouse-heading,
            .stock-heading {
                display: flex;
                align-items: center;
                justify-content: space-between;

                gap: 15px;

                margin-bottom: 16px;
            }

            .warehouse-title,
            .stock-title {
                margin: 0;

                color: #202a24;

                font-size: 16px;
                font-weight: 700;
            }

            .warehouse-subtitle,
            .stock-subtitle {
                margin: 4px 0 0;

                color: #8a948e;

                font-size: 12px;
            }


            /* =========================================================
            HEADER ICON
            ========================================================= */

            .warehouse-heading-icon,
            .stock-heading-icon {
                width: 38px;
                height: 38px;

                flex: 0 0 38px;

                display: flex;
                align-items: center;
                justify-content: center;

                border-radius: 10px;

                background: #eaf7f0;
                color: #198754;

                font-size: 17px;
            }


            /* =========================================================
            WAREHOUSE SCROLL
            ========================================================= */

            .warehouse-scroll {
                width: 100%;

                max-height: 300px;

                overflow-y: auto;
                overflow-x: hidden;

                padding-right: 3px;

                scrollbar-width: thin;
                scrollbar-color: #cbd8d0 transparent;
            }

            .warehouse-scroll::-webkit-scrollbar {
                width: 5px;
            }

            .warehouse-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .warehouse-scroll::-webkit-scrollbar-thumb {
                background: #cbd8d0;
                border-radius: 10px;
            }

            .warehouse-scroll::-webkit-scrollbar-thumb:hover {
                background: #198754;
            }


            /* =========================================================
            WAREHOUSE ITEM
            ========================================================= */

            .warehouse-list-item {
                display: flex !important;

                align-items: center;
                justify-content: space-between;

                gap: 12px;

                min-height: 50px;

                padding: 8px 10px !important;

                border: 0 !important;
                border-bottom: 1px solid #edf2ef !important;

                background: transparent !important;

                transition: all 0.18s ease;
            }

            .warehouse-list-item:last-child {
                border-bottom: 0 !important;
            }

            .warehouse-list-item:hover {
                padding-left: 14px !important;

                background: #f7fbf8 !important;
            }


            /* =========================================================
            ITEM LEFT
            ========================================================= */

            .warehouse-item-left {
                display: flex;

                align-items: center;

                gap: 10px;

                min-width: 0;
            }

            .warehouse-item-icon {
                width: 32px;
                height: 32px;

                min-width: 32px;

                display: flex;

                align-items: center;
                justify-content: center;

                border-radius: 8px;

                background: #eef8f2;
                color: #198754;

                font-size: 14px;
            }

            .warehouse-item-name {
                min-width: 0;

                color: #465149;

                font-size: 13px;
                font-weight: 600;

                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }


            /* =========================================================
            ARROW
            ========================================================= */

            .warehouse-item-arrow {
                flex: 0 0 auto;

                color: #a3ada7;

                font-size: 13px;

                transition: all 0.18s ease;
            }

            .warehouse-list-item:hover .warehouse-item-arrow {
                color: #198754;

                transform: translateX(3px);
            }


            /* =========================================================
            EMPTY
            ========================================================= */

            .warehouse-empty {
                min-height: 130px;

                display: flex !important;

                align-items: center;
                justify-content: center;

                gap: 8px;

                border: 0 !important;

                color: #8a948e;

                font-size: 13px;
            }

            .warehouse-empty i {
                color: #198754;

                font-size: 20px;
            }


            /* =========================================================
            STOCK CHART
            ========================================================= */

            .stock-card-body {
                display: flex;

                flex-direction: column;
            }

            .stock-chart-wrapper {
                position: relative;

                width: 100%;

                height: 190px;

                margin: 0 auto;
            }

            .stock-chart-wrapper canvas {
                width: 100% !important;
                height: 100% !important;
            }


            /* =========================================================
            STOCK VALUE
            ========================================================= */

            .stock-value {
                display: flex;

                align-items: baseline;
                justify-content: center;

                gap: 5px;

                margin-top: 8px;

                color: #198754;

                font-size: 24px;
                font-weight: 800;
            }

            .stock-value span {
                color: #7d8781;

                font-size: 12px;
                font-weight: 600;
            }


            /* =========================================================
            TABLET
            ========================================================= */

            @media (max-width: 991px) {

                .warehouse-card-body,
                .stock-card-body {
                    padding: 18px;
                }

                .warehouse-scroll {
                    max-height: 270px;
                }

                .stock-chart-wrapper {
                    height: 210px;
                }
            }


            /* =========================================================
            MOBILE
            ========================================================= */

            @media (max-width: 767px) {

                .warehouse-dashboard {
                    margin-top: 16px !important;
                }

                .warehouse-card-body,
                .stock-card-body {
                    padding: 15px;
                }

                .warehouse-title,
                .stock-title {
                    font-size: 15px;
                }

                .warehouse-subtitle,
                .stock-subtitle {
                    font-size: 11px;
                }

                .warehouse-heading-icon,
                .stock-heading-icon {
                    width: 34px;
                    height: 34px;

                    flex-basis: 34px;

                    font-size: 15px;
                }

                .warehouse-scroll {
                    max-height: 240px;
                }

                .warehouse-list-item {
                    min-height: 46px;

                    padding: 7px 6px !important;
                }

                .warehouse-item-icon {
                    width: 30px;
                    height: 30px;

                    min-width: 30px;

                    font-size: 13px;
                }

                .warehouse-item-name {
                    font-size: 12.5px;
                }

                .stock-chart-wrapper {
                    height: 190px;

                    max-width: 280px;
                }

                .stock-value {
                    font-size: 22px;
                }
            }


            /* =========================================================
            SMALL MOBILE
            ========================================================= */

            @media (max-width: 480px) {

                .warehouse-card-body,
                .stock-card-body {
                    padding: 13px;
                }

                .warehouse-heading,
                .stock-heading {
                    margin-bottom: 12px;
                }

                .warehouse-title,
                .stock-title {
                    font-size: 14px;
                }

                .warehouse-subtitle,
                .stock-subtitle {
                    font-size: 10.5px;
                }

                .warehouse-scroll {
                    max-height: 220px;
                }

                .warehouse-item-name {
                    font-size: 12px;
                }

                .warehouse-item-arrow {
                    font-size: 12px;
                }

                .stock-chart-wrapper {
                    height: 175px;

                    max-width: 250px;
                }

                .stock-value {
                    font-size: 21px;
                }
            }

            </style>


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