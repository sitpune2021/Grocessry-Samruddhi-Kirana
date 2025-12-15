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

<style>
    .sidebar {
        width: 220px;
    }

    .menu-title {
        font-weight: bold;
        cursor: pointer;
        padding: 8px;
        background: #f3f3f3;
    }

    .submenu li {
        padding: 5px 0;
    }

    .submenu a {
        text-decoration: none;
    }

   .submenu {
    list-style: none;
    padding-left: 15px;
    display: none;   /* 🔴 IMPORTANT */
}

</style>

<div class="sidebar">

    <ul style="list-style:none;padding:0;">

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
                href="{{route('warehouse.index')}}"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div class="text-truncate" data-i18n="Warehouse">Warehouse</div>

            </a>
        </li>
        
        <li>
            <div class="menu-title" onclick="toggleInventory()">
                📦 Inventory <span id="arrow">▸</span>
            </div>

            <ul class="submenu" id="inventoryMenu">
                <li><a href="{{route('category.index')}}">Category</a></li>
                <li><a href="{{route('product.index')}}">Products</a></li>
                <li><a href="{{route('batches.index')}}">Batch Management</a></li>
                <li>
                    @foreach($products as $product)
                        <a href="{{ route('sale.create', ['product' => $product->id]) }}">
                            Sell Management
                        </a>
                    @endforeach
                </li>
                <li><a href="/expiry-alerts">Expiry Alerts</a></li>
            </ul>
        </li>
        

    </ul>

</div>

<script>
    function toggleInventory() {
        const menu = document.getElementById('inventoryMenu');
        const arrow = document.getElementById('arrow');

        if (menu.style.display === 'block') {
            menu.style.display = 'none';
            arrow.innerHTML = '▸';
        } else {
            menu.style.display = 'block';
            arrow.innerHTML = '▾';
        }
    }
</script>
