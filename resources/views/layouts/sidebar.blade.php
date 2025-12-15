@include('layouts.header')

<!-- Sidebar Logo -->
<div class="app-brand d-flex align-items-center justify-content-center bg-secondary py-3">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
        <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
            alt="Samruddhi Kirana"
            class="img-fluid"
            style="max-height:100px; max-width:300px;">
    </a>

    <a href="javascript:void(0);"
        class="layout-menu-toggle menu-link text-black ms-auto d-xl-none">
        <i class="bx bx-chevron-left align-middle"></i>
    </a>
</div>

<div class="menu-divider m-0 border-secondary"></div>

<div class="menu-inner-shadow"></div>

<!-- Menu -->
<ul class="menu-inner py-2 bg-secondary">

    <!-- Dashboard -->
    <li class="menu-item">
        <a href="{{ route('dashboard') }}"
            class="menu-link d-flex align-items-center text-black">
            <i class="menu-icon tf-icons bx bx-home-smile text-warning"></i>
            <span class="ms-2">Dashboard</span>
        </a>
    </li>

    <!-- Menu Header -->
    <li class="menu-header small text-uppercase text-secondary px-3 mt-2">
        Menu
    </li>

    <!-- Category -->
    <li class="menu-item">
        <a href="{{ route('category.index') }}"
            class="menu-link d-flex align-items-center text-black">
            <i class="menu-icon tf-icons bx bx-category text-warning"></i>
            <span class="ms-2">Category</span>
        </a>
    </li>

    <!-- Product -->
    <li class="menu-item">
        <a href="{{ route('product.index') }}"
            class="menu-link d-flex align-items-center text-black">
            <i class="menu-icon tf-icons bx bx-package text-warning"></i>
            <span class="ms-2">Product</span>
        </a>
    </li>

    <!-- Warehouse -->
    <li class="menu-item">
        <a href="{{ route('warehouse.index') }}"
            class="menu-link d-flex align-items-center text-black">
            <i class="menu-icon tf-icons bx bx-store text-warning"></i>
            <span class="ms-2">Warehouse</span>
        </a>
    </li>

</ul>