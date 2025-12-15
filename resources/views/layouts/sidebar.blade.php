@include('layouts.header')
<div class="app-brand demo">
    <a href="index.html" class="app-brand-link">
        <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}" alt="Samruddhi Kirana" height="70px" width="300px">
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
    </a>
</div>

<div class="menu-divider mt-0"></div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item">
        <a href="{{route('dashboard')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-smile"></i>
            <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
        </a>
    </li>

    <!-- Apps & Pages -->
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Menu</span>
    </li>
    <li class="menu-item">
        <a
            href="{{route('category.index')}}"
            class="menu-link">
            <i class="menu-icon tf-icons bx bx-category"></i>
            <div class="text-truncate" data-i18n="Category">Category</div>

        </a>
    </li>
    <li class="menu-item">
        <a
            href=""
            target="_blank"
            class="menu-link">
            <i class="menu-icon tf-icons bx bx-package"></i>
            <div class="text-truncate" data-i18n="Product">Product</div>

        </a>
    </li>

</ul>